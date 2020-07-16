<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

class ProductTemplateTreeApi extends SugarApi
{

    public function registerApiRest()
    {
        return array(
            'tree' => array(
                'reqType' => 'GET',
                'path' => array('ProductTemplates', 'tree',),
                'pathVars' => array('module', 'type',),
                'method' => 'getTemplateTree',
                'shortHelp' => 'Returns a filterable tree structure of all Product Templates and Product Categories',
                'longHelp' => 'modules/ProductTemplates/clients/base/api/help/tree.html',
            ),
            'filterTree' => array(
                'reqType' => 'POST',
                'path' => array('ProductTemplates', 'tree',),
                'pathVars' => array('module', 'type',),
                'method' => 'getTemplateTree',
                'shortHelp' => 'Returns a filterable tree structure of all Product Templates and Product Categories',
                'longHelp' => 'modules/ProductTemplates/clients/base/api/help/tree.html',
            ),
        );
    }

    /**
     * Gets the full tree data in a jstree structure
     * @param ServiceBase $api
     * @param array $args
     * @return array
     * @throws SugarQueryException
     */
    public function getTemplateTree(ServiceBase $api, array $args)
    {
        $data = [];
        $tree = [];
        $records = [];
        $max_num = $this->getSugarConfig()->get('list_max_entries_per_page', 20);
        $offset = -1;
        $total = 0;
        $max_limit = $this->getSugarConfig()->get('max_list_limit');

        //set parameters
        if (array_key_exists('filter', $args)) {
            $data = $this->getTreeDataWithFilter($args['filter']);
        } elseif (array_key_exists('root', $args)) {
            $data = $this->getTreeDataWithRoot($args['root']);
        } else {
            $data = $this->getTreeDataWithRoot(null);
        }

        if (array_key_exists('offset', $args)) {
            $offset = $args['offset'];
        }

        //if the max_num is in-between 1 and $max_limit, set it, otherwise use max_limit
        if (array_key_exists('max_num', $args) && ($args['max_num'] < 1 || $args['max_num'] > $max_limit)) {
            $max_num = $max_limit;
        } elseif (array_key_exists('max_num', $args)) {
            $max_num = $args['max_num'];
        }

        // get total records in this set, calculate start position, slice data to current page
        $total = count($data);

        $offset = ($offset == -1) ? 0 : $offset;

        if ($offset < $total) {
            $data = array_slice($data, $offset, $max_num);
            
            //build the treedata
            foreach ($data as $node) {
                if ($this->checkContainsProduct($node)) {
                    //create new leaf
                    $records[] = $this->generateNewLeaf($node, $offset);
                }
                $offset++;
            }
        }

        if ($total <= $offset) {
            $offset = -1;
        }

        $tree['records'] = $records;
        $tree['next_offset'] = $offset;

        return $tree;
    }

    /**
     * Check if there are any child products/categories in order to show/hide the parent
     *
     * @param array $node
     * @return bool
     * @throws SugarQueryException
     */
    protected function checkContainsProduct($node)
    {
        if ($node['type'] === 'product') {
            return true;
        } elseif (!count($this->getTreeDataWithRoot($node['id']))) {
            return false;
        } else {
            $data = $this->getTreeDataWithRoot($node['id']);
            foreach ($data as $value) {
                if ($this->checkContainsProduct($value)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Create input array with given filter
     *
     * @param string $filter
     * @return array
     * @throws SugarQueryException
     */
    protected function getTreeDataWithFilter(string $filter)
    {
        $array = [
            'ProductCategories' => $filter,
            'ProductTemplates' => $filter,
        ];

        return $this->getTreeDataWithArray($array);
    }

    /**
     * Create input array with given root 'id'
     *
     * @param string $root
     * @return array
     * @throws SugarQueryException
     */
    protected function getTreeDataWithRoot(string $root = null)
    {
        $array = [
            'ProductCategories' => [
                'parent_id' => $root,
            ],
            'ProductTemplates' => [
                'category_id' => $root,
            ],
        ];

        return $this->getTreeDataWithArray($array);
    }

    /**
     * Get data using SugarQuery with the given input array
     *
     * @param array $input
     * @return array
     * @throws SugarQueryException
     */
    protected function getTreeDataWithArray(array $input = [])
    {
        $q = new SugarQuery();
        foreach ($input as $table => $value) {
            $bean = BeanFactory::newBean($table);
            if (!is_null($bean)) {
                if ($table === 'ProductCategories') {
                    $type = 'category';
                } elseif ($table === 'ProductTemplates') {
                    $type = 'product';
                }
                $query = new SugarQuery();
                $query->from($bean);
                $query->select(['id', 'name']);
                $query->select()->fieldRaw("'{$type}'", 'type');
                if (is_array($value)) {
                    foreach ($value as $key => $colValue) {
                        if (is_null($colValue)) {
                            $query->where()->isNull($key);
                        } else {
                            $query->where()->equals($key, $colValue);
                        }
                    }
                } else {
                    $query->where()->contains('name', $value);
                }
                 $q->union($query);
            }
        }
        $q->orderBy('type', 'ASC');
        $q->orderBy('name', 'ASC');
        
        return $q->execute();
    }

    /**
     * gets an instance of sugarconfig
     *
     * @return SugarConfig
     */
    protected function getSugarConfig()
    {
        return SugarConfig::getInstance();
    }

    /**
     * generates new leaf node
     * @param $node
     * @param $index
     * @return stdClass
     */
    protected function generateNewLeaf($node, $index)
    {
        $returnObj =  new \stdClass();
        $returnObj->id = $node['id'];
        $returnObj->type = $node['type'];
        $returnObj->data = $node['name'];
        $returnObj->state = ($node['type'] == 'product')? '' : 'closed';
        $returnObj->index = $index;

        return $returnObj;
    }

    /**
     * @deprecated
     * @param $filter
     * @return mixed[][]
     */
    protected function getFilteredTreeData($filter)
    {
        $filter = "%$filter%";
        $unionFilter = "and name like ? ";

        return $this->getTreeData($unionFilter, $unionFilter, [$filter, $filter]);
    }

    /**
     * @deprecated
     * @param $root
     * @return mixed[][]
     */
    protected function getRootedTreeData($root)
    {
        $union1Root = '';
        $union2Root = '';

        if ($root == null) {
            $union1Root = "and parent_id is null ";
            $union2Root = "and category_id is null ";
            $params = [];
        } else {
            $union1Root = "and parent_id = ? ";
            $union2Root = "and category_id = ? ";
            $params = [$root, $root];
        }

        return $this->getTreeData($union1Root, $union2Root, $params);
    }

    /**
     * Gets the tree data
     *
     * @deprecated
     * @param string $union1Filter
     * @param string $union2Filter
     * @param array $params Query parameters
     *
     * @return mixed[][]
     */
    protected function getTreeData($union1Filter, $union2Filter, array $params)
    {
        $q = "select id, name, 'category' as type from product_categories " .
            "where deleted = 0 " .
            $union1Filter .
            "union all " .
            "select id, name, 'product' as type from product_templates " .
            "where deleted = 0 " .
            $union2Filter .
            "order by type, name";

        $conn = $this->getDBConnection();
        $stmt = $conn->prepare($q);

        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Gets the DB connection for the query
     * @return \Sugarcrm\Sugarcrm\Dbal\Connection
     */
    public function getDBConnection()
    {
        $db = DBManagerFactory::getInstance();
        return $db->getConnection();
    }
}
