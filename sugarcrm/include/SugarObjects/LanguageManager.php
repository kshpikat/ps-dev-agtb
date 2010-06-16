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
class LanguageManager{
	
	/**
	 * Called from VardefManager to allow for caching a lang file for a module
	 * @param module - the name of the module we are working with
	 * @param templates - an array of templates this module uses
	 */
	function createLanguageFile($module , $templates=array('default'), $refresh = false){
		global $mod_strings, $current_language;
		if(!empty($GLOBALS['sugar_config']['developerMode']) || !empty($_SESSION['developerMode'])){
        	$refresh = true;
    	}
		$temp_mod_strings = $mod_strings;
		$lang = $current_language;
        if(empty($lang))
            $lang = $GLOBALS['sugar_config']['default_language'];
		static $createdModules = array();
		if(empty($createdModules[$module]) && ($refresh || !file_exists($GLOBALS['sugar_config']['cache_dir'].'modules/'.$module.'/language/'.$lang.'.lang.php'))){
			$loaded_mod_strings = array();
			$loaded_mod_strings = LanguageManager::loadTemplateLanguage($module , $templates, $lang , $loaded_mod_strings);
			$createdModules[$module] = true;
			LanguageManager::refreshLanguage($module,$lang, $loaded_mod_strings);	
		}
	}
	
	/**
	 * Load the module  tempalte lauguage files 
	 * @param module - the name of the module we are working with
	 * @param templates - an array of templates this module uses
	 * @param lang - current language this module use
	 * @param loaded_mod_strings - the string that we will add the module template language  into
	 */
	function loadTemplateLanguage($module , $templates , $lang, $loaded_mod_strings){
		$templates = array_reverse($templates);
		foreach($templates as $template){
			$temp = LanguageManager::addTemplate($module,$lang, $template);
			$loaded_mod_strings = sugarArrayMerge($loaded_mod_strings, $temp);
		}
		return $loaded_mod_strings;
	}
	
	function addTemplate($module, $lang, $template){
		if($template == 'default')$template = 'basic';
		$templates = array();
		$fields = array();
		if(empty($templates[$template])){
			$path = 'include/SugarObjects/templates/' . $template . '/language/'.$lang.'.lang.php';
			if(file_exists($path)){
				require($path);
				$templates[$template] = $mod_strings;
			}else{
				$path = 'include/SugarObjects/implements/' . $template . '/language/'.$lang.'.lang.php';
				if(file_exists($path)){
					require($path);
					$templates[$template] = $mod_strings;
				}
			}
		}
		if(!empty($templates[$template])){
			return $templates[$template];	
		}
	}
	
	function saveCache($module,$lang, $loaded_mod_strings, $additonal_objects= array()){
		if(empty($lang))
			$lang = $GLOBALS['sugar_config']['default_language'];
		
		$file = create_cache_directory('modules/' . $module . '/language/'.$lang.'.lang.php');
		write_array_to_file('mod_strings',$loaded_mod_strings, $file);
		include($file);
		
		// put the item in the sugar cache.
		$key = "LanguageManager.$module.$lang";
		sugar_cache_put($key,$loaded_mod_strings);
	}
	
	/**
	 * clear out the language cache. 
	 * @param string module_dir the module_dir to clear, if not specified then clear
	 *                      clear language cache for all modules.
	 * @param string lang the name of the object we are clearing this is for sugar_cache
	 */
	function clearLanguageCache($module_dir = '', $lang = ''){
		if(empty($lang)) {
			$languages = array_keys($GLOBALS['sugar_config']['languages']);
		} else {
			$languages = array($lang);
		}
		//if we have a module name specified then just remove that language file
		//otherwise go through each module and clean up the language
		if(!empty($module_dir)) {
			foreach($languages as $clean_lang) {
				LanguageManager::_clearCache($module_dir, $clean_lang);
			}
		} else {
			$cache_dir = $GLOBALS['sugar_config']['cache_dir'].'modules/';
			if(file_exists($cache_dir) && $dir = @opendir($cache_dir)) {
				while(($entry = readdir($dir)) !== false) {
					if ($entry == "." || $entry == "..") continue;
						foreach($languages as $clean_lang) {
							LanguageManager::_clearCache($entry, $clean_lang);
						}
				}
				closedir($dir);
			}
		}
	}
	
	/**
	 * PRIVATE function used within clearLanguageCache so we do not repeat logic
	 * @param string module_dir the module_dir to clear
	 * @param string lang the name of the language file we are clearing this is for sugar_cache
	 */
	function _clearCache($module_dir = '', $lang){
		if(!empty($module_dir) && !empty($lang)){
			$file = $GLOBALS['sugar_config']['cache_dir'].'modules/'.$module_dir.'/language/'.$lang.'.lang.php';
			if(file_exists($file)){
				unlink($file);
				$key = "LanguageManager.$module_dir.$lang";
				sugar_cache_clear($key);
			}
		}
	}
	
