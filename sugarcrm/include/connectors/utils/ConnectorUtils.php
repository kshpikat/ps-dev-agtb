<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
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
define('CONNECTOR_DISPLAY_CONFIG_FILE', 'custom/modules/Connectors/metadata/display_config.php');
require_once('include/connectors/ConnectorFactory.php');

function sources_sort_function($a, $b) {
	if(isset($a['order']) && isset($b['order'])) {
	   if($a['order'] == $b['order']) {
	   	  return 0;
	   }
	   
	   return ($a['order'] < $b['order']) ? -1 : 1;
	}
	
	return 0;
}

class ConnectorUtils 
{
    public static function getConnector(
        $id, 
        $refresh = false
        ) 
    {
        $s = self::getConnectors($refresh);
        return !empty($s[$id]) ? $s[$id] : null;
    }


    /**
     * getSearchDefs
     * Returns an Array of the search field defintions Connector module to
     * search entries from the connector.  If the searchdefs.php file in the custom 
     * directory is not found, it defaults to using the mapping.php file entries to
     * create a default version of the file.
     * 
     * @param boolean $refresh boolean value to manually refresh the search definitions
     * @return mixed $searchdefs Array of the search definitions
     */
    public static function getSearchDefs(
        $refresh = false
        ) 
    {
        if($refresh || !file_exists('custom/modules/Connectors/metadata/searchdefs.php')) {
            
            require('modules/Connectors/metadata/searchdefs.php');
            
            if(!file_exists('custom/modules/Connectors/metadata')) {
               mkdir_recursive('custom/modules/Connectors/metadata');
            }
            
            if(!write_array_to_file('searchdefs', $searchdefs, 'custom/modules/Connectors/metadata/searchdefs.php')) {
               $GLOBALS['log']->fatal("Cannot write file custom/modules/Connectors/metadata/searchdefs.php");
               return array();	
            }	
        }
        
        require('custom/modules/Connectors/metadata/searchdefs.php');
        return $searchdefs;	
    }

    
    /**
     * getViewDefs
     * Returns an Array of the merge definitions used by the Connector module to
     * merge values into the bean instance
     * 
     * @param mixed $filter_sources Array optional Array value of sources to only use
     * @return mixed $mergedefs Array of the merge definitions
     */
    public static function getViewDefs(
        $filter_sources = array()
        ) 
    {
        //Go through all connectors and get their mapping keys and merge them across each module
        $connectors = self::getConnectors();
        $modules_sources = self::getDisplayConfig();
        $view_defs = array();
        foreach($connectors as $id=>$ds) {
        
           if(!empty($filter_sources) && !isset($filter_sources[$id])) {
              continue;
           }
            
           if(file_exists('custom/' . $ds['directory'] . '/mapping.php')) {
             require('custom/' . $ds['directory'] . '/mapping.php');
           } else if(file_exists($ds['directory'] . '/mapping.php')) {
             require($ds['directory'] . '/mapping.php');
           }
            
           if(!empty($mapping['beans'])) {
              foreach($mapping['beans'] as $module=>$map) {
                 if(!empty($modules_sources[$module][$id])) {
                     if(!empty($view_defs['Connector']['MergeView'][$module])) {
                        $view_defs['Connector']['MergeView'][$module] = array_merge($view_defs['Connector']['MergeView'][$module], array_flip($map));
                     } else {
                        $view_defs['Connector']['MergeView'][$module] = array_flip($map);
                     }
                 }
              }
           }
        }
        
        if(!empty($view_defs['Connector']['MergeView'])) {
            foreach($view_defs['Connector']['MergeView'] as $module=>$map) {
                $view_defs['Connector']['MergeView'][$module] = array_keys($view_defs['Connector']['MergeView'][$module]);
            }
        }
        
        return $view_defs;
    }
    
    
    /**
     * getMergeViewDefs
     * Returns an Array of the merge definitions used by the Connector module to
     * merge values into the bean instance
     * 
     * @deprecated This method has been replaced by getViewDefs
     * @param boolean $refresh boolean value to manually refresh the mergeview definitions
     * @return mixed $mergedefs Array of the merge definitions
     */
    public static function getMergeViewDefs(
        $refresh = false
        ) 
    {
        if($refresh || !file_exists('custom/modules/Connectors/metadata/mergeviewdefs.php')) {
            
            //Go through all connectors and get their mapping keys and merge them across each module
            $connectors = self::getConnectors($refresh);
            $modules_sources = self::getDisplayConfig();
            $view_defs = array();
            foreach($connectors as $id=>$ds) {
                
               if(file_exists('custom/' . $ds['directory'] . '/mapping.php')) {
                 require('custom/' . $ds['directory'] . '/mapping.php');
               } else if(file_exists($ds['directory'] . '/mapping.php')) {
                 require($ds['directory'] . '/mapping.php');
               }
                
               if(!empty($mapping['beans'])) {
                  foreach($mapping['beans'] as $module=>$map) {
                     if(!empty($modules_sources[$module][$id])) {
                         if(!empty($view_defs['Connector']['MergeView'][$module])) {
                            $view_defs['Connector']['MergeView'][$module] = array_merge($view_defs['Connector']['MergeView'][$module], array_flip($map));
                         } else {
                            $view_defs['Connector']['MergeView'][$module] = array_flip($map);
                         }
                     }
                  }
               }
            }
            
            if(!empty($view_defs['Connector']['MergeView'])) {
                foreach($view_defs['Connector']['MergeView'] as $module=>$map) {
                    $view_defs['Connector']['MergeView'][$module] = array_keys($view_defs['Connector']['MergeView'][$module]);
                }
            }
            
            if(!file_exists('custom/modules/Connectors/metadata')) {
               mkdir_recursive('custom/modules/Connectors/metadata');
            }
            
            if(!write_array_to_file('viewdefs', $view_defs, 'custom/modules/Connectors/metadata/mergeviewdefs.php')) {
               $GLOBALS['log']->fatal("Cannot write file custom/modules/Connectors/metadata/mergeviewdefs.php");
               return array();	
            }	
        }
        
        require('custom/modules/Connectors/metadata/mergeviewdefs.php');
        return $viewdefs;
    
    }
    
    
    /**
     * getConnectors
     * Returns an Array of the connectors that have been loaded into the system
     * along with attributes pertaining to each connector.
     * 
     * @param boolean $refresh boolean flag indicating whether or not to force rewriting the file; defaults to false
     * @returns mixed $connectors Array of the connector entries found
     */
    public static function getConnectors(
        $refresh = false
        ) 
    {    
        //define paths
        $src1 = 'modules/Connectors/connectors/sources';
        $src2 = 'custom/modules/Connectors/connectors/sources';
        $src3 = 'custom/modules/Connectors/metadata';
        $src4 = 'custom/modules/Connectors/metadata/connectors.php';
        
        //if this is a templated environment, then use utilities to get the proper paths
        if(defined('TEMPLATE_URL')){
            $src1 = SugarTemplateUtilities::getFilePath($src1);
            $src2 = SugarTemplateUtilities::getFilePath($src2);
            $src3 = SugarTemplateUtilities::getFilePath($src3);
            $src4 = SugarTemplateUtilities::getFilePath($src4);		
        }
        
        if($refresh || !file_exists($src4)) {
          
          $sources = array_merge(self::getSources($src1), self::getSources($src2));
          if(!file_exists($src3)) {
             mkdir_recursive($src3);
          }
    
          if(!write_array_to_file('connectors', $sources, $src4)) {
             //Log error and return empty array
             $GLOBALS['log']->fatal("Cannot write sources to file");
             return array();	
          }
        } //if
    
        require($src4);
        return $connectors;
    }
    
    
    /**
     * getSources
     * Returns an Array of source entries found under the given directory
     * @param String $directory The directory to search
     * @return mixed $sources An Array of source entries
     */
    private static function getSources(
        $directory = 'modules/Connectors/connectors/sources'
        ) 
    {
          if(file_exists($directory)) {
              
              $files = array();
              $files = findAllFiles($directory, $files, false, 'config\.php');
              $start = strrpos($directory, '/') == strlen($directory)-1 ? strlen($directory) : strlen($directory) + 1;
              $sources = array();
              $sources_ordering = array();
              foreach($files as $file) {
                      require($file);
                      $end = strrpos($file, '/') - $start;
                      $source = array();
                      $source['id'] = str_replace('/', '_', substr($file, $start, $end));
                      $source['name'] = !empty($config['name']) ? $config['name'] : $source['id'];
                      $source['enabled'] = true;
                      $source['directory'] = $directory . '/' . str_replace('_', '/', $source['id']);
                      $order = isset($config['order']) ? $config['order'] : 99; //default to end using 99 if no order set
                      
                      $instance = ConnectorFactory::getInstance($source['id']);
                      $mapping = $instance->getMapping();
                      $modules = array();
                      if(!empty($mapping['beans'])) {
                         foreach($mapping['beans'] as $module=>$mapping_entry) {
                             $modules[]=$module;
                         }
                      }     	      
                      $source['modules'] = $modules;
                      $sources_ordering[$source['id']] = array('order'=>$order, 'source'=>$source);
              }
              
              usort($sources_ordering, 'sources_sort_function');
              foreach($sources_ordering as $entry) {
              	 $sources[$entry['source']['id']] = $entry['source'];
              }
              return $sources;
          }
          return array();
    }
    
    
    /**
     * getDisplayConfig
     * 
     */
    public static function getDisplayConfig(
        $refresh = false
        )
    {    
        if(!file_exists(CONNECTOR_DISPLAY_CONFIG_FILE) || $refresh) {
            $sources = self::getConnectors();
            $modules_sources = array();
            
            //Make the directory for the config file
            if(!file_exists('custom/modules/Connectors/metadata')) {
                mkdir_recursive('custom/modules/Connectors/metadata');
            }
                
            if(!write_array_to_file('modules_sources', $modules_sources, CONNECTOR_DISPLAY_CONFIG_FILE)) {
                //Log error and return empty array
                $GLOBALS['log']->fatal("Cannot write \$modules_sources to " . CONNECTOR_DISPLAY_CONFIG_FILE);
            }	    	
                
        }
        
        require(CONNECTOR_DISPLAY_CONFIG_FILE);
        return $modules_sources;
    }
    
    
    /**
     * getModuleConnectors
     *
     * @param String $module the module to get the connectors for
     * @param mixed $connectors Array of connectors mapped to the module or empty if none
     * @return unknown
     */
    public static function getModuleConnectors(
        $module
        )
    {
        $modules_sources = self::getDisplayConfig();
        if(!empty($modules_sources) && !empty($modules_sources[$module])){
            $sources = array();
            foreach($modules_sources[$module] as $index => $id){
                $sources[$id] = self::getConnector($id);
            }
            return $sources;
        }else{
            return array();
        }
    }
    
