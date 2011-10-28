<?php
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
 ********************************************************************************/
require_once('service/v3/SugarWebServiceUtilv3.php');
class SugarWebServiceUtilv3_1 extends SugarWebServiceUtilv3
{

    function get_return_module_fields($value, $module,$fields, $translate=true)
    {
		$GLOBALS['log']->info('Begin: SoapHelperWebServices->get_return_module_fields');
		global $module_name;
		$module_name = $module;
		$result = $this->get_field_list($value,$fields,  $translate);
		$GLOBALS['log']->info('End: SoapHelperWebServices->get_return_module_fields');

		$tableName = $value->getTableName();

		return Array('module_name'=>$module, 'table_name' => $tableName,
					'module_fields'=> $result['module_fields'],
					'link_fields'=> $result['link_fields'],
					);
	} // fn


    /**
	 * Track a view for a particular bean.
	 *
	 * @param SugarBean $seed
	 * @param string $current_view
	 */
    function trackView($seed, $current_view)
    {
        $trackerManager = TrackerManager::getInstance();
		if($monitor = $trackerManager->getMonitor('tracker'))
		{
			//BEGIN SUGARCRM flav=pro ONLY
	        $monitor->setValue('team_id', $GLOBALS['current_user']->getPrivateTeamID());
			//END SUGARCRM flav=pro ONLY
	        $monitor->setValue('date_modified', TimeDate::getInstance()->nowDb());
	        $monitor->setValue('user_id', $GLOBALS['current_user']->id);
	        $monitor->setValue('module_name', $seed->module_dir);
	        $monitor->setValue('action', $current_view);
	        $monitor->setValue('item_id', $seed->id);
	        $monitor->setValue('item_summary', $seed->get_summary_text());
	        $monitor->setValue('visible',true);
	        $trackerManager->saveMonitor($monitor, TRUE, TRUE);
		}
    }

    /**
     * Convert modules list to Web services result
     *
     * @param array $list List of module candidates (only keys are used)
     * @param array $availModules List of module availability from Session
     */
    public function getModulesFromList($list, $availModules)
    {
        global $app_list_strings;
        $enabled_modules = array();
        $availModulesKey = array_flip($availModules);
        foreach ($list as $key=>$value)
        {
            if( isset($availModulesKey[$key]) )
            {
                $label = !empty( $app_list_strings['moduleList'][$key] ) ? $app_list_strings['moduleList'][$key] : '';
        	    $acl = self::checkModuleRoleAccess($key);
        	    $enabled_modules[] = array('module_key' => $key,'module_label' => $label, 'acls' => $acl);
            }
        }
        return $enabled_modules;
    }

    /**
     * Examine the wireless_module_registry to determine which modules have been enabled for the mobile view.
     *
     * @param array $availModules An array of all the modules the user already has access to.
     * @return array Modules enalbed for mobile view.
     */
    function get_visible_mobile_modules($availModules)
    {
        foreach ( array ( '','custom/') as $prefix)
        {
        	if(file_exists($prefix.'include/MVC/Controller/wireless_module_registry.php'))
        		require $prefix.'include/MVC/Controller/wireless_module_registry.php' ;
        }
        return $this->getModulesFromList($wireless_module_registry, $availModules);
    }

    /**
     * Examine the application to determine which modules have been enabled..
     *
     * @param array $availModules An array of all the modules the user already has access to.
     * @return array Modules enabled within the application.
     */
    function get_visible_modules($availModules)
    {
        require_once("modules/MySettings/TabController.php");
        $controller = new TabController();
        $tabs = $controller->get_tabs_system();
        return $this->getModulesFromList($tabs[0], $availModules);
    }