	/**
	 * Given a module, search all of the specified locations, and any others as specified
	 * in order to refresh the cache file
	 * 
	 * @param string $module the given module we want to load the vardefs for
	 * @param string $lang the given language we wish to load
	 * @param array $additional_search_paths an array which allows a consumer to pass in additional vardef locations to search
	 */
	function refreshLanguage($module, $lang, $loaded_mod_strings = array(), $additional_search_paths = null){
		// Some of the vardefs do not correctly define dictionary as global.  Declare it first.
		$lang_paths = array(
					'modules/'.$module.'/language/'.$lang.'.lang.php',
					'modules/'.$module.'/language/'.$lang.'.lang.override.php',
					'custom/modules/'.$module.'/Ext/Language/'.$lang.'.lang.ext.php',
					'custom/modules/'.$module.'/language/'.$lang.'.lang.php',
				 );

		#27023, if this module template language file was not attached , get the template from this module vardef cache file if exsits and load the template language files.
		static $createdModules;
		if(empty($createdModules[$module]) && isset($GLOBALS['beanList'][$module])){
				$object = $GLOBALS['beanList'][$module];
				
				//BEGIN SUGARCRM flav!=sales ONLY
				if ($object == 'aCase')
		            $object = 'Case';
		        //END SUGARCRM flav!=sales ONLY
		        
		        if(!empty($GLOBALS["dictionary"]["$object"]["templates"])){
		        	$templates = $GLOBALS["dictionary"]["$object"]["templates"];					
					$loaded_mod_strings = LanguageManager::loadTemplateLanguage($module , $templates, $lang , $loaded_mod_strings);
					$createdModules[$module] = true;		
		        }	
		}
		//end of fix #27023
		
		// Add in additional search paths if they were provided.
		if(!empty($additional_search_paths) && is_array($additional_search_paths))
		{
			$lang_paths = array_merge($lang_paths, $additional_search_paths);
		}
		
		//search a predefined set of locations for the vardef files
		foreach($lang_paths as $path){
			if(file_exists($path)){
				require($path);
				if(!empty($mod_strings)){
					if (function_exists('sugarArrayMergeRecursive')){
						$loaded_mod_strings = sugarArrayMergeRecursive($loaded_mod_strings, $mod_strings);
					}
					else{
						$loaded_mod_strings = sugarArrayMerge($loaded_mod_strings, $mod_strings);
					}	
				}
			}
		}
		
		//great! now that we have loaded all of our vardefs.
		//let's go save them to the cache file.
		if(!empty($loaded_mod_strings))
			LanguageManager::saveCache($module, $lang, $loaded_mod_strings);
	}
	
	function loadModuleLanguage($module, $lang, $refresh=false){
		//here check if the cache file exists, if it does then load it, if it doesn't
		//then call refreshVardef
		//if either our session or the system is set to developerMode then refresh is set to true
		
		// Retrieve the vardefs from cache.
		$key = "LanguageManager.$module.$lang";
		
		if(!$refresh)
		{
			$return_result = sugar_cache_retrieve($key);		
			if(!empty($return_result)){
				return $return_result;
			}
		}
		        
		// Some of the vardefs do not correctly define dictionary as global.  Declare it first.
		if($refresh || !file_exists($GLOBALS['sugar_config']['cache_dir'].'modules/'.$module.'/language/'.$lang.'.lang.php')){
			LanguageManager::refreshLanguage($module, $lang);
		}
			
		//at this point we should have the cache/modules/... file
		//which was created from the refreshVardefs so let's try to load it.
		if(file_exists($GLOBALS['sugar_config']['cache_dir'].'modules/'. $module . '/language/'.$lang.'.lang.php')){
			global $mod_strings;

			require($GLOBALS['sugar_config']['cache_dir'].'modules/'. $module . '/language/'.$lang.'.lang.php');
				
			// now that we hae loaded the data from disk, put it in the cache.
			if(!empty($mod_strings))
				sugar_cache_put($key,$mod_strings);
			if(!empty($_SESSION['translation_mode'])){
				$mod_strings = array_map('translated_prefix', $mod_strings);			
			}
			return $mod_strings;
		}
    	////BEGIN SUGARCRM flav=int ONLY
    	else{
    		display_notice('<B> MISSING FIELD_DEFS ' . 'modules/'. strtoupper($module) . '/language/'.$lang.'.lang.php </b><BR>');
    	}
    		//END SUGARCRM flav=int ONLY
	}
}

function translated_prefix($key){
	return '[translated]' . $key;
}
