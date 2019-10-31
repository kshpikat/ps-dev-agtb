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

$dictionary['Case'] = array(
    'table' => 'cases',
    'audited' => true,
    'activity_enabled' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'Cases are issues or problems that a customer asks a support representative to resolve',
    'fields' => array(
        'account_name' => array(
            'name' => 'account_name',
            'rname' => 'name',
            'id_name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_NAME',
            'type' => 'relate',
            'related_fields' => array(
                'account_id',
            ),
            'link' => 'accounts',
            'table' => 'accounts',
            'join_name' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'len' => 100,
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'The name of the account represented by the account_id field',
            'required' => true,
            'importable' => 'required',
            'exportable' => true,
            'studio' => array(
                'portalrecordview' => false,
                'portallistview' => false,
            ),
        ),
        'account_id' => array(
            'name' => 'account_id',
            'type' => 'relate',
            'dbType' => 'id',
            'rname' => 'id',
            'module' => 'Accounts',
            'id_name' => 'account_id',
            'reportable' => false,
            'vname' => 'LBL_ACCOUNT_ID',
            'audited' => true,
            'massupdate' => false,
            'comment' => 'The account to which the case is associated',
        ),
// BEGIN SUGARCRM flav=ent ONLY
        'service_level' => array(
            'name' => 'service_level',
            'rname' => 'service_level',
            'id_name' => 'account_id',
            'vname' => 'LBL_SERVICE_LEVEL',
            'type' => 'relate',
            'link' => 'accounts',
            'table' => 'accounts',
            'join_name' => 'accounts',
            'isnull' => 'true',
            'module' => 'Accounts',
            'source' => 'non-db',
            'reportable' => false,
            'massupdate' => false,
            'comment' => 'Service level of the associated account of case',
            'readonly' => true,
        ),
        'business_center_name' => array(
            'name' => 'business_center_name',
            'rname' => 'name',
            'id_name' => 'business_center_id',
            'vname' => 'LBL_BUSINESS_CENTER_NAME',
            'type' => 'relate',
            'link' => 'business_centers',
            'table' => 'business_centers',
            'join_name' => 'business_centers',
            'isnull' => 'true',
            'module' => 'BusinessCenters',
            'dbType' => 'varchar',
            'len' => 255,
            'source' => 'non-db',
            'unified_search' => true,
            'comment' => 'The name of the business center represented by the business_center_id field',
            'required' => false,
        ),
        'business_center_id' => array(
            'name' => 'business_center_id',
            'type' => 'relate',
            'dbType' => 'id',
            'rname' => 'id',
            'module' => 'BusinessCenters',
            'id_name' => 'business_center_id',
            'reportable' => false,
            'vname' => 'LBL_BUSINESS_CENTER_ID',
            'audited' => true,
            'massupdate' => false,
            'comment' => 'The business center to which the case is associated',
        ),
        'business_centers' => array(
            'name' => 'business_centers',
            'type' => 'link',
            'relationship' => 'business_center_cases',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'vname' => 'LBL_BUSINESS_CENTER',
        ),
// END SUGARCRM flav=ent ONLY
        'source' => array(
            'name' => 'source',
            'vname' => 'LBL_SOURCE',
            'type' => 'enum',
            'options' => 'source_dom',
            'len' => 255,
            'comment' => 'An indicator of how the bug was entered (ex: via web, email, etc.)',
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'case_status_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'The status of the case',
            'merge_filter' => 'enabled',
            'sortable' => true,
        ),
        'priority' => array(
            'name' => 'priority',
            'vname' => 'LBL_PRIORITY',
            'type' => 'enum',
            'options' => 'case_priority_dom',
            'len' => 100,
            'audited' => true,
            'comment' => 'The priority of the case',
            'merge_filter' => 'enabled',
            'sortable' => true,
        ),
        'resolution' => array(
            'name' => 'resolution',
            'vname' => 'LBL_RESOLUTION',
            'type' => 'text',
            'full_text_search' => array(
                'enabled' => true,
                'searchable' => true,
                'boost' => 0.65,
            ),
            'comment' => 'The resolution of the case',
        ),
// BEGIN SUGARCRM flav=ent ONLY
        'portal_viewable' => array(
            'name' => 'portal_viewable',
            'vname' => 'LBL_SHOW_IN_PORTAL',
            'type' => 'bool',
            'default' => 1,
            'reportable' => false,
        ),
        'changetimers' => [
            'name' => 'changetimers',
            'type' => 'link',
            'relationship' => 'cases_changetimers',
            'source' => 'non-db',
            'vname' => 'LBL_CHANGETIMERS',
        ],
// END SUGARCRM flav=ent ONLY
        'tasks' => array(
            'name' => 'tasks',
            'type' => 'link',
            'relationship' => 'case_tasks',
            'source' => 'non-db',
            'vname' => 'LBL_TASKS',
        ),
        'notes' => array(
            'name' => 'notes',
            'type' => 'link',
            'relationship' => 'case_notes',
            'source' => 'non-db',
            'vname' => 'LBL_NOTES',
        ),
        'meetings' => array(
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'case_meetings',
            'bean_name' => 'Meeting',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
        ),
        'emails' => array(
            'name' => 'emails',
            'type' => 'link',
            'relationship' => 'emails_cases_rel',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        ),
        'archived_emails' => array(
            'name' => 'archived_emails',
            'type' => 'link',
            'link_file' => 'modules/Cases/CaseEmailsLink.php',
            'link_class' => 'CaseEmailsLink',
            'link' => 'contacts',
            'module' => 'Emails',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
            'link_type' => 'many',
            'relationship' => '',
            'hideacl' => true,
            'readonly' => true,
        ),
        'documents' => array(
            'name' => 'documents',
            'type' => 'link',
            'relationship' => 'documents_cases',
            'source' => 'non-db',
            'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
        ),
        'calls' => array(
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'case_calls',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
        ),
        'bugs' => array(
            'name' => 'bugs',
            'type' => 'link',
            'relationship' => 'cases_bugs',
            'source' => 'non-db',
            'vname' => 'LBL_BUGS',
        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'contacts_cases',
            'source' => 'non-db',
            'vname' => 'LBL_CONTACTS',
        ),
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'account_cases',
            'link_type' => 'one',
            'side' => 'right',
            'source' => 'non-db',
            'vname' => 'LBL_ACCOUNT',
        ),
        'project' => array(
            'name' => 'project',
            'type' => 'link',
            'relationship' => 'projects_cases',
            'source' => 'non-db',
            'vname' => 'LBL_PROJECTS',
        ),
        'kbcontents' => array(
            'name' => 'kbcontents',
            'type' => 'link',
            'vname' => 'LBL_KBCONTENTS_SUBPANEL_TITLE',
            'relationship' => 'relcases_kbcontents',
            'source' => 'non-db',
            'link_type' => 'many',
            'side' => 'right',
        ),
        'primary_contact_name' => [
            'name' => 'primary_contact_name',
            'rname' => 'name',
            'db_concat_fields' => [
                0 => 'first_name',
                1 => 'last_name',
            ],
            'related_fields' => [
                'primary_contact_id',
            ],
            'source' => 'non-db',
            'len' => '255',
            'group' => 'primary_contact_name',
            'vname' => 'LBL_PRIMARY_CONTACT_NAME',
            'reportable' => true,
            'id_name' => 'primary_contact_id',
            'join_name' => 'case_contact',
            'type' => 'relate',
            'module' => 'Contacts',
            'link' => 'case_contact',
            'table' => 'contacts',
            'studio' => 'visible',
            'audited' => true,
            'processes' => true,
        ],
        'primary_contact_id' => [
            'name' => 'primary_contact_id',
            'type' => 'id',
            'group' => 'primary_contact_name',
            'reportable' => true,
            'vname' => 'LBL_PRIMARY_CONTACT_ID',
            'audited' => true,
        ],
        'case_contact' => [
            'name' => 'case_contact',
            'type' => 'link',
            'relationship' => 'contact_cases',
            'source' => 'non-db',
            'side' => 'right',
            'vname' => 'LBL_CONTACT',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'id_name' => 'primary_contact_id',
            'link_type' => 'one',
            'audited' => true,
        ],
    ),
    'indices' => array(
        array(
            'name' => 'idx_case_del_nam_dm',
            'type' => 'index',
            'fields' => array(
                'deleted',
                'name',
                'date_modified',
                'id',
                'team_set_id',
            ),
        ),
        array(
            'name' => 'idx_account_id',
            'type' => 'index',
            'fields' => array(
                'deleted',
                'account_id',
            ),
        ),
        array(
            'name' => 'idx_cases_stat_del',
            'type' => 'index',
            'fields' => array(
                'assigned_user_id',
                'status',
                'deleted',
            ),
        ),
    ),
    'relationships' => array(
        'case_calls' => array(
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'Calls',
            'rhs_table' => 'calls',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Cases',
        ),
        'case_tasks' => array(
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Cases',
        ),
        'case_notes' => array(
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Cases',
        ),
        'case_meetings' => array(
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'Meetings',
            'rhs_table' => 'meetings',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Cases',
        ),
        'case_emails' => array(
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'Emails',
            'rhs_table' => 'emails',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Cases',
        ),
        'cases_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many',
        ),
        'cases_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many',
        ),
        'cases_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many',
        ),
        'contact_cases' => [
            'lhs_module' => 'Contacts',
            'lhs_table' => 'contacts',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'primary_contact_id',
            'relationship_type' => 'one-to-many',
        ],
    ),
    'acls' => array(
        'SugarACLStatic' => true,
    ),
    'duplicate_check' => array(
        'enabled' => true,
        'FilterDuplicateCheck' => array(
            'filter_template' => array(
                array(
                    '$and' => array(
                        array(
                            'name' => array(
                                '$starts' => '$name',
                            ),
                        ),
                        array(
                            'status' => array(
                                '$not_equals' => 'Closed',
                            ),
                        ),
                        array(
                            'account_id' => array(
                                '$equals' => '$account_id',
                            ),
                        ),
                    ),
                ),
            ),
            'ranking_fields' => array(
                array(
                    'in_field_name' => 'name',
                    'dupe_field_name' => 'name',
                ),
                array(
                    'in_field_name' => 'account_id',
                    'dupe_field_name' => 'account_id',
                ),
            ),
        ),
    ),

    // This enables optimistic locking for Saves From EditView
    'optimistic_locking' => true,
// BEGIN SUGARCRM flav=ent ONLY
    'portal_visibility' => [
        'class' => 'Cases',
        'links' => [
            'Accounts' => 'accounts',
            'Contacts' => 'contacts',
        ],
    ],
// END SUGARCRM flav=ent ONLY
);

VardefManager::createVardef('Cases', 'Case', array(
    'default',
    'assignable',
    'team_security',
    'issue',
// BEGIN SUGARCRM flav=ent ONLY
    'sla_fields',
// END SUGARCRM flav=ent ONLY
), 'case');

//jc - adding for refactor for import to not use the required_fields array
//defined in the field_arrays.php file
$dictionary['Case']['fields']['name']['importable'] = 'required';

//need to handle large mail
$dictionary['Case']['fields']['description']['dbtype'] = 'longtext';

//boost value for full text search
$dictionary['Case']['fields']['name']['full_text_search']['boost'] = 1.53;
$dictionary['Case']['fields']['case_number']['full_text_search']['boost'] = 1.29;
$dictionary['Case']['fields']['description']['full_text_search']['boost'] = 0.66;
$dictionary['Case']['fields']['work_log']['full_text_search']['boost'] = 0.64;
