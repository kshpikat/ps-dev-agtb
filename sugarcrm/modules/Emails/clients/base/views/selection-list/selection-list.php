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
$viewdefs['Emails']['base']['view']['selection-list'] = array(
    'panels' => array(
        array(
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_1',
            'fields' => array(
                array(
                    'name' => 'from',
                    'type' => 'from',
                    'label' => 'LBL_LIST_FROM_ADDR',
                    'enabled' => true,
                    'default' => true,
                    'readonly' => true,
                    'fields' => array('name', 'email_address_used', 'email'),
                ),
                array(
                    'name' => 'name',
                    'enabled' => true,
                    'default' => true,
                    'link' => 'true'
                ),
                array(
                    'name' => 'status',
                    'label' => 'LBL_LIST_STATUS',
                    'enabled' => true,
                    'default' => true,
                ),
                array(
                    'name' => 'date_sent',
                    'label' => 'LBL_LIST_DATE_COLUMN',
                    'enabled' => true,
                    'default' => true,
                ),
                array(
                    'name' => 'assigned_user_name',
                    'target_record_key' => 'assigned_user_id',
                    'target_module' => 'Employees',
                    'enabled' => true,
                    'default' => true,
                ),
                array(
                    'name' => 'parent_name',
                    'link' => true,
                    'enabled' => true,
                    'default' => true,
                    'sortable' => false,
                ),
            ),
        ),
    ),
);
