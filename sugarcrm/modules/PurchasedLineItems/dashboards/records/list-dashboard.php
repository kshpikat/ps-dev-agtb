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

return [
    'metadata' => [
        'dashlets' => [
            [
                'view' => [
                    'type' => 'dashablelist',
                    'label' => 'TPL_DASHLET_MY_MODULE',
                    'display_columns' => [
                        'name',
                        'purchase_name',
                        'product_template_name',
                        'revenue',
                        'total_amount',
                        'quantity',
                        'service_start_date',
                    ],
                    'limit' => 10,
                ],
                'context' => [
                    'module' => 'PurchasedLineItems',
                ],
                'width' => 12,
                'height' => 4,
                'x' => 0,
                'y' => 0,
            ],
        ],
    ],
    'name' => 'LBL_PURCHASEDLINEITEMS_LIST_DASHBOARD',
];
