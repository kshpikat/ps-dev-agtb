<?php
//if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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

/**
 * Global search
 * @api
 */
class SugarSpot
{
	/**
     * searchAndDisplay
     *
	 * Performs the search and returns the HTML widget containing the results
	 *
	 * @param  $query string what we are searching for
	 * @param  $modules array modules we are searching in
	 * @param  $offset int search result offset
	 * @return string HTML code containing results
	 */
	public function searchAndDisplay($query, $modules, $offset=-1)
	{
        global $current_user;
		$query_encoded = urlencode($query);
	    $results = $this->_performSearch($query, $modules, $offset);
        $displayResults = array();
        $displayMoreForModule = array();

		foreach($results as $m=>$data)
        {
			if(empty($data['data']))
            {
				continue;
			}

            $total = count($data['data']);
			$countRemaining = $data['pageData']['offsets']['total'] - $total;

            if(isset($results[$m]['readAccess']) && !$results[$m]['readAccess'])
            {
               $displayTotal = $countRemaining > 0 ? ($total + $countRemaining) : $total;
               $displayResults[$m]['link'] = array('total'=>$displayTotal, 'count_remaining'=>$countRemaining, 'query_encoded'=>$query_encoded);
               continue;
            }

			if($offset > 0)
            {
                $countRemaining -= $offset;
            }

			if($countRemaining > 0)
            {
                $displayMoreForModule[$m] = array('query'=>$query,
                                                  'offset'=>$data['pageData']['offsets']['next']++,
                                                  'countRemaining'=>$countRemaining);
			}

            foreach($data['data'] as $row)
            {
				$name = '';

                //Determine a name to use
				if(!empty($row['NAME']))
                {
					$name = $row['NAME'];
				} else if(!empty($row['DOCUMENT_NAME'])) {
				    $name = $row['DOCUMENT_NAME'];
				} else {
                    $foundName = '';
					foreach($row as $k=>$v){
						if(strpos($k, 'NAME') !== false)
                        {
                            if(!empty($row[$k]))
                            {
							    $name = $v;
							    break;
                            } else if(empty($foundName)) {
                                $foundName = $v;
                            }
						}
					}

					if(empty($name))
					{
					   $name = $foundName;
					}
				}

				//BEGIN SUGARCRM flav=spotactions ONLY
                /*
				if ($m == 'Actions') {
					$actions++;

					$str .=  <<<EOHTML
<li><a id='sugaraction{$actions}' href="{$row['VALUE']}">$name</a></li>
EOHTML;
				}
				else
                */
				//END SUGARCRM flav=spotactions ONLY
				$displayResults[$m][$row['ID']] = $name;

		    }



        }
        $ss = new Sugar_Smarty();
        $ss->assign('displayResults', $displayResults);
        $ss->assign('displayMoreForModule', $displayMoreForModule);
        $ss->assign('appStrings', $GLOBALS['app_strings']);
        $ss->assign('appListStrings', $GLOBALS['app_list_strings']);
        $ss->assign('queryEncoded', $query_encoded);
        $template = 'include/SearchForm/tpls/SugarSpot.tpl';
        if(file_exists('custom/include/SearchForm/tpls/SugarSpot.tpl'))
        {
            $template = 'custom/include/SearchForm/tpls/SugarSpot.tpl';
        }
        return $ss->fetch($template);
	}

	/**
	 * Returns the array containing the $searchFields for a module.  This function
	 * first checks the default installation directories for the SearchFields.php file and then
	 * loads any custom definition (if found)
	 *
	 * @param  $moduleName String name of module to retrieve SearchFields entries for
	 * @return array of SearchFields
	 */
	protected static function getSearchFields(
	    $moduleName
	    )
	{
		$searchFields = array();

		if(file_exists("modules/{$moduleName}/metadata/SearchFields.php"))
		{
		    require("modules/{$moduleName}/metadata/SearchFields.php");
		}

		if(file_exists("custom/modules/{$moduleName}/metadata/SearchFields.php"))
		{
		    require("custom/modules/{$moduleName}/metadata/SearchFields.php");
		}

		return $searchFields;
	}

