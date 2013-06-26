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

require_once 'modules/ModuleBuilder/parsers/ModuleBuilderParser.php';
require_once 'modules/Administration/Common.php';
require_once 'include/MetaDataManager/MetaDataManager.php';

class ParserDropDown extends ModuleBuilderParser {

    /**
     * Takes in the request params from a save request and processes
     * them for the save.
     *
     * @param REQUEST params  $params
     */
    function saveDropDown($params){
        global $locale;
        
		$emptyMarker = translate('LBL_BLANK');
		
        if (!empty($_REQUEST['dropdown_lang'])) {
            $selected_lang = $_REQUEST['dropdown_lang'];
        } else {
            $selected_lang = $locale->getAuthenticatedUserLanguage();
        }
        
		$type = $_REQUEST['view_package'];
		$dir = '';
		$dropdown_name = $params['dropdown_name'];
		$json = getJSONobj();

		$list_value = str_replace('&quot;&quot;:&quot;&quot;', '&quot;__empty__&quot;:&quot;&quot;', $params['list_value']);
		//Bug 21362 ENT_QUOTES- convert single quotes to escaped single quotes.
		$temp = $json->decode(html_entity_decode(rawurldecode($list_value), ENT_QUOTES) );
		$dropdown = array () ;
		// dropdown is received as an array of (name,value) pairs - now extract to name=>value format preserving order
		// we rely here on PHP to preserve the order of the received name=>value pairs - associative arrays in PHP are ordered
        if(is_array($temp))
        {
            foreach ( $temp as $item )
            {
                $dropdown[ SugarCleaner::stripTags(from_html($item [ 0 ]), false) ] = SugarCleaner::stripTags(from_html($item [ 1 ]), false) ;
            }
        }
		if(array_key_exists($emptyMarker, $dropdown)){
            $output=array();
            foreach($dropdown as $key => $value){
                if($emptyMarker===$key)
                    $output['']='';
                else
                    $output[$key]=$value;
		}
            $dropdown=$output;
		}

		if($type != 'studio'){
			$mb = new ModuleBuilder();
			$module = $mb->getPackageModule($params['view_package'], $params['view_module']);
			$this->synchMBDropDown($dropdown_name, $dropdown, $selected_lang, $module);
			//Can't use synch on selected lang as we want to overwrite values, not just keys
			$module->mblanguage->appListStrings[$selected_lang.'.lang.php'][$dropdown_name] = $dropdown;
			$module->mblanguage->save($module->key_name); // tyoung - key is required parameter as of
		}else{
			$contents = return_custom_app_list_strings_file_contents($selected_lang);
			$my_list_strings = return_app_list_strings_language($selected_lang);
			if($selected_lang == $GLOBALS['current_language']){
	           $GLOBALS['app_list_strings'][$dropdown_name] = $dropdown;
	        }
			//write to contents
			$contents = str_replace("?>", '', $contents);
			if(empty($contents))$contents = "<?php";
	        //add new drop down to the bottom
	        if(!empty($params['use_push'])){
	        	//this is for handling moduleList and such where nothing should be deleted or anything but they can be renamed
	        	foreach($dropdown as $key=>$value){
	        		//only if the value has changed or does not exist do we want to add it this way
	        		if(!isset($my_list_strings[$dropdown_name][$key]) || strcmp($my_list_strings[$dropdown_name][$key], $value) != 0 ){
		        		//clear out the old value
		        		$pattern_match = '/\s*\$app_list_strings\s*\[\s*\''.$dropdown_name.'\'\s*\]\[\s*\''.$key.'\'\s*\]\s*=\s*[\'\"]{1}.*?[\'\"]{1};\s*/ism';
		        		$contents = preg_replace($pattern_match, "\n", $contents);
		        		//add the new ones without using GLOBALS
		        		$contents .= "\n\$app_list_strings['$dropdown_name']['$key']=" . var_export_helper($value) . ";";
	        		}
	        	}
	        }else{
	        	//Now synch up the keys in other langauges to ensure that removed/added Drop down values work properly under all langs.
	        	$this->synchDropDown($dropdown_name, $dropdown, $selected_lang, $dir);
	        	$contents = $this->getNewCustomContents($dropdown_name, $dropdown, $selected_lang);
	        }
		    if(!empty($dir) && !is_dir($dir))
		    {
		     	$continue = mkdir_recursive($dir);
		    }
			save_custom_app_list_strings_contents($contents, $selected_lang, $dir);
		}
		sugar_cache_reset();
		clearAllJsAndJsLangFilesWithoutOutput();
        
        // Clear out the api metadata cache
        MetaDataManager::clearAPICache();
    }

