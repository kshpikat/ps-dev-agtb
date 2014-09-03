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

require_once 'clients/base/api/FilterApi.php';

class TreeApi extends FilterApi
{

    /**
     * Depth of the tree by default.
     *
     * @var integer
     */
    public $defaultTreeDepth = 5;

    public function registerApiRest()
    {
        return array(
            'filterModuleSubTree' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'tree', '?'),
                'pathVars' => array('module', 'record', '', 'link_name'),
                'method' => 'filterSubTree',
            ),
            'filterModuleTree' => array(
                'reqType' => 'GET',
                'path' => array('<module>', 'tree', '?'),
                'pathVars' => array('module', '', 'link_name'),
                'method' => 'filterTree',
            ),
            'roots' => array(
                'reqType' => 'GET',
                'path' => array('<module>', 'tree', 'roots'),
                'pathVars' => array('module', '', ''),
                'method' => 'roots',
                'shortHelp' => 'This method returns all root nodes',
                'longHelp' => 'include/api/help/tree_get_roots_help.html',
            ),
            'tree' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'tree'),
                'pathVars' => array('module', 'root', ''),
                'method' => 'tree',
                'shortHelp' => 'This method returns formatted tree for selected root',
                'longHelp' => 'include/api/help/tree_get_tree_help.html',
            ),
            'children' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'children'),
                'pathVars' => array('module', 'record', ''),
                'method' => 'children',
                'shortHelp' => 'This method returns children categories for selected record',
                'longHelp' => 'include/api/help/tree_get_children_help.html',
            ),
            'next' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'next'),
                'pathVars' => array('module', 'record', ''),
                'method' => 'next',
                'shortHelp' => 'This method returns next sibling of selected record',
                'longHelp' => 'include/api/help/tree_get_next_help.html',
            ),
            'prev' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'prev'),
                'pathVars' => array('module', 'record', ''),
                'method' => 'prev',
                'shortHelp' => 'This method returns previous sibling of selected record',
                'longHelp' => 'include/api/help/tree_get_prev_help.html',
            ),
            'parent' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'parent'),
                'pathVars' => array('module', 'record', ''),
                'method' => 'getParent',
                'shortHelp' => 'This method returns parent node of selected record',
                'longHelp' => 'include/api/help/tree_get_parent_help.html',
            ),
            'path' => array(
                'reqType' => 'GET',
                'path' => array('<module>', '?', 'path'),
                'pathVars' => array('module', 'record', ''),
                'method' => 'path',
                'shortHelp' => 'This method returns full path of selected record',
                'longHelp' => 'include/api/help/tree_get_path_help.html',
            ),
            'movebefore' => array(
                'reqType' => 'PUT',
                'path' => array('<module>', '?', 'movebefore', '?'),
                'pathVars' => array('module', 'record', 'movebefore', 'target'),
                'method' => 'moveBefore',
                'shortHelp' => 'This method record as previous sibling of target',
                'longHelp' => 'include/api/help/tree_put_movebefore_help.html',
            ),
            'moveafter' => array(
                'reqType' => 'PUT',
                'path' => array('<module>', '?', 'moveafter', '?'),
                'pathVars' => array('module', 'record', 'moveafter', 'target'),
                'method' => 'moveAfter',
                'shortHelp' => 'This method record as next sibling of target',
                'longHelp' => 'include/api/help/tree_put_moveafter_help.html',
            ),
            'movefirst' => array(
                'reqType' => 'PUT',
                'path' => array('<module>', '?', 'movefirst', '?'),
                'pathVars' => array('module', 'record', 'movefirst', 'target'),
                'method' => 'moveFirst',
                'shortHelp' => 'This method record as as first child of target',
                'longHelp' => 'include/api/help/tree_put_movefirst_help.html',
            ),
            'movelast' => array(
                'reqType' => 'PUT',
                'path' => array('<module>', '?', 'movelast', '?'),
                'pathVars' => array('module', 'record', 'movelast', 'target'),
                'method' => 'moveLast',
                'shortHelp' => 'This method record as as last child of target',
                'longHelp' => 'include/api/help/tree_put_movelast_help.html',
            ),
            'append' => array(
                'reqType' => 'POST',
                'path' => array('<module>', 'append', '?'),
                'pathVars' => array('module', 'append', 'target'),
                'method' => 'append',
                'shortHelp' => 'This method append record to target as last child',
                'longHelp' => 'include/api/help/tree_post_append_help.html',
            ),
            'prepend' => array(
                'reqType' => 'POST',
                'path' => array('<module>', 'prepend', '?'),
                'pathVars' => array('module', 'prepend', 'target'),
                'method' => 'prepend',
                'shortHelp' => 'This method prepend record to target as first child',
                'longHelp' => 'include/api/help/tree_post_prepend_help.html',
            ),
            'insertbefore' => array(
                'reqType' => 'POST',
                'path' => array('<module>', 'insertbefore', '?'),
                'pathVars' => array('module', 'insertbefore', 'target'),
                'method' => 'insertBefore',
                'shortHelp' => 'This method insert record as previous sibling of target',
                'longHelp' => 'include/api/help/tree_post_insertbefore_help.html',
            ),
            'insertafter' => array(
                'reqType' => 'POST',
                'path' => array('<module>', 'insertafter', '?'),
                'pathVars' => array('module', 'insertafter', 'target'),
                'method' => 'insertAfter',
                'shortHelp' => 'This method insert record as next sibling of target',
                'longHelp' => 'include/api/help/tree_post_insertafter_help.html',
            ),
        );
    }
    
    /**
     * This method loading and returns bean.
     * @param string $module
     * @param string $id bean id
     * @return SugarBean
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotFound
     */
    protected function retrieveBean($module, $id = null)
    {
        $bean = BeanFactory::retrieveBean($module, $id);
        
        if (false === ($bean instanceof NestedBeanInterface)) {
            throw new SugarApiExceptionInvalidParameter('Requested module "' . $module . '" should be instance of NestedBeanInterface');
        }
        
        if (null === $bean || $bean->deleted == 1) {
            throw new SugarApiExceptionNotFound('Could not find record in module: ' . $module);
        }
        
        return $bean;
    }

    /**
     * This method loading and returns beans that should be bound in the tree.
     * @param string $module
     * @param string $subject subject bean id
     * @param string $target target bean id
     * @return array SugarBean An array of SugarBeans that can bound in tree.
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionNotFound
     * @throws SugarApiException
     */
    protected function loadBoundBeans($module, $subject, $target)
    {
        $bean = $this->retrieveBean($module, $subject);
        $target = $this->retrieveBean($module, $target);

        if (null === $bean || $bean->deleted == 1) {
            throw new SugarApiExceptionNotFound('Could not find record: ' . $args['record'] . ' in module: ' . $args['module']);
        }

        if (null === $target || $target->deleted == 1) {
            throw new SugarApiExceptionNotFound('Could not find record: ' . $args['target'] . ' in module: ' . $args['module']);
        }

        if ($bean->id === $target->id) {
            throw new SugarApiException('The target node should not be self.');
        }

        if ($target->isDescendantOf($bean)) {
            throw new SugarApiException('The target node should not be descendant.');
        }

        return array($bean, $target);
    }

    /**
     * This method creates and populate from API new bean
     * @param array $api
     * @param array $args
     * @return SugarBean 
     * @throws SugarApiExceptionInvalidParameter
     * @throws SugarApiExceptionEditConflict
     */
    protected function createNewBean($api, $args)
    {
        $bean = BeanFactory::newBean($args['module']);
        
        if (false === ($bean instanceof NestedBeanInterface)) {
            throw new SugarApiExceptionInvalidParameter('Requested module "' . $args['module'] . '" should be instance of NestedBeanInterface');
        }

        try {
            $errors = ApiHelper::getHelper($api, $bean)->populateFromApi($bean, $args);
        } catch (SugarApiExceptionEditConflict $conflict) {
            $api->action = 'view';
            $data = $this->formatBean($api, $args, $bean);
            // put current state of the record on the exception
            $conflict->setExtraData("record", $data);
            throw $conflict;
        }
        $bean->id = null;
        return $bean;
    }

    /**
     * This method prepend record to target as first child.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return id Bean id
     * @throws SugarApiExceptionNotFound
     */
    public function prepend($api, $args)
    {
        $this->requireArgs($args, array('module', 'target'));
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $target->prepend($bean);
        return $this->updateBean($bean, $api, $args);
    }

    /**
     * This method append record to target as last child.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return id Bean id
     * @throws SugarApiExceptionNotFound
     */
    public function append($api, $args)
    {
        $this->requireArgs($args, array('module', 'target'));
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $target->append($bean);
        return $this->updateBean($bean, $api, $args);
    }

    /**
     * This method insert record as previous sibling of target.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return id Bean id
     * @throws SugarApiExceptionNotFound
     */
    public function insertBefore($api, $args)
    {
        $this->requireArgs($args, array('module', 'target'));
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $bean->insertBefore($target);
        return $this->updateBean($bean, $api, $args);
    }

    /**
     * This method insert record as next sibling of target.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return id Bean id
     * @throws SugarApiExceptionNotFound
     */
    public function insertAfter($api, $args)
    {
        $this->requireArgs($args, array('module', 'target'));
        $bean = $this->createNewBean($api, $args);
        $api->action = 'save';
        $target = $this->retrieveBean($args['module'], $args['target']);
        $bean->insertAfter($target);
        return $this->updateBean($bean, $api, $args);
    }

    /**
     * This method record as previous sibling of target.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL)
     */
    public function moveBefore($api, $args)
    {
        $this->requireArgs($args, array('module', 'record', 'target'));
        list ($bean, $target) = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveBefore($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method record as next sibling of target.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL)
     */
    public function moveAfter($api, $args)
    {
        $this->requireArgs($args, array('module', 'record', 'target'));
        list ($bean, $target) = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveAfter($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method record as as first child of target.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array An array version of the SugarBean with only the requested fields (also filtered by ACL)
     */
    public function moveFirst($api, $args)
    {
        $this->requireArgs($args, array('module', 'record', 'target'));
        list ($bean, $target) = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveAsFirst($target);
        return $this->formatBean($api, $args, $bean);
    }

    /**
     * This method record as as last child of target.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array An array version of the SugarBean
     */
    public function moveLast($api, $args)
    {
        $this->requireArgs($args, array('module', 'record', 'target'));
        list ($bean, $target) = $this->loadBoundBeans($args['module'], $args['record'], $args['target']);
        $bean->moveAsLast($target);
        return $this->formatBean($api, $args, $bean);
    }
    
    /**
     * This method returns all root nodes.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array list of root nodes
     */
    public function roots($api, $args)
    {
        $this->requireArgs($args, array('module'));
        $seed = $this->retrieveBean($args['module']);
        return $seed->getRoots();
    }

    /**
     * This method returns formatted tree for selected root.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array hierarchy of nodes
     */
    public function tree($api, $args)
    {
        $this->requireArgs($args, array('module', 'root'));
        $seed = $this->retrieveBean($args['module'], $args['root']);
        return $seed->getTree();
    }

    /**
     * This method returns children nodes for selected record.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array list of children nodes
     */
    public function children($api, $args)
    {
        $this->requireArgs($args, array('module', 'record'));
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $record->getСhildren(1);
    }

    /**
     * This method returns next sibling of selected record.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array node data
     */
    public function next($api, $args)
    {
        $this->requireArgs($args, array('module', 'record'));
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $record->getNextSibling();
    }

    /**
     * This method returns previous sibling of selected record.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array node data
     */
    public function prev($api, $args)
    {
        $this->requireArgs($args, array('module', 'record'));
        $record = $this->retrieveBean($args['module'], $args['record']);
        return $record->getPrevSibling();
    }

    /**
     * This method returns parent node of selected record.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array node data
     */
    public function getParent($api, $args)
    {
        $this->requireArgs($args, array('module', 'record'));
        $bean = $this->retrieveBean($args['module'], $args['record']);
        return $bean->getParent();
    }

    /**
     * This method returns full path of selected record.
     * @param Object $api api object
     * @param Array $args arguments passed from api
     * @return array list of parent nodes
     */
    public function path($api, $args)
    {
        $this->requireArgs($args, array('module', 'record'));
        $record = $this->retrieveBean($args['module'], $args['record']);
        return array_reverse($record->getParents());
    }

    protected function parseArguments(ServiceBase $api, array $args, SugarBean $seed = null)
    {
        $options = parent::parseArguments($api, $args, $seed);
        // Set up the defaults
        $options['depth'] = $this->defaultTreeDepth;

        if (!empty($args['depth'])) {
            $options['depth'] = (int) $args['depth'];
        }
        return $options;
    }

    protected function runQuery(ServiceBase $api, array $args, SugarQuery $q, array $options, SugarBean $seed)
    {
        $data = parent::runQuery($api, $args, $q, $options, $seed);

        if ($options['depth'] > 0) {
            $options['depth'] --;
            foreach ($data['records'] as $i => $row) {
                $record = $seed->getCleanCopy();
                $record->loadFromRow($row, true);

                $q = self::getQueryObject($seed, $options);
                $q->joinSubpanel($record, $args['link_name'], array(
                    'joinType' => 'INNER',
                    'ignoreRole' => !empty($args['ignore_role'])
                ));
                self::addFilters($args['filter'], $q->where(), $q);

                $data['records'][$i][$args['link_name']] = $this->runQuery($api, $args, $q, $options, $seed);
            }
        }
        return $data;
    }

    public function filterSubTree($api, $args)
    {
        $this->requireArgs($args, array('module', 'record', 'link_name'));
        // Load the parent bean.
        $record = BeanFactory::retrieveBean($args['module'], $args['record']);

        if (empty($record)) {
            throw new SugarApiExceptionNotFound(
            sprintf(
                    'Could not find parent record %s in module: %s', $args['record'], $args['module']
            )
            );
        }
        if (!$record->ACLAccess('view')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $args['module']);
        }
        // Load the relationship.
        $linkName = $args['link_name'];
        if (!$record->load_relationship($linkName)) {
            // The relationship did not load.
            throw new SugarApiExceptionNotFound('Could not find a relationship named: ' . $args['link_name']);
        }
        $linkModuleName = $record->$linkName->getRelatedModuleName();
        if ($linkModuleName != $record->module_name) {
            throw new SugarApiExceptionNotFound('Could not find self referencing in relationship named: ' . $linkName);
        }
        $linkSeed = BeanFactory::getBean($linkModuleName);
        if (!$linkSeed->ACLAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to list records for module: ' . $linkModuleName);
        }
        $options = $this->parseArguments($api, $args, $linkSeed);

        // If they don't have fields selected we need to include any link fields
        // for this relationship
        if (empty($args['fields']) && is_array($linkSeed->field_defs)) {
            $relatedLinkName = $record->$linkName->getRelatedModuleLinkName();
            $options['linkDataFields'] = array();
            foreach ($linkSeed->field_defs as $field => $def) {
                if (empty($def['rname_link']) || empty($def['link'])) {
                    continue;
                }
                if ($def['link'] != $relatedLinkName) {
                    continue;
                }
                // It's a match
                $options['linkDataFields'][] = $field;
                $options['select'][] = $field;
            }
        }

        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = array();
        }

        $q = self::getQueryObject($linkSeed, $options);
        $q->joinSubpanel($record, $linkName, array(
            'joinType' => 'INNER',
            'ignoreRole' => !empty($args['ignore_role'])
        ));
        self::addFilters($args['filter'], $q->where(), $q);

        return $this->runQuery($api, $args, $q, $options, $linkSeed);
    }

    public function filterTree($api, $args)
    {
        $this->requireArgs($args, array('module', 'link_name'));
        // Load up a seed bean
        $seed = BeanFactory::getBean($args['module']);
        if (!$seed->ACLAccess('list')) {
            throw new SugarApiExceptionNotAuthorized('No access to view records for module: ' . $args['module']);
        }
        // Load the relationship.
        $linkName = $args['link_name'];
        if (!$seed->load_relationship($linkName)) {
            // The relationship did not load.
            throw new SugarApiExceptionNotFound('Could not find a relationship named: ' . $linkName);
        }
        $linkModuleName = $seed->$linkName->getRelatedModuleName();
        if ($linkModuleName != $seed->module_name) {
            throw new SugarApiExceptionNotFound('Could not find self referencing in relationship named: ' . $linkName);
        }
        $options = $this->parseArguments($api, $args, $seed);

        // If they don't have fields selected we need to include any link fields
        // for this relationship
        if (empty($args['fields']) && is_array($seed->field_defs)) {
            $relatedLinkName = $seed->$linkName->getRelatedModuleLinkName();
            $options['linkDataFields'] = array();

            foreach ($seed->field_defs as $field => $def) {
                if (empty($def['rname_link']) || empty($def['link'])) {
                    continue;
                }
                if ($def['link'] != $relatedLinkName) {
                    continue;
                }
                // It's a match
                $options['linkDataFields'][] = $field;
                $options['select'][] = $field;
            }
        }
        if (!isset($args['filter']) || !is_array($args['filter'])) {
            $args['filter'] = array();
        }

        $q = self::getQueryObject($seed, $options);

        if ($seed->$linkName->getSide() == REL_LHS) {
            $q->where()->isNull($seed->$linkName->getRelationshipObject()->def['rhs_key'], $seed);
        } else {
            $q->where()->isNull($seed->$linkName->getRelationshipObject()->def['lhs_key'], $seed);
        }

        self::addFilters($args['filter'], $q->where(), $q);

        return $this->runQuery($api, $args, $q, $options, $seed);
    }

}
