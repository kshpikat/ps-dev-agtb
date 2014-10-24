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
$viewdefs['base']['view']['dashlet-nestedset-list'] = array(
    'dashlets' => array(
        array(
            'label' => 'LBL_DASHLET_TOPICS_NAME',
            'description' => 'LBL_DASHLET_TOPICS_DESCRIPTION',
            'config' => array(
                'last_state' => array(
                    'id' => 'dashlet-nestedset-list-kbscontents',
                ),
                'data_provider' => 'Categories',
                'config_provider' => 'KBSContents',
                'root_name' => 'category_root',
                'extra_provider' => array(
                    'module' => 'KBSContents',
                    'field' => 'category_id',
                ),
            ),
            'preview' => array(
                'data_provider' => 'Categories',
                'config_provider' => 'KBSContents',
                'root_name' => 'category_root',
            ),
            'filter' => array(
                'module' => array(
                    'KBSContents',
                    'KBSContentTemplates',
                ),
                'view' => array(
                    'record',
                    'records',
                ),
            ),
        )
    ),
    'config' => array (
    ),
);