    /**
	 * function synchDropDown
	 * 	Ensures that the set of dropdown keys is consistant accross all languages.
	 *
	 * @param $dropdown_name The name of the dropdown to be synched
	 * @param $dropdown array The dropdown currently being saved
	 * @param $selected_lang String the language currently selected in Studio/MB
	 * @param $saveLov String the path to the directory to save the new lang file in.
	 */
    function synchDropDown($dropdown_name, $dropdown, $selected_lang, $saveLoc) {
   		$allLanguages =  get_languages();
        foreach ($allLanguages as $lang => $langName) {
        	if ($lang != $selected_lang) {
        		$listStrings = return_app_list_strings_language($lang);
        		$langDropDown = array();
        		if (isset($listStrings[$dropdown_name]) && is_array($listStrings[$dropdown_name]))
        		{
        		 	$langDropDown = $this->synchDDKeys($dropdown, $listStrings[$dropdown_name]);
        		} else
        		{
        			//if the dropdown does not exist in the language, justt use what we have.
        			$langDropDown = $dropdown;
        		}
        		$contents = $this->getNewCustomContents($dropdown_name, $langDropDown, $lang);
        		save_custom_app_list_strings_contents($contents, $lang, $saveLoc);
        	}
        }
    }

    /**
	 * function synchMBDropDown
	 * 	Ensures that the set of dropdown keys is consistant accross all languages in a ModuleBuilder Module
	 *
	 * @param $dropdown_name The name of the dropdown to be synched
	 * @param $dropdown array The dropdown currently being saved
	 * @param $selected_lang String the language currently selected in Studio/MB
	 * @param $module MBModule the module to update the languages in
	 */
    function synchMBDropDown($dropdown_name, $dropdown, $selected_lang, $module) {
    	$selected_lang	= $selected_lang . '.lang.php';
		foreach($module->mblanguage->appListStrings as $lang => $listStrings) {
			if ($lang != $selected_lang)
			{
				$langDropDown = array();
				if (isset($listStrings[$dropdown_name]) && is_array($listStrings[$dropdown_name]))
				{
					$langDropDown = $this->synchDDKeys($dropdown, $listStrings[$dropdown_name]);
				} else
        		{
        			$langDropDown = $dropdown;
        		}
        		$module->mblanguage->appListStrings[$lang][$dropdown_name] = $langDropDown;
				$module->mblanguage->save($module->key_name);
			}
		}
    }

    private function synchDDKeys($dom, $sub) {
    	//check for extra keys
        foreach($sub as $key=>$value) {
        	if (!isset($dom[$key])) {
        		unset ($sub[$key]);
        	}
        }
        //check for missing keys
        foreach($dom as $key=>$value) {
        	if (!isset($sub[$key])) {
        		$sub[$key] = $value;
        	}
        }
        return $sub;
    }

    function getPatternMatch($dropdown_name) {
        // Change the regex to NOT look for GLOBALS anymore
    	return '/\s*\$app_list_strings\s*\[\s*\''
    		 . $dropdown_name.'\'\s*\]\s*=\s*array\s*\([^\)]*\)\s*;\s*/ism';
    }

    /**
     * Gets the new custom dropdown list file contents after replacement
     * 
     * @param string $dropdown_name
     * @param array $dropdown
     * @param string $lang
     * @return string
     */
    function getNewCustomContents($dropdown_name, $dropdown, $lang) {
    	$contents = return_custom_app_list_strings_file_contents($lang);
        $contents = str_replace("?>", '', $contents);
		if(empty($contents))$contents = "<?php";
    	$contents = preg_replace($this->getPatternMatch($dropdown_name), "\n\n", $contents);
	    $contents .= "\n\n\$app_list_strings['$dropdown_name']=" . var_export_helper($dropdown) . ";";
	    return $contents;
    }
}