	//BEGIN SUGARCRM flav=spotactions ONLY
	/**
	 * Performs a search for actions based upon the query string
	 *
	 * @param  $query           string what we are searching for
	 * @param  $offset          int    search result offset
	 * @param  $max             int    max number of search results returned
	 * @param  $primary_module  string module to search in
	 * @return array
	 */
	protected function _searchActions(
	    $query,
	    $offset = -1,
	    $max,
	    $primary_module
	    )
	{
		$action_list = $this->_buildActionCache();

		$GLOBALS['matching_keys']=array();
		array_walk($action_list, array($this, '_searchKeys'),array($query,$primary_module));
		$data=array_slice($GLOBALS['matching_keys'],(($offset == -1)? 0 :$offset),$max);

		$pageData['bean'] = array('objectName' => 'Action', 'moduleDir' => 'modules/Action');
		$pageData['offsets'] = array( 'current'=>$offset, 'next'=>$offset, 'prev'=>$offset, 'end'=>$offset, 'total'=>count($GLOBALS['matching_keys']), 'totalCounted'=>count($GLOBALS['matching_keys']));


		return array('data'=>$data , 'pageData'=>$pageData);;
	}

	//END SUGARCRM flav=spotactions ONLY

	/**
	 * Get count from query
	 * @param SugarBean $seed
	 * @param string $main_query
	 */
	protected function _getCount($seed, $main_query)
	{
//        $count_query = $seed->create_list_count_query($main_query);
		$result = $seed->db->query("SELECT COUNT(*) as c FROM ($main_query) main");
		$row = $seed->db->fetchByAssoc($result);
		return isset($row['c'])?$row['c']:0;
	}