    /**
     * Generate unifed search fields for a particular module even if the module does not participate in the unified search.
     *
     * @param string $moduleName
     * @return array An array of fields to be searched against.
     */
    function generateUnifiedSearchFields($moduleName)
    {
        global $beanList, $beanFiles, $dictionary;

        if(!isset($beanList[$moduleName]))
            return array();

        $beanName = $beanList[$moduleName];

        if (!isset($beanFiles[$beanName]))
            return array();

        $beanName = BeanFactory::getObjectName($moduleName);

        $manager = new VardefManager ( );
        $manager->loadVardef( $moduleName , $beanName ) ;

        // obtain the field definitions used by generateSearchWhere (duplicate code in view.list.php)
        if(file_exists('custom/modules/'.$moduleName.'/metadata/metafiles.php')){
            require('custom/modules/'.$moduleName.'/metadata/metafiles.php');
        }elseif(file_exists('modules/'.$moduleName.'/metadata/metafiles.php')){
            require('modules/'.$moduleName.'/metadata/metafiles.php');
        }

        if(!empty($metafiles[$moduleName]['searchfields']))
            require $metafiles[$moduleName]['searchfields'] ;
        elseif(file_exists("modules/{$moduleName}/metadata/SearchFields.php"))
            require "modules/{$moduleName}/metadata/SearchFields.php" ;

        $fields = array();
        foreach ( $dictionary [ $beanName ][ 'fields' ] as $field => $def )
        {
            if (strpos($field,'email') !== false)
                $field = 'email' ;

            //bug: 38139 - allow phone to be searched through Global Search
            if (strpos($field,'phone') !== false)
                $field = 'phone' ;

            if ( isset($def['unified_search']) && $def['unified_search'] && isset ( $searchFields [ $moduleName ] [ $field ]  ))
            {
                    $fields [ $field ] = $searchFields [ $moduleName ] [ $field ] ;
            }
        }

        //If no fields with the unified flag have been set then lets add a default field.
        if( empty($fields) )
        {
            if( isset($dictionary[$beanName]['fields']['name']) && isset($searchFields[$moduleName]['name'])  )
                $fields['name'] = $searchFields[$moduleName]['name'];
            else
            {
                if( isset($dictionary[$beanName]['fields']['first_name']) && isset($searchFields[$moduleName]['first_name']) )
                    $fields['first_name'] = $searchFields[$moduleName]['first_name'];
                if( isset($dictionary[$beanName]['fields']['last_name']) && isset($searchFields[$moduleName]['last_name'])  )
                    $fields['last_name'] = $searchFields[$moduleName]['last_name'];
            }
        }

		return $fields;
    }

    /**
     * Check a module for acces to a set of available actions.
     *
     * @param string $module
     * @return array results containing access and boolean indicating access
     */
    function checkModuleRoleAccess($module)
    {
        $results = array();
        $actions = array('edit','delete','list','view','import','export');
        foreach ($actions as $action)
        {
            $access = ACLController::checkAccess($module, $action, true);
            $results[] = array('action' => $action, 'access' => $access);
        }

        return $results;
    }

