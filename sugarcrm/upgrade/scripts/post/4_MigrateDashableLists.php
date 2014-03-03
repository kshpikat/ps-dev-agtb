<?php

/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc. All rights reserved.
 */

/**
 * This class does the migration of dashable lists for versions >= 7.0 to 7.2.

 * This migration consists of removing the `my_items` and `favorites` flags and
 * create a new flag called `filter_id` which will have its value based on the
 * combination of the values of the latter.
 *
 * @see SugarUpgradeMigrateDashableLists::updateView()
 *
 */
class SugarUpgradeMigrateDashableLists extends UpgradeScript
{
    public $order = 4000;
    public $type = self::UPGRADE_DB;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (version_compare($this->from_version, '7.0', '>=') &&
            version_compare($this->from_version, '7.2', '<')
        ) {
            $this->migrateDashableLists();
        }
    }

    /**
     * Performs dashable lists migration.
     */
    public function migrateDashableLists()
    {
        $result = $this->db->query('SELECT id, metadata FROM dashboards WHERE deleted=0');

        while ($dashboard = $this->db->fetchByAssoc($result, false)) {
            $metadata = json_decode($dashboard['metadata']);

            if (!property_exists($metadata, 'components')) {
                continue;
            }

            $views = $this->getDashableListsViews($metadata);
            foreach ($views as $view) {
                $this->updateView($view);
            }

            $metadata = json_encode($metadata);
            if ($metadata === $dashboard['metadata']) {
                continue;
            }

            $this->db->query(
                sprintf(
                    "UPDATE dashboards SET metadata='%s' WHERE id='%s'",
                    $metadata,
                    $this->db->quote($dashboard['id'])
                )
            );
        }
    }

    /**
     * Retrieve dashable lists views based on supplied dashboard metadata.
     *
     * @param object $metadata Dashboard metadata.
     * @return array Array of dashable lists views.
     */
    private function getDashableListsViews($metadata)
    {
        $views = array();
        foreach ($metadata->components as $component) {
            if (!property_exists($component, 'rows')) {
                continue;
            }

            foreach ($component->rows as $rows) {

                foreach ($rows as $row) {
                    if (!property_exists($row, 'view')) {
                        continue;
                    }

                    $view = $row->view;
                    if (!property_exists($view, 'type') || $view->type !== 'dashablelist') {
                        continue;
                    }

                    $views[] = $view;
                }
            }
        }

        return $views;
    }

    /**
     * Update dashable list view, based on the following rules:
     *
     * my_items = 1 && favorites = 0 => filter_id = 'assigned_to_me'
     * my_items = 1 && favorites = 1 => filter_id = 'assigned_to_me'
     * my_items = 0 && favorites = 1 => filter_id = 'favorites'
     * my_items = 0 && favorites = 0 => filter_id = 'all_records'
     *
     * @param object $view Dashable list view.
     */
    public function updateView($view)
    {
        if (!property_exists($view, 'my_items') && !property_exists($view, 'favorites')) {
            return;
        }

        if (empty($view->my_items) && empty($view->favorites)) {
            $view->filter_id = 'all_records';

        } elseif ($view->my_items == 1) {
            $view->filter_id = 'assigned_to_me';

        } else {
            $view->filter_id = 'favorites';
        }

        unset($view->my_items, $view->favorites);
    }
}