    /**
     * isModuleEnabled
     * Given a module name, checks to see if the module is enabled to be serviced by the connector module
     * @param String $module String name of the module
     * @return boolean $enabled boolean value indicating whether or not the module is enabled to be serviced by the connector module
     */
    public static function isModuleEnabled(
        $module
        ) 
    {
        $modules_sources = self::getDisplayConfig();
        return !empty($modules_sources) && !empty($modules_sources[$module]) ? true : false;
    }
    
    
    /**
     * isSourceEnabled
     * Given a source id, checks to see if the source is enabled for at least one module
     * @param String $source String name of the source
     * @return boolean $enabled boolean value indicating whether or not the source is displayed in at least one module
     */
    public static function isSourceEnabled(
        $source
        ) 
    {
        $modules_sources = self::getDisplayConfig();
        foreach($modules_sources as $module=>$mapping) {
                foreach($mapping as $s) {
                        if($s == $source) {
                           return true;  	
                        }
                }
        }
        return false;
    }
    
    /**
     * When a module has all of the sources removed from it we do not properly remove it from the viewdefs. This function
     * will handle that.
     *
     * @param String $module	 - the module in question
     */
    public static function cleanMetaDataFile(
        $module
        )
    {
        $metadata_file = file_exists("custom/modules/{$module}/metadata/detailviewdefs.php") ? "custom/modules/{$module}/metadata/detailviewdefs.php" : "modules/{$module}/metadata/detailviewdefs.php"; 
        require($metadata_file);
    
        $insertConnectorButton = true;
                     
    
        //BEGIN SUGARCRM flav=pro || flav=sales ONLY
    
        if(!empty($viewdefs)) {
            $buttons = !empty($viewdefs[$module]['DetailView']['templateMeta']['form']['buttons']) ? $viewdefs[$module]['DetailView']['templateMeta']['form']['buttons'] : array();
        }
                     
        $hasConnectorDefined = false;
        $button_keys = array();
        foreach($buttons as $id=>$button) {
             if(!is_array($button) && $button == 'CONNECTOR') {
                $button_keys['CONNECTOR'] = $id;
                break;
             }
       }
       if ( isset($button_keys['CONNECTOR']) && isset($buttons[$button_keys['CONNECTOR']]) )
           unset($buttons[$button_keys['CONNECTOR']]);
                     
       $hasWizardSourceEnabled = self::hasWizardSourceEnabledForModule($module);
       //Update the button changes
       $viewdefs[$module]['DetailView']['templateMeta']['form']['buttons'] = $buttons;         	  	 
    
       //END SUGARCRM flav=pro || flav=sales ONLY
    
    
       self::removeHoverField($viewdefs, $module);
       
        //Make the directory for the metadata file
        if(!file_exists("custom/modules/{$module}/metadata")) {
            mkdir_recursive("custom/modules/{$module}/metadata");
        }          	  	 
             
        if(!write_array_to_file('viewdefs', $viewdefs,  "custom/modules/{$module}/metadata/detailviewdefs.php")) {
            $GLOBALS['log']->fatal("Cannot update file custom/modules/{$module}/metadata/detailviewdefs.php");
            return false;
        }
                     
        if(file_exists("{$GLOBALS['sugar_config']['cache_dir']}modules/{$module}/DetailView.tpl") && !unlink("{$GLOBALS['sugar_config']['cache_dir']}modules/{$module}/DetailView.tpl")) {
            $GLOBALS['log']->fatal("Cannot delete file {$GLOBALS['sugar_config']['cache_dir']}modules/{$module}/DetailView.tpl");
            return false;
        }
    }
    
    
    /**
     * updateMetaDataFiles
     * This method updates the metadata files (detailviewdefs.php) according to the settings in display_config.php
     * @return $result boolean value indicating whether or not the method successfully completed.
     */
    public static function updateMetaDataFiles() 
    {
        if(file_exists(CONNECTOR_DISPLAY_CONFIG_FILE)) {
           $modules_sources = array();
        
           require(CONNECTOR_DISPLAY_CONFIG_FILE);
           
           $GLOBALS['log']->debug(var_export($modules_sources, true));
           if(!empty($modules_sources)) {
              foreach($modules_sources as $module=>$mapping) {
                     $metadata_file = file_exists("custom/modules/{$module}/metadata/detailviewdefs.php") ? "custom/modules/{$module}/metadata/detailviewdefs.php" : "modules/{$module}/metadata/detailviewdefs.php"; 
    
                                     
                     $viewdefs = array();
    
                     require($metadata_file);
    
                     $insertConnectorButton = true;
                     
    
                     //BEGIN SUGARCRM flav=pro || flav=sales ONLY
    
                     if(!empty($viewdefs)) {
                        $buttons = !empty($viewdefs[$module]['DetailView']['templateMeta']['form']['buttons']) ? $viewdefs[$module]['DetailView']['templateMeta']['form']['buttons'] : array();
                     }
                     
                     $hasConnectorDefined = false;
                     $button_keys = array();
                     foreach($buttons as $id=>$button) {
                         if(!is_array($button) && $button == 'CONNECTOR') {
                            $button_keys['CONNECTOR'] = $id;
                            $hasConnectorDefined = true;
                         }
                     }
                     
                     $hasWizardSourceEnabled = self::hasWizardSourceEnabledForModule($module);
                     
                     if(!empty($mapping) && !$hasConnectorDefined && $hasWizardSourceEnabled) {
                        $buttons[] = 'CONNECTOR';
                     } else if((empty($mapping) && $hasConnectorDefined) || !$hasWizardSourceEnabled) {
                        if(!empty($button_keys['CONNECTOR']) && !empty($buttons[$button_keys['CONNECTOR']])){
                            unset($buttons[$button_keys['CONNECTOR']]);
                        }
                     }
                     
                     //Update the button changes
                     $viewdefs[$module]['DetailView']['templateMeta']['form']['buttons'] = $buttons;         	  	 
    
                     //END SUGARCRM flav=pro || flav=sales ONLY
    
    
                     self::removeHoverField($viewdefs, $module);
                    
                     //Insert the hover field if available
                     if(!empty($mapping)) {
    
                        require_once('include/connectors/sources/SourceFactory.php');
                        require_once('include/connectors/formatters/FormatterFactory.php');
                        $shown_formatters = array();
                        foreach($mapping as $id) {
                                $source = SourceFactory::getSource($id, false);
                                if($source->isEnabledInHover() && $source->isRequiredConfigFieldsForButtonSet()) {
                                   $shown_formatters[$id] = FormatterFactory::getInstance($id);
                                }   
                        }
    
                        //Now we have to decide which field to put it on... use the first one for now
                        if(!empty($shown_formatters)) {
                            
                           foreach($shown_formatters as $id=>$formatter) {
                               $added_field = false;   
                               $formatter_mapping = $formatter->getSourceMapping();
                               
                               $source = $formatter->getComponent()->getSource();
                               //go through the mapping and add the hover to every field define in the mapping
                               //1) check for hover fields
                               $hover_fields = $source->getFieldsWithParams('hover', true);
                               
                               foreach($hover_fields as $key => $def){
                                    if(!empty($formatter_mapping['beans'][$module][$key])){
                                        $added_field = self::setHoverField($viewdefs, $module, $formatter_mapping['beans'][$module][$key], $id);
                                    }
                               }
                               
                               //2) check for first mapping field
                               if(!$added_field && !empty($formatter_mapping['beans'][$module])) {
                                    foreach($formatter_mapping['beans'][$module] as $key => $val){
                                        $added_field = self::setHoverField($viewdefs, $module, $val, $id);
                                        if($added_field){
                                            break;
                                        }
                                    }
                               }
                           } //foreach
                           
                            
    
                           //Log an error message
                           if(!$added_field) {
                              $GLOBALS['log']->fatal("Unable to place hover field link on metadata for module {$module}");
                           }
                        }
                        
                     }
                     
                     
                     //Make the directory for the metadata file
                     if(!file_exists("custom/modules/{$module}/metadata")) {
                        mkdir_recursive("custom/modules/{$module}/metadata");
                     }          	  	 
             
                     if(!write_array_to_file('viewdefs', $viewdefs,  "custom/modules/{$module}/metadata/detailviewdefs.php")) {
                        $GLOBALS['log']->fatal("Cannot update file custom/modules/{$module}/metadata/detailviewdefs.php");
                        return false;
                     }
                     
                     if(file_exists("{$GLOBALS['sugar_config']['cache_dir']}modules/{$module}/DetailView.tpl") && !unlink("{$GLOBALS['sugar_config']['cache_dir']}modules/{$module}/DetailView.tpl")) {
                        $GLOBALS['log']->fatal("Cannot delete file {$GLOBALS['sugar_config']['cache_dir']}modules/{$module}/DetailView.tpl");
                        return false;
                     }
              }
           }
        }
        return true;
    }
    
