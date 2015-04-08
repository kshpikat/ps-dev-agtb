<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

use Sugarcrm\Sugarcrm\SearchEngine\SearchEngine;
use Sugarcrm\Sugarcrm\SearchEngine\Capability\GlobalSearch\GlobalSearchCapable;
use Sugarcrm\Sugarcrm\Elasticsearch\Adapter\ResultSet;

require_once 'include/SugarSearchEngine/SugarSearchEngineAbstractBase.php';
require_once 'include/SugarSearchEngine/Elastic/SugarSearchEngineElasticResultSet.php';

/**
 *
 * Wrapper around new GlobalSearch framework, replaces previous logic.
 *
 *                      !!! DEPRECATION WARNING !!!
 *
 * All code in include/SugarSearchEngine is going to be deprecated in a future
 * release. Do not use any of its APIs for code customizations as there will be
 * no guarantee of support and/or functionality for it. Use the new framework
 * located in the directories src/SearchEngine and src/Elasticsearch.
 *
 * @deprecated
 */
class SugarSearchEngineElastic extends SugarSearchEngineAbstractBase
{
    /**
     * @var GlobalSearchCapable
     */
    protected $engine;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Ctor
     * @param array $options
     * @param GlobalSearchCapable $engine
     * @param LoggerManager $logger
     */
    public function __construct($options = array(), GlobalSearchCapable $engine = null, LoggerManager $logger = null)
    {
        $this->options = $options;
        $this->engine = $engine ?: SearchEngine::getInstance('GlobalSearch')->getEngine();
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     * @return null|SugarSeachEngineElasticResultSet
     */
    public function search($query, $offset = 0, $limit = 20, $options = array())
    {
        if (!$this->engine->isAvailable()) {
            return;
        }

        $this->engine->term($query);
        $this->engine->offset($offset);
        $this->engine->limit($limit);
        $this->engine->highlighter(true);
        $this->engine->fieldBoost(true);

        // set module filter
        if (!empty($options['moduleFilter'])) {
            $this->engine->from($options['moduleFilter']);
        }

        // TODO - my items
        if (isset($options['my_items']) && $options['my_items'] !== false) {
        }

        // TODO - range filter
        if (isset($options['filter']) && $options['filter']['type'] == 'range') {
        }

        // TODO - favorites filter
        if (isset($options['favorites']) && $options['favorites'] == 2) {
        }

        // TODO - sort options
        if (isset($options['sort']) && is_array($options['sort'])) {
            foreach ($options['sort'] as $sort) {
            }
        }

        return $this->createResultSet($this->engine->search());
    }

    /**
     * Wrapper method transforming ResultSet into old format
     * @param ResultSet $resultSet
     * @return SugarSeachEngineElasticResult
     */
    protected function createResultSet(ResultSet $resultSet)
    {
        return new SugarSeachEngineElasticResultSet($resultSet->getResultSet());
    }

    /**
     * {@inheritdoc}
     */
    public function indexBean($bean, $batch = true)
    {
        $this->logger->deprecated('SugarSearchEngineElastic::indexBean is deprecated and no longer available');
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SugarBean $bean)
    {
        $this->logger->deprecated('SugarSearchEngineElastic::delete is deprecated and no longer available');
    }

    /**
     * {@inheritdoc}
     */
    public function bulkInsert(array $docs)
    {
        $this->logger->deprecated('SugarSearchEngineElastic::bulkInsert is deprecated and no longer available');
    }

    /**
     * {@inheritdoc}
     */
    public function createIndexDocument($bean, $searchFields = null)
    {
        $this->logger->deprecated('SugarSearchEngineElastic::createIndexDocument is deprecated and no longer available');
    }

    /**
     * {@inheritdoc}
     */
    public function getServerStatus()
    {
        global $app_strings, $sugar_config;
        $isValid = $this->engine->isAvailable(true);
        $status = $isValid ? $app_strings['LBL_EMAIL_SUCCESS'] : $app_strings['ERR_ELASTIC_TEST_FAILED'];
        return array('valid' => $isValid, 'status' => $status);
    }

    /**
     * {@inheritdoc}
     */
    public function createIndex($recreate = false, $modules = array())
    {
        $this->logger->deprecated('SugarSearchEngineElastic::createIndex is deprecated and no longer available');
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeFtsEnabled($type)
    {
        $this->logger->deprecated('SugarSearchEngineElastic::isTypeFtsEnabled is deprecated');
        return in_array($type, $this->engine->getSupportedTypes());
    }
}
