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
 *Portions created by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights
 *Reserved.
 ********************************************************************************/
require_once('include/SugarSearchEngine/Interface.php');

abstract class SugarSearchEngineAbstractBase implements SugarSearchEngineInterface
{
    /**
     * @var array
     */
    protected $_documents = array();

    /**
     * The max number of documents to bulk insert at a time
     */
    const MAX_BULK_THRESHOLD = 100;

    /**
     *
     */
    const ENABLE_MODULE_CACHE_KEY = 'ftsEnabledModules';

    /**
     *
     */
    const DISABLED_MODULE_CACHE_KEY = 'ftsDisabledModules';


    public function __construct()
    {
        $this->cacheFtsModulesFile = sugar_cached('modules/ftsModulesCache.php');
    }
    /**
     * For a given module, return all of the full text search enabled fields.
     *
     * @param $module
     *
     */
    public function retrieveFtsEnabledFieldsPerModule($module)
    {
        $results = array();
        if( is_string($module))
        {
            $obj = BeanFactory::getBean($module, null);

        }
        else if( is_a($module, 'SugarBean') )
        {
            $obj = $module;
        }
        else
        {
            return $results;
        }

        $cacheKey = "fts_fields_{$obj->table_name}";
        $cacheResults = sugar_cache_retrieve($cacheKey);
        if(!empty($cacheResults))
            return $cacheResults;

        foreach($obj->field_defs as $field => $def)
        {
            if( isset($def['full_text_search']) && is_array($def['full_text_search']) && !empty($def['full_text_search']['boost']) )
                $results[$field] = $def;
        }

        sugar_cache_put($cacheKey, $results);
        return $results;

    }

    /**
     * Retrieve all FTS fields for all FTS enabled modules.
     *
     * @return array
     */
    public function retrieveFtsEnabledFieldsForAllModules()
    {
        $cachedResults = sugar_cache_retrieve(self::ENABLE_MODULE_CACHE_KEY);
        if($cachedResults != null && !empty($cachedResults) )
        {
            $GLOBALS['log']->fatal("Retrieving enabled fts modules from cache");
            return $cachedResults;
        }
        $results = array();
        foreach( $GLOBALS['moduleList'] as $moduleName )
        {
            $fields = $this->retrieveFtsEnabledFieldsPerModule($moduleName);
            if( !empty($fields) && $this->isModuleFtsEnabled($moduleName) )
                $results[$moduleName] = $fields;
        }

        //write_array_to_file('cacheFtsModulesFile', $results, $this->cacheFtsModulesFile);
        sugar_cache_put(self::ENABLE_MODULE_CACHE_KEY, $results);
        return $results;
    }

    /**
     * @return array
     */
    public function getModulesByFTSStatus()
    {
        $disabledModules = $this->getDisabledFTSModules();
        $enabledModules = array_keys($this->retrieveFtsEnabledFieldsForAllModules());
        $enabledModulesTranslated = array();
        $disabledModulesTranslated = array();
        foreach($enabledModules as $m)
        {
            $moduleName = isset($GLOBALS['app_list_strings']['moduleList'][$m]) ? $GLOBALS['app_list_strings']['moduleList'][$m] : $m;
            $enabledModulesTranslated[] = array('module'=> $m, 'label' => $moduleName);
        }
        foreach($disabledModules as $m)
        {
            $moduleName = isset($GLOBALS['app_list_strings']['moduleList'][$m]) ? $GLOBALS['app_list_strings']['moduleList'][$m] : $m;
            $disabledModulesTranslated[] = array('module'=> $m, 'label' => $moduleName);
        }
        asort($enabledModules);
        asort($disabledModulesTranslated);
        return array('enabled' => $enabledModulesTranslated, 'disabled' => $disabledModulesTranslated);

    }
    /**
     * @return bool|The
     */
    public function getDisabledFTSModules()
    {
        $cachedResults = sugar_cache_retrieve(self::DISABLED_MODULE_CACHE_KEY);
        if($cachedResults != null && !empty($cachedResults) )
        {
            $GLOBALS['log']->fatal("Retrieving disabled fts modules from cache");
            return $cachedResults;
        }
        $GLOBALS['log']->fatal("Could not resolve fts module cache, loading from file....");
        if( file_exists($this->cacheFtsModulesFile) )
        {
            include($this->cacheFtsModulesFile);
            return $ftsDisabledModules;
        }
        return false;
    }
    /**
     * Determine if a module is FTS enabled.
     *
     * @param $module
     * @return bool
     */
    protected function isModuleFtsEnabled($module)
    {
        $GLOBALS['log']->fatal("Checking if module is fts enabled");
        $disabledModules = $this->getDisabledFTSModules();
        if( empty($disabledModules) )
            return TRUE;

        return !in_array($module, $disabledModules);

    }

    /**
     * Bulk insert any documents that have been marked for bulk insertion.
     */
    public function __destruct()
    {
        if (count($this->_documents) > 0 )
        {
            $this->bulkInsert($this->_documents);
        }

    }
}