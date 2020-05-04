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

$dictionary['Purchase'] = [
    'table' => 'purchases',
    'audited' => true,
    'activity_enabled' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Module to track items sold',
    'fields' => [
        'start_date' => [
            'name' => 'start_date',
            'vname' => 'LBL_START_DATE',
            'type' => 'date',
            'comment' => 'Start date of the purchase',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => false,
            ),
            'readonly' => true,
        ],
        'end_date' => [
            'name' => 'end_date',
            'vname' => 'LBL_END_DATE',
            'type' => 'date',
            'comment' => 'End date of the purchase',
            'readonly' => true,
        ],
        'service' => [
            'name' => 'service',
            'vname' => 'LBL_SERVICE',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'Indicates whether the purchase is a service',
        ],
        'renewable' => [
            'name' => 'renewable',
            'vname' => 'LBL_RENEWABLE',
            'type' => 'bool',
            'default' => 0,
            'comment' => 'Indicates whether the purchase is renewable',
        ],
        'product_template_id' => [
            'name' => 'product_template_id',
            'type' => 'id',
            'module' => 'ProductTemplates',
            'reportable' => false,
            'vname' => 'LBL_PRODUCT_TEMPLATE_ID',
        ],
        'product_template_name' => [
            'name' => 'product_template_name',
            'rname' => 'name',
            'id_name' => 'product_template_id',
            'vname' => 'LBL_PRODUCT_TEMPLATE_NAME',
            'type' => 'relate',
            'link' => 'product_templates',
            'table' => 'product_templates',
            'join_name' => 'templates',
            'module' => 'ProductTemplates',
            'dbType' => 'varchar',
            'source' => 'non-db',
            'unified_search' => true,
            'required' => false,
        ],
        'account_name' => [
            'name' => 'account_name',
            'rname' => 'name',
            'id_name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_NAME',
            'related_fields' => [
                'account_id',
            ],
            'join_name' => 'accounts',
            'type' => 'relate',
            'link' => 'accounts',
            'table' => 'accounts',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'importable' => true,
            'exportable'=> true,
            'required' => true,
        ],
        'account_id' => [
            'name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_ID',
            'type' => 'relate',
            'dbType' => 'id',
            'rname' => 'id',
            'id_name' => 'account_id',
            'module' => 'Accounts',
            'reportable' => false,
            'massupdate' => false,
        ],
        'type_id' => [
            'name' => 'type_id',
            'vname' => 'LBL_TYPE_ID',
            'type' => 'id',
            'required' => false,
            'reportable' => false,
            'comment' => 'Product type (ex: hardware, software]',
        ],
        'type_name' => [
            'name' => 'type_name',
            'rname' => 'name',
            'id_name' => 'type_id',
            'vname' => 'LBL_PRODUCT_TYPE',
            'join_name' => 'types',
            'type' => 'relate',
            'save' => true,
            'link' => 'product_types',
            'table' => 'product_types',
            'isnull' => 'true',
            'module' => 'ProductTypes',
            'importable' => 'false',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
        ],
        'category_id' => [
            'name' => 'category_id',
            'vname' => 'LBL_CATEGORY_ID',
            'type' => 'id',
            'group' => 'category_name',
            'required' => false,
            'reportable' => true,
            'comment' => 'Product category',
        ],
        'category_name' => [
            'name' => 'category_name',
            'rname' => 'name',
            'id_name' => 'category_id',
            'vname' => 'LBL_CATEGORY_NAME',
            'join_name' => 'categories',
            'type' => 'relate',
            'link' => 'categories',
            'table' => 'product_categories',
            'isnull' => 'true',
            'module' => 'ProductCategories',
            'dbType' => 'varchar',
            'len' => '255',
            'save' => true,
            'source' => 'non-db',
            'required' => false,
            'studio' => [
                'editview' => false,
                'detailview' => false,
                'quickcreate' => false,
            ],
        ],
        // Links
        'purchasedlineitems' => array(
            'name' => 'purchasedlineitems',
            'type' => 'link',
            'vname' => 'LBL_PURCHASED_LINE_ITEMS',
            'relationship' => 'purchase_purchasedlineitems',
            'source' => 'non-db',
            'workflow' => false
        ),
        'product_templates' => [
            'name' => 'product_templates',
            'type' => 'link',
            'relationship' => 'purchases_producttemplates',
            'vname' => 'LBL_PRODUCT_TEMPLATES',
            'link_type' => 'one',
            'module' => 'ProductTemplates',
            'bean_name' => 'ProductTemplate',
            'source' => 'non-db',
        ],
        'accounts' => [
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'account_purchases',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNT',
        ],
        'product_types' => [
            'name' => 'product_types',
            'type' => 'link',
            'relationship' => 'purchases_types',
            'vname' => 'LBL_PRODUCT_TYPES',
            'link_type' => 'one',
            'module' => 'ProductTypes',
            'bean_name' => 'ProductType',
            'source' => 'non-db',
        ],
        'categories' => [
            'name' => 'categories',
            'type' => 'link',
            'relationship' => 'purchases_categories',
            'vname' => 'LBL_PRODUCT_CATEGORIES',
            'link_type' => 'one',
            'module' => 'ProductCategories',
            'bean_name' => 'ProductCategory',
            'source' => 'non-db',
        ],
        'documents' => [
            'name' => 'documents',
            'type' => 'link',
            'relationship' => 'documents_purchases',
            'source' => 'non-db',
            'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
        ],
        'contacts' => [
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'contacts_purchases',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS_SUBPANEL_TITLE',
        ],
        'cases' => [
            'name' => 'cases',
            'type' => 'link',
            'relationship' => 'cases_purchases',
            'source' => 'non-db',
            'vname' => 'LBL_CASES_SUBPANEL_TITLE',
        ],
        'calls' => [
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'purchase_calls',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
            'module' => 'Calls',
        ],
        'meetings' => [
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'purchase_meetings',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
            'module' => 'Meetings',
        ],
        'notes' => [
            'name' => 'notes',
            'type' => 'link',
            'relationship' => 'purchase_notes',
            'source' => 'non-db',
            'vname' => 'LBL_NOTES',
        ],
        'tasks' => [
            'name' => 'tasks',
            'type' => 'link',
            'relationship' => 'purchase_tasks',
            'source' => 'non-db',
            'vname' => 'LBL_TASKS',
        ],
        'emails' => [
            'name' => 'emails',
            'type' => 'link',
            'relationship' => 'emails_purchases_rel', /* reldef in emails */
            'module' => 'Emails',
            'bean_name' => 'Email',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        ],
        'archived_emails' => [
            'name' => 'archived_emails',
            'type' => 'link',
            'link_file' => 'modules/Emails/ArchivedEmailsLink.php',
            'link_class' => 'ArchivedEmailsLink',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
            'module' => 'Emails',
            'link_type' => 'many',
            'relationship' => '',
            'hideacl' => true,
            'readonly' => true,
        ],
    ],
    'relationships' => [
        'purchase_purchasedlineitems' => [
            'lhs_module' => 'Purchases',
            'lhs_table' => 'purchases',
            'lhs_key' => 'id',
            'rhs_module' => 'PurchasedLineItems',
            'rhs_table' => 'purchased_line_items',
            'rhs_key' => 'purchase_id',
            'relationship_type' => 'one-to-many',
        ],
        'purchases_producttemplates' => [
            'lhs_module' => 'Purchases',
            'lhs_table' => 'purchases',
            'lhs_key' => 'product_template_id',
            'rhs_module' => 'ProductTemplates',
            'rhs_table' => 'product_templates',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ],
        'purchases_types' => [
            'lhs_module' => 'ProductTypes',
            'lhs_table' => 'product_types',
            'lhs_key' => 'id',
            'rhs_module' => 'Purchases',
            'rhs_table' => 'purchases',
            'rhs_key' => 'type_id',
            'relationship_type' => 'one-to-many',
        ],
        'purchases_categories' => [
            'lhs_module' => 'ProductCategories',
            'lhs_table' => 'product_categories',
            'lhs_key' => 'id',
            'rhs_module' => 'Purchases',
            'rhs_table' => 'purchases',
            'rhs_key' => 'category_id',
            'relationship_type' => 'one-to-many',
        ],
        'purchase_tasks' => [
            'lhs_module' => 'Purchases',
            'lhs_table' => 'purchases',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Purchases',
        ],
        'purchase_notes' => [
            'lhs_module' => 'Purchases',
            'lhs_table' => 'purchases',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Purchases',
        ],
        'purchase_meetings' => [
            'lhs_module' => 'Purchases',
            'lhs_table' => 'purchases',
            'lhs_key' => 'id',
            'rhs_module' => 'Meetings',
            'rhs_table' => 'meetings',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Purchases',
        ],
        'purchase_calls' => [
            'lhs_module' => 'Purchases',
            'lhs_table' => 'purchases',
            'lhs_key' => 'id',
            'rhs_module' => 'Calls',
            'rhs_table' => 'calls',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Purchases',
        ],
    ],
    'uses' => [
        'basic',
        'assignable',
        'team_security',
    ],
];
VardefManager::createVardef(
    'Purchases',
    'Purchase'
);