	/**
     * _performSearch
     *
	 * Performs the search from the global search field.
	 *
	 * @param  $query   string what we are searching for
	 * @param  $modules array  modules we are searching in
	 * @param  $offset  int    search result offset
	 * @return array
	 */
	protected function _performSearch(
	    $query,
	    $modules,
	    $offset = -1
	    )
	{
        //Return an empty array if no query string is given
	    if(empty($query))
        {
            return array();
        }

		$primary_module='';
		$results = array();
		require_once 'include/SearchForm/SearchForm2.php' ;
		$where = '';
        $searchEmail = preg_match('/^([^%]|%)*@([^%]|%)*$/', $query);

        // bug49650 - strip out asterisks from query in case
        // user thinks asterisk is a wildcard value
        $query = str_replace( '*' , '' , $query );
        
        $limit = !empty($GLOBALS['sugar_config']['max_spotresults_initial']) ? $GLOBALS['sugar_config']['max_spotresults_initial'] : 5;
		if($offset !== -1){
			$limit = !empty($GLOBALS['sugar_config']['max_spotresults_more']) ? $GLOBALS['sugar_config']['max_spotresults_more'] : 20;
		}
    	$totalCounted = empty($GLOBALS['sugar_config']['disable_count_query']);


        global $current_user;

	    foreach($modules as $moduleName)
        {
		    if (empty($primary_module))
		    {
		    	$primary_module=$moduleName;
		    }

			$searchFields = SugarSpot::getSearchFields($moduleName);

            //Continue on to the next module if no search fields found for module
			if (empty($searchFields[$moduleName]))
			{
				continue;
			}

			$return_fields = array();
			$seed = SugarModule::get($moduleName)->loadBean();

            //Continue on to next module if we don't have ListView ACLAccess for module
            if(!$seed->ACLAccess('ListView')) {
               continue;
            }

            $class = $seed->object_name;

			foreach($searchFields[$moduleName] as $k=>$v)
            {
				$keep = false;
				$searchFields[$moduleName][$k]['value'] = $query;

                //If force_unifiedsearch flag is true, we are essentially saying this field must be searched on (e.g. search_name in SearchFields.php file)
                if(!empty($searchFields[$moduleName][$k]['force_unifiedsearch']))
                {
                    continue;
                }

				if(!empty($GLOBALS['dictionary'][$class]['unified_search'])){
					if(empty($GLOBALS['dictionary'][$class]['fields'][$k]['unified_search'])){

						if(isset($searchFields[$moduleName][$k]['db_field'])){
							foreach($searchFields[$moduleName][$k]['db_field'] as $field)
                            {
								if(!empty($GLOBALS['dictionary'][$class]['fields'][$field]['unified_search']))
                                {
                                    if(isset($GLOBALS['dictionary'][$class]['fields'][$field]['type']))
                                    {
                                        if(!$this->filterSearchType($GLOBALS['dictionary'][$class]['fields'][$field]['type'], $query))
                                        {
                                            unset($searchFields[$moduleName][$k]);
                                            continue;
                                        }
                                    } else {
									    $keep = true;
                                    }
								}
							} //foreach
						}
                        # Bug 42961 Spot search for custom fields
                        if (!$keep && (isset($v['force_unifiedsearch']) == false || $v['force_unifiedsearch'] != true))
                        {
							if(strpos($k,'email') === false || !$searchEmail) {
								unset($searchFields[$moduleName][$k]);
							}
						}
					}else{
					    if($GLOBALS['dictionary'][$class]['fields'][$k]['type'] == 'int' && !is_numeric($query)) {
					        unset($searchFields[$moduleName][$k]);
					    }
					}
				}else if(empty($GLOBALS['dictionary'][$class]['fields'][$k]) ){
					//If module did not have unified_search defined, then check the exception for an email search before we unset
					if(strpos($k,'email') === false || !$searchEmail)
					{
					   unset($searchFields[$moduleName][$k]);
					}
				}else if(!$this->filterSearchType($GLOBALS['dictionary'][$class]['fields'][$k]['type'], $query)){
                    unset($searchFields[$moduleName][$k]);
				}
			} //foreach

            //If no search field criteria matched then continue to next module
			if (empty($searchFields[$moduleName]))
            {
                continue;
            }

            //Variable used to store the "name" field displayed in results
            $name_field = null;

			if(isset($seed->field_defs['name']))
            {
			    $return_fields['name'] = $seed->field_defs['name'];
                $name_field = 'name';
			}


			foreach($seed->field_defs as $k => $v)
            {
			    if(isset($seed->field_defs[$k]['type']) && ($seed->field_defs[$k]['type'] == 'name') && !isset($return_fields[$k]))
                {
				    $return_fields[$k] = $seed->field_defs[$k];
				}
			}


			if(!isset($return_fields['name']))
            {
			    // if we couldn't find any name fields, try search fields that have name in it
			    foreach($searchFields[$moduleName] as $k => $v)
                {
			        if(strpos($k, 'name') != -1 && isset($seed->field_defs[$k]) && !isset($seed->field_defs[$k]['source']))
                    {
				        $return_fields[$k] = $seed->field_defs[$k];
                        $name_field = $k;
				        break;
				    }
			    }
			}


			if(!isset($return_fields['name']))
            {
			    // last resort - any fields that have 'name' in their name
			    foreach($seed->field_defs as $k => $v)
                {
                    if(strpos($k, 'name') != -1 && isset($seed->field_defs[$k]) && !isset($seed->field_defs[$k]['source']))
                    {
				        $return_fields[$k] = $seed->field_defs[$k];
				        $name_field = $k;
                        break;
				    }
			    }
			}


			if(empty($name_field)) {
			    // FAIL: couldn't find a name field to display a result label
			    $GLOBALS['log']->error("Unable to find name field for module $moduleName");
			    continue;
			}

			if(isset($return_fields['name']['fields']))
            {
			    // some names are composite name fields (e.g. last_name, first_name), add these to return list
			    foreach($return_fields['name']['fields'] as $field)
                {
			        $return_fields[$field] = $seed->field_defs[$field];
			    }
			} 

			$searchForm = new SearchForm ( $seed, $moduleName ) ;
			$searchForm->setup (array ( $moduleName => array() ) , $searchFields , '' , 'saved_views' /* hack to avoid setup doing further unwanted processing */ ) ;
			$where_clauses = $searchForm->generateSearchWhere() ;

			if(empty($where_clauses))
            {
			    continue;
			}
			if(count($where_clauses) > 1) {
			    $query_parts =  array();

			    $ret_array_start = $seed->create_new_list_query('', '', $return_fields, array(), 0, '', true, $seed, true);
                $search_keys = array_keys($searchFields[$moduleName]);

                foreach($where_clauses as $n => $clause) {
			        $allfields = $return_fields;
			        $skey = $search_keys[$n];
			        if(isset($seed->field_defs[$skey])) {
                        // Joins for foreign fields aren't produced unless the field is in result, hence the merge
			            $allfields[$skey] = $seed->field_defs[$skey];
			        }
                    $ret_array = $seed->create_new_list_query('', $clause, $allfields, array(), 0, '', true, $seed, true);
                    $query_parts[] = $ret_array_start['select'] . $ret_array['from'] . $ret_array['where'] . $ret_array['order_by'];
                }
                $main_query = "(".join(") UNION (", $query_parts).")";
			} else {
                foreach($searchFields[$moduleName] as $k=>$v){
                    if(isset($seed->field_defs[$k])) {
			            $return_fields[$k] = $seed->field_defs[$k];
                    }
			    }
			    $ret_array = $seed->create_new_list_query('', $where_clauses[0], $return_fields, array(), 0, '', true, $seed, true);
		        $main_query = $ret_array['select'] . $ret_array['from'] . $ret_array['where'] . $ret_array['order_by'];
			}

			$totalCount = null;
		    if($limit < -1) {
			    $result = $seed->db->query($main_query);
		    } else {
			    if($limit == -1) {
				    $limit = $GLOBALS['sugar_config']['list_max_entries_per_page'];
                }

                if($offset == 'end') {
		            $totalCount = $this->_getCount($seed, $main_query);
		            if($totalCount) {
                	    $offset = (floor(($totalCount -1) / $limit)) * $limit;
		            } else {
		                $offset = 0;
		            }
                }
                $result = $seed->db->limitQuery($main_query, $offset, $limit + 1);
		    }

            $data = array();
            $count = 0;
            while($count < $limit && ($row = $seed->db->fetchByAssoc($result))) {
		        $temp = clone $seed;
			    $temp->setupCustomFields($temp->module_dir);
				$temp->loadFromRow($row);
				$data[] = $temp->get_list_view_data($return_fields);
				$count++;
		    }

    		$nextOffset = -1;
    		$prevOffset = -1;
    		$endOffset = -1;

    		if($count >= $limit) {
    			$nextOffset = $offset + $limit;
    		}

    		if($offset > 0) {
    			$prevOffset = $offset - $limit;
    			if($prevOffset < 0) $prevOffset = 0;
    		}

    		if( $count >= $limit && $totalCounted){
    		    if(!isset($totalCount)) {
    			    $totalCount  = $this->_getCount($seed, $main_query);
    		    }
    		} else {
    		    $totalCount = $count + $offset;
    		}

            $pageData['offsets'] = array( 'current'=>$offset, 'next'=>$nextOffset, 'prev'=>$prevOffset, 'end'=>$endOffset, 'total'=>$totalCount, 'totalCounted'=>$totalCounted);
		    $pageData['bean'] = array('objectName' => $seed->object_name, 'moduleDir' => $seed->module_dir);

            //BEGIN SUGARCRM flav=com ONLY
            $readAccess = true;
            //END SUGARCRM flav=com ONLY

            //BEGIN SUGARCRM flav=pro ONLY
            $readAccess = false;

            if(isset($return_fields['name']['fields']))
            {
                // some names are composite name fields (e.g. last_name, first_name), check access to these fields
			    foreach($return_fields['name']['fields'] as $field)
                {
                    if(ACLField::hasAccess($field, $seed->module_dir, $current_user->id, false))
                    {
                       $readAccess = true;
                    }
			    }
            } else {
                $readAccess = ACLField::hasAccess($name_field, $seed->module_dir, $current_user->id, false);
            }
            //END SUGARCRM flav=pro ONLY
		    $results[$moduleName] = array("data"=>$data, "pageData"=>$pageData, "readAccess"=>$readAccess);

		} //foreach

        //BEGIN SUGARCRM flav=spotactions ONLY
        //Search actions...
        $results['Actions'] = $this->_searchActions($query,$offset,$limit,$primary_module);
        //END SUGARCRM flav=spotactions ONLY
        return $results;
	}

