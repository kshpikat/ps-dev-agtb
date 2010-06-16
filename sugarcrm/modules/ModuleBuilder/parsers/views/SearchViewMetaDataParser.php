<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 * $Id: additionalDetails.php 13782 2006-06-06 17:58:55Z majed $
 *********************************************************************************/


require_once ('modules/ModuleBuilder/parsers/views/ListLayoutMetaDataParser.php') ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class SearchViewMetaDataParser extends ListLayoutMetaDataParser
{
    static $variableMap = array (
    						MB_BASICSEARCH => 'basic_search' ,
    						MB_ADVANCEDSEARCH => 'advanced_search' ,
    						//BEGIN SUGARCRM flav=pro || flav=sales ONLY
    						MB_WIRELESSBASICSEARCH => 'basic_search' ,
    						MB_WIRELESSADVANCEDSEARCH => 'advanced_search'
    						//END SUGARCRM flav=pro || flav=sales ONLY
    						) ;
    // Columns is used by the view to construct the listview - each column is built by calling the named function
    public $columns = array ( 'LBL_DEFAULT' => 'getDefaultFields' , 'LBL_HIDDEN' => 'getAvailableFields' ) ;
    protected $allowParent = true;

    /*
     * Constructor
     * Must set:
     * $this->columns   Array of 'Column LBL'=>function_to_retrieve_fields_for_this_column() - expected by the view
     * @param string searchLayout	The type of search layout, e.g., MB_BASICSEARCH or MB_ADVANCEDSEARCH
     * @param string moduleName     The name of the module to which this listview belongs
     * @param string packageName    If not empty, the name of the package to which this listview belongs
     */
    function __construct ($searchLayout, $moduleName , $packageName = '')
    {
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . ": __construct( $searchLayout , $moduleName , $packageName )" ) ;

        // BEGIN ASSERTIONS
        if (! isset ( self::$variableMap [ $searchLayout ] ) )
        {
            sugar_die ( get_class ( $this ) . ": View $searchLayout is not supported" ) ;
        }
        // END ASSERTIONS

        $this->_searchLayout = $searchLayout ;

        // unsophisticated error handling for now...
        try
        {
        	if (empty ( $packageName ))
        	{
            	require_once 'modules/ModuleBuilder/parsers/views/DeployedMetaDataImplementation.php' ;
            	$this->implementation = new DeployedMetaDataImplementation ( $searchLayout, $moduleName ) ;
        	} else
        	{
            	require_once 'modules/ModuleBuilder/parsers/views/UndeployedMetaDataImplementation.php' ;
            	$this->implementation = new UndeployedMetaDataImplementation ( $searchLayout, $moduleName, $packageName ) ;
        	}
        } catch (Exception $e)
        {
        	throw $e ;
        }

        $this->_saved = array_change_key_case ( $this->implementation->getViewdefs () ) ; // force to lower case so don't have problems with case mismatches later
        if(isset($this->_saved['templatemeta'])) {
            $this->_saved['templateMeta'] = $this->_saved['templatemeta'];
            unset($this->_saved['templatemeta']);
        }

        if ( ! isset ( $this->_saved [ 'layout' ] [ self::$variableMap [ $this->_searchLayout ] ] ) )
        {
        	// attempt to fallback on a basic_search layout...

        	if ( ! isset ( $this->_saved [ 'layout' ] [ self::$variableMap [ MB_BASICSEARCH ] ] ) )
        		throw new Exception ( get_class ( $this ) . ": {$this->_searchLayout} does not exist for module $moduleName" ) ;

        	$this->_saved [ 'layout'] [ MB_ADVANCEDSEARCH ] = $this->_saved [ 'layout' ] [ MB_BASICSEARCH ] ;
        }

        $this->view = $searchLayout;
        // convert the search view layout (which has its own unique layout form) to the standard listview layout so that the parser methods and views can be reused
        $this->_viewdefs = $this->convertSearchViewToListView ( $this->_saved [ 'layout' ] [ self::$variableMap [ $this->_searchLayout ] ] ) ;
        $this->_fielddefs = $this->implementation->getFielddefs () ;
        $this->_standardizeFieldLabels( $this->_fielddefs );

    }

    public function isValidField($key, $def)
    {
		
        if (!parent::isValidField($key, $def))
            return false;

        if (isset($def [ 'studio' ]) && is_array($def [ 'studio' ]) && isset($def [ 'studio' ]['searchview']))
        {
        	return $def [ 'studio' ]['searchview'] !== false && $def [ 'studio' ]['searchview'] != 'false';
        }
    	
        //Special case to prevent multiple copies of assigned user on the search view
        if (empty ($def[ 'studio' ] ) && $key == "assigned_user_name" )
        {
        	$origDefs = $this->getOriginalViewDefs();
        	if (isset($origDefs['assigned_user_id']))
        		return false;
        }

        //Remove image fields (unless studio was set)
        if (!empty($def [ 'studio' ]) && isset($def['type']) && $def['type'] == "image")
           return false;
        
       return true;
    }

    /*
     * Save the modified searchLayout
     * Have to preserve the original layout format, which is array('metadata'=>array,'layouts'=>array('basic'=>array,'advanced'=>array))
     */
    function handleSave ($populate = true)
    {
        if ($populate)
            $this->_populateFromRequest() ;
            
        //BEGIN SUGARCRM flav=pro ONLY
        if($this->_searchLayout == 'basic_search' && isset($this->_viewdefs['team_name'])) {
           $this->_viewdefs['team_name']['label'] = 'LBL_TEAM';  //Change to singular form label
        }
        //END SUGARCRM flav=pro ONLY
            
        $this->_saved [ 'layout' ] [ self::$variableMap [ $this->_searchLayout ] ] = $this->convertSearchViewToListView($this->_viewdefs);;
        $this->implementation->deploy ( $this->_saved ) ;
    }

    private function convertSearchViewToListView ($viewdefs)
    {
        $temp = array ( ) ;
        foreach ( $viewdefs as $key => $value )
        {
            if (! is_array ( $value ))
            {
                $key = $value ;
                $def = array ( ) ;
                $def[ 'name' ] = $key;
                $value = $def ;
            }

            if (!isset ( $value [ 'name' ] ))
            {
                $value [ 'name' ] = $key;
            }
            else
            {
                $key = $value [ 'name' ] ; // override key with name, needed when the entry lacks a key
            }
            // now add in the standard listview default=>true
            $value [ 'default' ] = true ;
            $temp [ strtolower ( $key ) ] = $value ;
        }
        return $temp ;
    }
    
    function getOriginalViewDefs() {
        $defs = $this->implementation->getOriginalViewdefs ();
        $out = array();
        if (!empty($defs) && !empty($defs['layout']) && !empty($defs['layout'][$this->_searchLayout]))
        {
        	$defs = $defs['layout'][$this->_searchLayout];
	        foreach ($defs as $def)
	        {
	            if (is_array($def) && isset($def['name']))
	            {
	            	$out[strtolower($def['name'])] = $def;
	            }
	        }
        }

        return $out;
    }
}
?>