    function get_field_list($value,$fields,  $translate=true) {

	    $GLOBALS['log']->info('Begin: SoapHelperWebServices->get_field_list');
		$module_fields = array();
		$link_fields = array();
		if(!empty($value->field_defs)){

			foreach($value->field_defs as $var){
				if(!empty($fields) && !in_array( $var['name'], $fields))continue;
				if(isset($var['source']) && ($var['source'] != 'db' && $var['source'] != 'non-db' &&$var['source'] != 'custom_fields') && $var['name'] != 'email1' && $var['name'] != 'email2' && (!isset($var['type'])|| $var['type'] != 'relate'))continue;
				if ((isset($var['source']) && $var['source'] == 'non_db') && (isset($var['type']) && $var['type'] != 'link')) {
					continue;
				}
				$required = 0;
				$options_dom = array();
				$options_ret = array();
				// Apparently the only purpose of this check is to make sure we only return fields
				//   when we've read a record.  Otherwise this function is identical to get_module_field_list
				if( isset($var['required']) && ($var['required'] || $var['required'] == 'true' ) ){
					$required = 1;
				}

				if($var['type'] == 'bool')
				    $var['options'] = 'checkbox_dom';

				if(isset($var['options'])){
					$options_dom = translate($var['options'], $value->module_dir);
					if(!is_array($options_dom)) $options_dom = array();
					foreach($options_dom as $key=>$oneOption)
						$options_ret[$key] = $this->get_name_value($key,$oneOption);
				}

	            if(!empty($var['dbType']) && $var['type'] == 'bool') {
	                $options_ret['type'] = $this->get_name_value('type', $var['dbType']);
	            }

	            $entry = array();
	            $entry['name'] = $var['name'];
	            $entry['type'] = $var['type'];
	            $entry['group'] = isset($var['group']) ? $var['group'] : '';
	            $entry['id_name'] = isset($var['id_name']) ? $var['id_name'] : '';

	            if ($var['type'] == 'link') {
		            $entry['relationship'] = (isset($var['relationship']) ? $var['relationship'] : '');
		            $entry['module'] = (isset($var['module']) ? $var['module'] : '');
		            $entry['bean_name'] = (isset($var['bean_name']) ? $var['bean_name'] : '');
					$link_fields[$var['name']] = $entry;
	            } else {
		            if($translate) {
		            	$entry['label'] = isset($var['vname']) ? translate($var['vname'], $value->module_dir) : $var['name'];
		            } else {
		            	$entry['label'] = isset($var['vname']) ? $var['vname'] : $var['name'];
		            }
		            $entry['required'] = $required;
		            $entry['options'] = $options_ret;
		            $entry['related_module'] = (isset($var['id_name']) && isset($var['module'])) ? $var['module'] : '';
		            $entry['calculated'] =  (isset($var['calculated']) && $var['calculated']) ? true : false;
					if(isset($var['default'])) {
					   $entry['default_value'] = $var['default'];
					}
					if( $var['type'] == 'parent' && isset($var['type_name']) )
					   $entry['type_name'] = $var['type_name'];

					$module_fields[$var['name']] = $entry;
	            } // else
			} //foreach
		} //if

		if($value->module_dir == 'Meetings' || $value->module_dir == 'Calls')
		{
		    if( isset($module_fields['duration_minutes']) && isset($GLOBALS['app_list_strings']['duration_intervals']))
		    {
		        $options_dom = $GLOBALS['app_list_strings']['duration_intervals'];
		        $options_ret = array();
		        foreach($options_dom as $key=>$oneOption)
						$options_ret[$key] = $this->get_name_value($key,$oneOption);

		        $module_fields['duration_minutes']['options'] = $options_ret;
		    }
		}

		if($value->module_dir == 'Bugs'){
			require_once('modules/Releases/Release.php');
			$seedRelease = new Release();
			$options = $seedRelease->get_releases(TRUE, "Active");
			$options_ret = array();
			foreach($options as $name=>$value){
				$options_ret[] =  array('name'=> $name , 'value'=>$value);
			}
			if(isset($module_fields['fixed_in_release'])){
				$module_fields['fixed_in_release']['type'] = 'enum';
				$module_fields['fixed_in_release']['options'] = $options_ret;
			}
            if(isset($module_fields['found_in_release'])){
                $module_fields['found_in_release']['type'] = 'enum';
                $module_fields['found_in_release']['options'] = $options_ret;
            }
			if(isset($module_fields['release'])){
				$module_fields['release']['type'] = 'enum';
				$module_fields['release']['options'] = $options_ret;
			}
			if(isset($module_fields['release_name'])){
				$module_fields['release_name']['type'] = 'enum';
				$module_fields['release_name']['options'] = $options_ret;
			}
		}

		if(isset($value->assigned_user_name) && isset($module_fields['assigned_user_id'])) {
			$module_fields['assigned_user_name'] = $module_fields['assigned_user_id'];
			$module_fields['assigned_user_name']['name'] = 'assigned_user_name';
		}
		if(isset($value->assigned_name) && isset($module_fields['team_id'])) {
			$module_fields['team_name'] = $module_fields['team_id'];
			$module_fields['team_name']['name'] = 'team_name';
		}
		if(isset($module_fields['modified_user_id'])) {
			$module_fields['modified_by_name'] = $module_fields['modified_user_id'];
			$module_fields['modified_by_name']['name'] = 'modified_by_name';
		}
		if(isset($module_fields['created_by'])) {
			$module_fields['created_by_name'] = $module_fields['created_by'];
			$module_fields['created_by_name']['name'] = 'created_by_name';
		}

		$GLOBALS['log']->info('End: SoapHelperWebServices->get_field_list');
		return array('module_fields' => $module_fields, 'link_fields' => $link_fields);
	}