	//BEGIN SUGARCRM flav=spotactions ONLY
	/**
	 * Builds the search action cache
	 */
	protected function _buildActionCache()
	{
	    $action_list = array();
	    $all_menu_files=array();
	    $all_module_menu=array();

		global $current_user, $current_language, $app_list_strings, $mod_list_strings;
		$current_language= (empty($current_language)? "en_us": $current_language);

		 $user_action_map_filename = 'cache/modules/'. $current_language . '_sugar_actions_' . $current_user->id . ".php";

		 if (!file_exists($user_action_map_filename)) {
			 $all_menu_files=findAllFiles(getcwd(). "/modules",$all_menu_files,false,"Menu.php");
			 if (!empty($all_menu_files) and is_array($all_menu_files)) {
			     foreach ($all_menu_files as $menu_file) {

			 		//skip over the import module for now. but we will need a way to add
			 		//that option everywhere....
			 		if (strpos($menu_file,'/Import/') !== false) {
			 			continue;
			 		}


					$lang_file_name=dirname($menu_file). '/language/' . $current_language. ".lang.php";
			 		if (!file_exists($lang_file_name)) {
			 			//try the english lang file.
			 			$lang_file_name=dirname($menu_file). '/language/' . "en_us.lang.php";
			 		}

			 		if(file_exists($lang_file_name) && file_exists($menu_file)){
			 			global $mod_strings;
			 			require($lang_file_name);

			 			$module_menu = array();
		 				require($menu_file);
		 				$all_module_menu=array_merge($all_module_menu,$module_menu);
					}
                }
			 }

			 foreach ($all_module_menu as $menu_entry )	{
				 	//0: action //1: Label //2: action name //3: Module
					$action_list[$menu_entry[1]]=$menu_entry[0];
			 }


			 //process the admin actions now..
			 if ($current_user->isDeveloperForAnyModule()) {
			 	global $admin_group_header;
			 	require("modules/Administration/metadata/adminpaneldefs.php");

			 	global $mod_strings;
 				require("modules/Administration/language/". $current_language . ".lang.php");

			 	//access to the menu option is decided in the adminpaneldes.php
			 	foreach ($admin_group_header as $key=>$values) {
			 		//this will be tue for Module level admins only..
			 		if (count($values[3]) == 0) {
			 			continue;
			 		}
			 		foreach ($values[3] as $link_key=>$link_value) {
			 			foreach ($link_value as $def) {
				 			$action_list[$mod_strings[$def[1]]]=$def[3];
			 			}
			 		}
			 	}
			 }

			 file_put_contents($user_action_map_filename,'<?php $action_list='.var_export($action_list,true). '; ?>');
		 }
		 else {
		 	require ($user_action_map_filename);
		 }

		 return $action_list;
	}
	//END SUGARCRM flav=spotactions ONLY