    public function removeHoverField(
        &$viewdefs, 
        $module
        ) 
    {	 
        require_once('include/SugarFields/Parsers/MetaParser.php');
        $metaParser = new MetaParser();	
        if(!$metaParser->hasMultiplePanels($viewdefs[$module]['DetailView']['panels'])) {
            $keys = array_keys($viewdefs[$module]['DetailView']['panels']);
            if(!empty($keys) && count($keys) != 1) {
               $viewdefs[$module]['DetailView']['panels'] = array('default'=>$viewdefs[$module]['DetailView']['panels']);
            }
        }
        
        foreach($viewdefs[$module]['DetailView']['panels'] as $panel_id=>$panel) {
          foreach($panel as $row_id=>$row) { 
              foreach($row as $field_id=>$field) {
                  if(is_array($field) && !empty($field['displayParams']['enableConnectors'])) {
                    
                     unset($field['displayParams']['enableConnectors']);
                     unset($field['displayParams']['module']);
                     unset($field['displayParams']['connectors']);
                     $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id] = $field;
                  }
              } //foreach
          } //foreach
        } //foreach
        return false;		
    }
    
    public function setHoverField(
        &$viewdefs, 
        $module, 
        $hover_field, 
        $source_id
        ) 
    {
       //Check for metadata files that aren't correctly created
       require_once('include/SugarFields/Parsers/MetaParser.php');
       $metaParser = new MetaParser();
       if(!$metaParser->hasMultiplePanels($viewdefs[$module]['DetailView']['panels'])) {
            $keys = array_keys($viewdefs[$module]['DetailView']['panels']);
            if(!empty($keys) && count($keys) != 1) {
               $viewdefs[$module]['DetailView']['panels'] = array('default'=>$viewdefs[$module]['DetailView']['panels']);
            }
       }
       
       foreach($viewdefs[$module]['DetailView']['panels'] as $panel_id=>$panel) {
          foreach($panel as $row_id=>$row) {
              foreach($row as $field_id=>$field) {
                  $name = is_array($field) ? $field['name'] : $field;
                  if($name == $hover_field) {
                      if(is_array($field)) {
                         if(!empty($viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams'])) {
                            $newDisplayParam = $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams'];
                            $newDisplayParam['module'] = $module;
                            $newDisplayParam['enableConnectors'] = true;
                            if(!is_null($source_id) && !in_array($source_id, $newDisplayParam['connectors'])){
                                $newDisplayParam['connectors'][] = $source_id;
                            }
                            $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams'] = $newDisplayParam;
                         } else {
                            $field['displayParams'] = array('enableConnectors'=>true, 'module'=>$module, 'connectors' => array(0 => $source_id));
                            $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id] = $field;
                         }
                        
                      } else {
                         $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id] = array ('name'=>$field, 'displayParams'=>array('enableConnectors'=>true, 'module'=>$module, 'connectors' => array(0 => $source_id)));
                      }
                      return true;
                  }
              }
          }
       }
       return false;   	
    }
    
    /**
     * setDefaultHoverField
     * Sets the hover field to the first element in the detailview screen
     *
     * @param Array $viewdefs the metadata of the detailview
     * @param String $module the Module to which the hover field should be added to
     * @return boolean True if field was added; false otherwise
     */
    private function setDefaultHoverField(
        &$viewdefs, 
        $module, 
        $source_id
        ) 
    {
      foreach($viewdefs[$module]['DetailView']['panels'] as $panel_id=>$panel) {
          foreach($panel as $row_id=>$row) {
              foreach($row as $field_id=>$field) {
                  if(is_array($field)) {
                     if(!empty($viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams'])) {
                        $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams']['enableConnectors'] = true;
                        $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams']['module'] = $module;
                        if(!is_null($source_id) && !in_array($source_id, $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams']['connectors'])){
                                $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id]['displayParams']['connectors'][] = $source_id;
                        }
                     } else {
                        $field['displayParams'] = array('enableConnectors'=>true, 'module'=>$module, 'connectors' => array(0 => $source_id));
                        $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id] = $field;
                     }
                  } else {
                     $viewdefs[$module]['DetailView']['panels'][$panel_id][$row_id][$field_id] = array ('name'=>$field, 'displayParams'=>array('enableConnectors'=>true, 'module'=>$module, 'connectors' => array(0 => $source_id)));
                  }
                  return true;
              } //foreach
          } //foreach
      } //foreach
      return false;	
    }
    
    
    /**
     * getConnectorButtonScript
     * This method builds the HTML code for the hover link field
     * 
     * @param mixed $displayParams Array value of display parameters passed from the SugarField code
     * @param mixed $smarty The Smarty object from the calling smarty code
     * @return String $code The HTML code for the hover link
     */
    public static function getConnectorButtonScript(
        $displayParams, 
        $smarty
        ) 
    {     
        $module = $displayParams['module'];
        require_once('include/connectors/utils/ConnectorUtils.php');
        $modules_sources = self::getDisplayConfig();
        global $current_language, $app_strings;
        $mod_strings = return_module_language($current_language, 'Connectors');
        $menuParams = 'var menuParams = "';
        $shown_sources = array();
        if(!empty($module) && !empty($displayParams['connectors'])) {
            foreach($displayParams['connectors'] as $id) {
                if(!empty($modules_sources[$module]) && in_array($id, $modules_sources[$module])){
                    $shown_sources[] = $id;
                }	                  
              }
              
              if(empty($shown_sources)) {
                    return '';
              }
              
              require_once('include/connectors/formatters/FormatterFactory.php'); 
              $code = '';
                          
              //If there is only one source, just show the icon or some standalone view
              if(count($shown_sources) == 1) {
                  $formatter = FormatterFactory::getInstance($shown_sources[0]);
                  $formatter->setModule($module);
                  $formatter->setSmarty($smarty);
                  $formatter_code = $formatter->getDetailViewFormat();
                  if(!empty($formatter_code)) {
                      $iconFilePath = $formatter->getIconFilePath();
                      $iconFilePath = empty($iconFilePath) ? 'themes/default/images/icon_Connectors.gif' : $iconFilePath;
            
                      $code = '<img id="dswidget_img" border="0" src="' . $iconFilePath .'" alt="' . $shown_sources[0] .'" onmouseover="show_' . $shown_sources[0] . '(event);">';
                      $code .= "<link rel='stylesheet' type='text/css' href='include/javascript/yui/build/container/assets/container.css'>"; 
                      $code .= "<script type='text/javascript' src='{sugar_getjspath file='include/connectors/formatters/default/company_detail.js'}'></script>";
                      $code .= $formatter->getDetailViewFormat();
                      $code .= $formatter_code;
                  }
                  return $code; 	
              } else {
     
                  $formatterCode = '';	
                  $sourcesDisplayed = 0;
                  $singleIcon = '';		  
                  foreach($shown_sources as $id) {
                      $formatter = FormatterFactory::getInstance($id);
                      $formatter->setModule($module);
                      $formatter->setSmarty($smarty);
                      $buttonCode = $formatter->getDetailViewFormat();
                      if(!empty($buttonCode)) {
                          $sourcesDisplayed++;
                          $singleIcon = $formatter->getIconFilePath();
                          $source = SourceFactory::getSource($id);	          	      
                          $config = $source->getConfig();
                          $name = !empty($config['name']) ? $config['name'] : $id;
                          //Create the menu item to call show_[source id] method in javascript
                          $menuParams .= '<a href=\'#\' style=\'width:150px\' class=\'menuItem\' onmouseover=\'hiliteItem(this,\"yes\");\' onmouseout=\'unhiliteItem(this);\' onclick=\'show_' . $id . '(event);\'>' . $name . '</a>'; 
                          $formatterCode .= $buttonCode;          	          
                      }   
                  } //for
    
                  if(!empty($formatterCode)) {
                      if($sourcesDisplayed > 1) {
                      	$dswidget_img = SugarThemeRegistry::current()->getImageURL('MoreDetail.png');
                        $code = '<img id="dswidget_img" src="'.$dswidget_img.'" width="8" height="7" border="0" alt="connectors_popups" onmouseover="return showConnectorMenu2();" onmouseout="return nd(1000);">';
                      } else {
                       	  $dswidget_img = SugarThemeRegistry::current()->getImageURL('icon_Connectors.gif');
                          $singleIcon = empty($singleIcon) ? $dswidget_img : $singleIcon;
                          $code = '<img id="dswidget_img" border="0" src="' . $singleIcon . '" alt="connectors_popups" onmouseover="return showConnectorMenu2();" onmouseout="return nd(1000);">';	
                      }
                      $code .= "{overlib_includes}\n";
                      $code .= "<link rel='stylesheet' type='text/css' href='include/javascript/yui/build/container/assets/container.css'>\n"; 
                      $code .= "<script type='text/javascript' src='{sugar_getjspath file='include/connectors/formatters/default/company_detail.js'}'></script>\n";
                      $code .= "<script type='text/javascript'>\n";
                      $code .= "function showConnectorMenu2() {literal} { {/literal}\n";	 		  
                      
                      $menuParams .= '";';
                      $code .= $menuParams . "\n";
                      $code .= "return overlib(menuParams, CENTER, STICKY, MOUSEOFF, 3000, WIDTH, 110, FGCLASS, 'olOptionsFgClass', CGCLASS, 'olOptionsCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olOptionsCapFontClass', CLOSEFONTCLASS, 'olOptionsCloseFontClass');\n";	
                      $code .= "{literal} } {/literal}\n";
                      $code .= "</script>\n";
                      $code .= $formatterCode;
                  }
                  return $code; 		  
              } //if-else 
        } //if
    }
    
    
    /**
     * getConnectorStrings
     * This method returns the language Strings for a given connector instance
     * 
     * @param String $source_id String value of the connector id to retrive language strings for
     * @param String $language optional String value for the language to use (defaults to $GLOBALS['current_language'])
     */
    public static function getConnectorStrings(
        $source_id, 
        $language = ''
        ) 
    {
        $lang = empty($language) ? $GLOBALS['current_language'] : $language;
        $lang .= '.lang.php';
        $dir = str_replace('_', '/', $source_id);
        if(file_exists("custom/modules/Connectors/connectors/sources/{$dir}/language/{$lang}")) {
            require("custom/modules/Connectors/connectors/sources/{$dir}/language/{$lang}");
            return !empty($connector_strings) ? $connector_strings : array();
        } else if(file_exists("modules/Connectors/connectors/sources/{$dir}/language/{$lang}")){
            require("modules/Connectors/connectors/sources/{$dir}/language/{$lang}");
            return !empty($connector_strings) ? $connector_strings : array();
        } else {
            $GLOBALS['log']->error("Unable to locate language string file for source {$source_id}");
            return array();
        }
    }
    
    
    /**
     * installSource
     * Install the name of the source (called from ModuleInstaller.php).  Modifies the files in the custom
     * directory to add the new source in.
     * 
     * @param String $source String value of the id of the connector to install
     * @return boolean $result boolean value indicating whether or not connector was installed
     */
    public static function installSource(
        $source
        ) 
    {
        if(empty($source)) {
           return false;	
        }
        //Add the source to the connectors.php file
        self::getConnectors(true);
        
        //Get the display config file
        self::getDisplayConfig();
        //Update the display_config.php file to show this new source
        $modules_sources = array();
        require(CONNECTOR_DISPLAY_CONFIG_FILE);
        foreach($modules_sources as $module=>$mapping) {
    
            foreach($mapping as $id=>$src) {
              if($src == $source) {
                 unset($modules_sources[$module][$id]);
                 break;
              }   	  
            }
        }
        
        //Make the directory for the config file
        if(!file_exists('custom/modules/Connectors/metadata')) {
           mkdir_recursive('custom/modules/Connectors/metadata');
        }
            
        if(!write_array_to_file('modules_sources', $modules_sources, CONNECTOR_DISPLAY_CONFIG_FILE)) {
           //Log error and return empty array
           $GLOBALS['log']->fatal("Cannot write \$modules_sources to " . CONNECTOR_DISPLAY_CONFIG_FILE);
        }
        return true;
    }
    
    
    /**
     * uninstallSource
     * 
     * @param String $source String value of the id of the connector to un-install
     * @return boolean $result boolean value indicating whether or not connector was un-installed
     */
    public static function uninstallSource(
        $source
        ) 
    {
        if(empty($source)) {
           return false;	
        }	
        
        //Remove the source from the connectors.php file
        self::getConnectors(true);
        
        //Update the display_config.php file to remove this source
        $modules_sources = array();
        require(CONNECTOR_DISPLAY_CONFIG_FILE);
        foreach($modules_sources as $module=>$mapping) {
            foreach($mapping as $id=>$src) {
                if($src == $source) {
                   unset($modules_sources[$module][$id]);
                }
            }	
        } 
        
        //Make the directory for the config file
        if(!file_exists('custom/modules/Connectors/metadata')) {
           mkdir_recursive('custom/modules/Connectors/metadata');
        }
            
        if(!write_array_to_file('modules_sources', $modules_sources, CONNECTOR_DISPLAY_CONFIG_FILE)) {
           //Log error and return empty array
           $GLOBALS['log']->fatal("Cannot write \$modules_sources to " . CONNECTOR_DISPLAY_CONFIG_FILE);
           return false;
        }    
        
    
        //BEGIN SUGARCRM flav=pro || flav=sales ONLY
    
        //Remove from searchdefs
        $searchdefs = ConnectorUtils::getSearchDefs();
        if(!empty($searchdefs[$source])) {
           unset($searchdefs[$source]);
        }
    
        if(!write_array_to_file('searchdefs', $searchdefs, 'custom/modules/Connectors/metadata/searchdefs.php')) {
           $GLOBALS['log']->fatal("Cannot write file custom/modules/Connectors/metadata/searchdefs.php");
           return false;
        }	    
    
        //END SUGARCRM flav=pro || flav=sales ONLY
    
        return true;
    }
    
    /**
     * hasWizardSourceEnabledForModule
     * This is a private method that returns a boolean value indicating whether or not at least one
     * source is enabled for a given module.  By enabled we mean that the source has the neccessary
     * configuration properties set as determined by the isRequiredConfigFieldsForButtonSet method.  In
     * addition, a check is made to ensure that it is a source that has been enabled for the wizard.
     * 
     * @param String $module String value of module to check
     * @return boolean $enabled boolean value indicating whether or not module has at least one source enabled
     */
    private static function hasWizardSourceEnabledForModule(
        $module = ''
        ) 
    {
        if(file_exists(CONNECTOR_DISPLAY_CONFIG_FILE)) {
           require_once('include/connectors/sources/SourceFactory.php');
           require(CONNECTOR_DISPLAY_CONFIG_FILE);
           if(!empty($modules_sources) && !empty($modules_sources[$module])) {
              foreach($modules_sources[$module] as $id) {
                   $source = SourceFactory::getSource($id, false);
                   if(!is_null($source) && $source->isEnabledInWizard() && $source->isRequiredConfigFieldsForButtonSet()) {
                      return true;
                   }         	
              }
           }
           return false;
        }
        return false;
    }
}