	/**
	 * Return the contents of a file base64 encoded
	 *
	 * @param string $filename - Full path of filename
	 * @param bool $remove - Indicates if the file should be removed after the contents is retrieved.
	 *
	 * @return string - Contents base64'd.
	 */
	function get_file_contents_base64($filename, $remove = FALSE)
	{
	    $contents = "";
	    if( file_exists($filename) )
	    {
	        $contents =  base64_encode(file_get_contents($filename));
	        if($remove)
    	        @unlink($filename);
	    }

	    return $contents;
	}

	function get_module_view_defs($module_name, $type, $view){
        require_once('include/MVC/View/SugarView.php');
        $metadataFile = null;
        $results = array();
        $view = strtolower($view);
        switch (strtolower($type)){
            case 'wireless':
                if( $view == 'list'){
                    require_once('include/SugarWireless/SugarWirelessListView.php');
                    $GLOBALS['module'] = $module_name; //WirelessView keys off global variable not instance variable...
                    $v = new SugarWirelessListView();
                    $results = $v->getMetaDataFile();
                }
                elseif ($view == 'subpanel')
                    $results = $this->get_subpanel_defs($module_name, $type);
                else{
                    require_once('include/SugarWireless/SugarWirelessView.php');
                    $v = new SugarWirelessView();
                    $v->module = $module_name;
                    $fullView = ucfirst($view) . 'View';
                    $meta = $v->getMetaDataFile('Wireless' . $fullView);
                    $metadataFile = $meta['filename'];
                    require($metadataFile);
                    //Wireless detail metadata may actually be just edit metadata.
                    $results = isset($viewdefs[$meta['module_name']][$fullView] ) ? $viewdefs[$meta['module_name']][$fullView] : $viewdefs[$meta['module_name']]['EditView'];
                }

                break;
            case 'default':
            default:
                if ($view == 'subpanel')
                    $results = $this->get_subpanel_defs($module_name, $type);
                else
                {
                    $v = new SugarView(null,array());
                    $v->module = $module_name;
                    $v->type = $view;
                    $fullView = ucfirst($view) . 'View';
                    $metadataFile = $v->getMetaDataFile();
                    require_once($metadataFile);
                    if($view == 'list')
                        $results = $listViewDefs[$module_name];
                    else
                        $results = $viewdefs[$module_name][$fullView];
                }
        }

        return $results;
    }

    /**
     * Equivalent of get_list function within SugarBean but allows the possibility to pass in an indicator
     * if the list should filter for favorites.  Should eventually update the SugarBean function as well.
     *
     */
    function get_data_list($seed, $order_by = "", $where = "", $row_offset = 0, $limit=-1, $max=-1, $show_deleted = 0, $favorites = false, $singleSelect=false)
	{
		$GLOBALS['log']->debug("get_list:  order_by = '$order_by' and where = '$where' and limit = '$limit'");
		if(isset($_SESSION['show_deleted']))
		{
			$show_deleted = 1;
		}
		$order_by=$seed->process_order_by($order_by, null);

		if($seed->bean_implements('ACL') && ACLController::requireOwner($seed->module_dir, 'list') )
		{
			global $current_user;
			$owner_where = $seed->getOwnerWhere($current_user->id);
			if(!empty($owner_where)){
				if(empty($where)){
					$where = $owner_where;
				}else{
					$where .= ' AND '.  $owner_where;
				}
			}
		}
		$params = array();
		if($favorites)
		  $params['favorites'] = true;

		$query = $seed->create_new_list_query($order_by, $where,array(),$params, $show_deleted,'',false,null,$singleSelect);
		return $seed->process_list_query($query, $row_offset, $limit, $max, $where);
	}

