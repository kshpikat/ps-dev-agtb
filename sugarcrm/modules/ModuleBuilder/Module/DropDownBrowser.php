<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

class DropDownBrowser
{
    // Restrict the full dropdown list to remove some options that shouldn't be edited by the end users
    public static $restrictedDropdowns = array(
        'eapm_list',
        'eapm_list_documents',
        'eapm_list_import',
        'extapi_meeting_password',
        'Elastic_boost_options',
        //BEGIN SUGARCRM flav=pro ONLY
        'commit_stage_dom',
        'commit_stage_custom_dom',
        'commit_stage_binary_dom',
        'forecasts_config_ranges_options_dom',
        'forecasts_timeperiod_types_dom',
        'forecast_schedule_status_dom',
        'forecasts_chart_options_group',
        'forecasts_config_worksheet_layout_forecast_by_options_dom',
        'forecasts_timeperiod_options_dom',
        //END SUGARCRM flav=pro ONLY
        // 'moduleList', // We may want to put this in at a later date
        // 'moduleListSingular', // Same with this
    );

    function getNodes()
    {
	    global $mod_strings, $app_list_strings;
		$nodes = array();
//      $nodes[$mod_strings['LBL_EDIT_DROPDOWNS']] = array( 'name'=>$mod_strings['LBL_EDIT_DROPDOWNS'], 'action' =>'module=ModuleBuilder&action=globaldropdown&view_package=studio', 'imageTitle' => 'SPUploadCSS', 'help' => 'editDropDownBtn');
   //     $nodes[$mod_strings['LBL_ADD_DROPDOWN']] = array( 'name'=>$mod_strings['LBL_ADD_DROPDOWN'], 'action'=>'module=ModuleBuilder&action=globaldropdown&view_package=studio','imageTitle' => 'SPSync', 'help' => 'addDropDownBtn');
        
        $my_list_strings = $app_list_strings;
        foreach($my_list_strings as $key=>$value){
            if (!is_array($value) || array_filter($value, 'is_array')) {
        		unset($my_list_strings[$key]);
        	}
        }

        foreach ( self::$restrictedDropdowns as $restrictedDropdown ) {
            unset($my_list_strings[$restrictedDropdown]);
        }

        $dropdowns = array_keys($my_list_strings);
        asort($dropdowns);
        foreach($dropdowns as $dd)
        {
            if (!empty($dd))
            {
                $nodes[$dd] = array( 'name'=>$dd, 'action'=>"module=ModuleBuilder&action=dropdown&view_package=studio&dropdown_name=$dd",'imageTitle' => 'SPSync', 'help' => 'editDropDownBtn');
            }
        }
        return $nodes;
    }

}
?>
