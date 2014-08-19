<?php
// FILE SUGARCRM flav=ent ONLY
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
$viewdefs['Opportunities']['base']['view']['config-opps-view-by'] = array(
    'label' => 'LBL_OPPS_CONFIG_VIEW_BY_LABEL',
    'panels' => array (
        array(
            'fields' => array(
                array(
                    'name' =>'opps_view_by',
                    'type' => 'radioenum',
                    'label' => 'LBL_OPPS_CONFIG_VIEW_BY_FIELD_TEXT',
                    'view' => 'edit',
                    'options' => 'opps_config_view_by_options_dom',
                    'default' => false,
                    'enabled' => true,
                )
            )
        )
    )
);