    /**
     * Add ACL values to metadata files.
     *
     * @param String $module_name
     * @param String $view_type (wireless or detail)
     * @param String $view  (list, detail,edit, etc)
     * @param array $metadata The metadata for the view type and view.
     * @return unknown
     */
	function addFieldLevelACLs($module_name,$view_type, $view, $metadata)
	{
	    $functionName = "metdataAclParser" . ucfirst($view_type) . ucfirst($view);
	    if( method_exists($this, $functionName) )
	       return $this->$functionName($module_name, $metadata);
	    else
	       return $metadata;
	}

	/**
	 * Parse wireless listview metadata and add ACL values.
	 *
	 * @param String $module_name
	 * @param array $metadata
	 * @return array Metadata with acls added
	 */
	function metdataAclParserWirelessList($module_name, $metadata)
	{
	    global  $beanList, $beanFiles;
	    $class_name = $beanList[$module_name];
	    require_once($beanFiles[$class_name]);
	    $seed = new $class_name();

	    $results = array();
	    foreach ($metadata as $field_name => $entry)
	    {
	        if($seed->bean_implements('ACL'))
	            $entry['acl'] = $this->getFieldLevelACLValue($seed->module_dir, strtolower($field_name));
	        else
	            $entry['acl'] = 99;

	        $results[$field_name] = $entry;
	    }

	    return $results;
	}

	/**
	 * Parse wireless detailview metadata and add ACL values.
	 *
	 * @param String $module_name
	 * @param array $metadata
	 * @return array Metadata with acls added
	 */
	function metdataAclParserWirelessEdit($module_name, $metadata)
	{
	    global  $beanList, $beanFiles;
	    $class_name = $beanList[$module_name];
	    require_once($beanFiles[$class_name]);
	    $seed = new $class_name();

	    $results = array();
	    $results['templateMeta'] = $metadata['templateMeta'];
	    $aclRows = array();
	    //Wireless metadata only has a single panel definition.
	    foreach ($metadata['panels'] as $row)
	    {
	        $aclRow = array();
	        foreach ($row as $field)
	        {
	            $aclField = array();
	            if( is_string($field) )
	                $aclField['name'] = $field;
	            else
	                $aclField = $field;

	            if($seed->bean_implements('ACL'))
	                $aclField['acl'] = $this->getFieldLevelACLValue($seed->module_dir, $aclField['name']);
	            else
	                $aclField['acl'] = 99;

	            $aclRow[] = $aclField;
	        }
	        $aclRows[] = $aclRow;
	    }

	    $results['panels'] = $aclRows;
	    return $results;
	}
	/**
	 * Return the field level acl raw value.  We cannot use the hasAccess call as we do not have a valid bean
	 * record at the moment and therefore can not specify the is_owner flag.  We need the raw access value so we
	 * can do the computation on the client side.  TODO: Move function into ACLField class.
	 *
	 * @param String $module Name of the module
	 * @param String $field Name of the field
	 * @return int
	 */
	function getFieldLevelACLValue($module, $field, $current_user = null)
	{
	    if($current_user == null)
	       $current_user = $GLOBALS['current_user'];

	    if( is_admin($current_user) )
	         return 99;

	    if(!isset($_SESSION['ACL'][$current_user->id][$module]['fields'][$field])){
			 return 99;
		}

		return $_SESSION['ACL'][$current_user->id][$module]['fields'][$field];
	}
}