	/**
     * Function used to walk the array and find keys that map the queried string.
     * if both the pattern and module name is found the promote the string to thet top.
     */
    protected function _searchKeys(
        $item1,
        $key,
        $patterns
        )
    {
        //make the module name singular....
        if ($patterns[1][strlen($patterns[1])-1] == 's') {
            $patterns[1]=substr($patterns[1],0,(strlen($patterns[1])-1));
        }

        $module_exists = stripos($key,$patterns[1]); //primary module name.
        $pattern_exists = stripos($key,$patterns[0]); //pattern provided by the user.
        if ($module_exists !== false and $pattern_exists !== false)  {
            $GLOBALS['matching_keys']= array_merge(array(array('NAME'=>$key, 'ID'=>$key, 'VALUE'=>$item1)),$GLOBALS['matching_keys']);
        }
        else {
            if ($pattern_exists !== false) {
                $GLOBALS['matching_keys'][]=array('NAME'=>$key, 'ID'=>$key, 'VALUE'=>$item1);
            }
        }
    }


    /**
     * filterSearchType
     *
     * This is a private function to determine if the search type field should be filtered out based on the query string value
     * 
     * @param String $type The string value of the field type (e.g. phone, date, datetime, int, etc.)
     * @param String $query The search string value sent from the global search
     * @return boolean True if the search type fits the query string value; false otherwise
     */
    protected function filterSearchType($type, $query)
    {
        switch($type)
        {
            case 'id':
            case 'date':
            case 'datetime':
            case 'bool':
                return false;
                break;
            case 'int':
                if(!is_numeric($query)) {
                   return false;
                }
                break;
            case 'phone':
            case 'decimal':
            case 'float':
                if(!preg_match('/[0-9]/', $query))
                {
                   return false;
                }
                break;
        }
        return true;
    }

}