<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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


require_once 'data/SugarVisibility.php';

/**
 * Bean visibility manager
 * @api
 */
class BeanVisibility
{
    /**
     * List of strategies to apply to this bean
     * @var array
     */
    protected $strategies = array();

    /**
     * Parent bean
     * @var SugarBean
     */
    protected $bean;

    /**
     * Loaded Strategies
     * @var array
     */
    protected $loadedStrategies = array();

    /**
     * @param SugarBean $bean
     * @param array $metadata
     */
    public function __construct(SugarBean $bean, array $metadata)
    {
        $this->bean = $bean;
        foreach ($metadata as $visclass => $data) {
            if ($data === false) {
                continue;
            }
            $this->addStrategy($visclass, $data);
        }
    }

    /**
     * Add the strategy to the list
     * @param string $strategy Strategy class name
     * @param mixed $data Strategy params
     */
    public function addStrategy($strategy, $data = null)
    {
        $this->strategies[] = new $strategy($this->bean, $data);
        /*
         *  because PHP will allow $strategy to be an object and instantiate a new version of
         *  itself in the above line we need to check if it is an object before we save it to the
         *  loadedStrategies array
         */
        $strategyName = is_object($strategy) ? get_class($strategy) : $strategy;
        $this->loadedStrategies[$strategyName] = true;
    }

    /**
     * Add visibility clauses to the FROM part of the query
     * @param string $query
     * @param array $options
     * @return string Modified query
     */
    public function addVisibilityFrom(&$query, array $options = array())
    {
        foreach ($this->strategies as $strategy) {
            $strategy->setOptions($options)->addVisibilityFrom($query);
        }
        return $query;
    }

    /**
     * Add visibility clauses to the WHERE part of the query
     * @param string $query
     * @param array $options
     * @return string Modified query
     */
    public function addVisibilityWhere(&$query, array $options = array())
    {
        foreach($this->strategies as $strategy) {
            $strategy->setOptions($options)->addVisibilityWhere($query);
        }
        return $query;
    }

    /**
     * Add visibility clauses to the FROM part of SugarQuery
     * @param SugarQuery $query
     * @param array $options
     * @return SugarQuery Mofidied SugarQuery
     */
    public function addVisibilityFromQuery(SugarQuery $query, array $options = array())
    {
        foreach($this->strategies as $strategy) {
            $strategy->setOptions($options)->addVisibilityFromQuery($query);
        }
        return $query;
    }

    /**
     * Add visibility clauses to the WHERE part of SugarQuery
     * @param SugarQuery $query
     * @param array $options
     * @return SugarQuery Mofidied SugarQuery
     */
    public function addVisibilityWhereQuery(SugarQuery $query, array $options = array())
    {
        foreach($this->strategies as $strategy) {
            $strategy->setOptions($options)->addVisibilityWhereQuery($query);
        }
        return $query;
    }

    /**
     * Check if the Strategy has been loaded
     * @param string $name
     * @return boolean
     */
    public function isLoaded($name)
    {
        return isset($this->loadedStrategies[$name]);
    }

    /**
     * Called before the bean is indexed so that any calculated attributes can updated.
     * Propagates to all registered strategies.
     * @return void
     */
    public function beforeSseIndexing()
    {
        foreach ($this->strategies as $strategy) {
            $strategy->beforeSseIndexing();
        }
    }

    /**
     * Apply SugarSearchEngine visibility filters.
     * @param SugarSearchEngineInterface $engine Sugar search engine object
     * @param mixed $filter Current filter used as base
     * @return mixed
     *
     * FIXME: $filter is tightly coupled to Elasticsearch
     */
    public function addSseVisibilityFilter(SugarSearchEngineInterface $engine, $filter)
    {
        foreach ($this->strategies as $strategy) {
            $filter = $strategy->addSseVisibilityFilter($engine, $filter);
        }
        return $filter;
    }
}

