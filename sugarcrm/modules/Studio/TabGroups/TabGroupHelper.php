<?php
//FILE SUGARCRM flav!=sales ONLY
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

 // $Id: TabGroupHelper.php 19402 2007-01-16 02:40:56Z clee $

require_once('modules/Administration/Common.php');
class TabGroupHelper{
    var $modules = array();
    function getAvailableModules($lang = ''){
       static $availableModules = array();
       if(!empty($availableModules))return $availableModules;
       $specifyLanguageAppListStrings = $GLOBALS['app_list_strings'];
       if(!empty($lang)){
       	$specifyLanguageAppListStrings = return_app_list_strings_language($lang);
       }
       foreach($GLOBALS['moduleList'] as $value){
           $availableModules[$value] = array('label'=>$specifyLanguageAppListStrings['moduleList'][$value], 'value'=>$value);
       }
       foreach($GLOBALS['modInvisListActivities'] as $value){
           $availableModules[$value] = array('label'=>$specifyLanguageAppListStrings['moduleList'][$value], 'value'=>$value);
       }
       
       if(should_hide_iframes() && isset($availableModules['iFrames'])) {
          unset($availableModules['iFrames']);
       }
       return $availableModules;
    }
    
    /**
     * Takes in the request params from a save request and processes 
     * them for the save.
     *
     * @param REQUEST params  $params
     */
    function saveTabGroups($params){
    	//#30205 
    	global $sugar_config;
		if (strcmp($params['other_group_tab_displayed'], '1') == 0) {
			$value = true;
		}else{
			$value = false;
		}
		if(!isset($sugar_config['other_group_tab_displayed']) || $sugar_config['other_group_tab_displayed'] != $value){
			require_once('modules/Configurator/Configurator.php');
    		$cfg = new Configurator();
    		$cfg->config['other_group_tab_displayed'] = $value;
    		$cfg->handleOverride();
		}

    	//Get the selected tab group language 
    	$grouptab_lang = (!empty($params['grouptab_lang'])?$params['grouptab_lang']:$_SESSION['authenticated_user_language']);  

    	$tabGroups = array();
		$selected_lang = (!empty($params['dropdown_lang'])?$params['dropdown_lang']:$_SESSION['authenticated_user_language']);    	
        $slot_count = $params['slot_count'];
		$completedIndexes = array();
        for($count = 0; $count < $slot_count; $count++){
        	if($params['delete_' . $count] == 1 || !isset($params['slot_' . $count])){
        		continue;	
        	}
        	
        	
        	$index = $params['slot_' . $count];
        	if (isset($completedIndexes[$index]))
        	   continue;
        	
        	$labelID = (!empty($params['tablabelid_' . $index]))?$params['tablabelid_' . $index]: 'LBL_GROUPTAB' . $count . '_'. time();
        	$labelValue = $params['tablabel_' . $index];
        	$appStirngs = return_application_language($grouptab_lang);
        	if(empty($appStirngs[$labelID]) || $appStirngs[$labelID] != $labelValue){
        		$contents = return_custom_app_list_strings_file_contents($grouptab_lang);
        		$new_contents = replace_or_add_app_string($labelID,$labelValue, $contents);
        		save_custom_app_list_strings_contents($new_contents, $grouptab_lang);
        		
        		$languages = get_languages();
        		foreach ($languages as $language => $langlabel) {
        			if($grouptab_lang == $language){
        				continue;
        			}
					$appStirngs = return_application_language($language);
		        	if(!isset($appStirngs[$labelID])){
        				$contents = return_custom_app_list_strings_file_contents($language);
		        		$new_contents = replace_or_add_app_string($labelID,$labelValue, $contents);
		        		save_custom_app_list_strings_contents($new_contents, $language);
		        	}
		        }
	        	
		        $app_strings[$labelID] = $labelValue;
        		
        	}
        	$tabGroups[$labelID] = array('label'=>$labelID);
        	$tabGroups[$labelID]['modules']= array();
        	for($subcount = 0; isset($params[$index.'_' . $subcount]); $subcount++){
        		$tabGroups[$labelID]['modules'][] = $params[$index.'_' . $subcount];
        	}
        	
        	$completedIndexes[$index] = true;
        	
    	} 
    	sugar_cache_put('app_strings', $GLOBALS['app_strings']);
     	$newFile = create_custom_directory('include/tabConfig.php');
     	write_array_to_file("GLOBALS['tabStructure']", $tabGroups, $newFile);
   		$GLOBALS['tabStructure'] = $tabGroups; 
   }
    
}


?>
