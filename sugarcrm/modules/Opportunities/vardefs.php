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

$dictionary['Opportunity'] = array(
    'table' => 'opportunities',
    'audited' => true,
    'activity_enabled' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'duplicate_merge' => true,
    'comment' => 'An opportunity is the target of selling activities',
    'fields' => array(
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_OPPORTUNITY_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '50',
            'unified_search' => true,
            'full_text_search' => array('enabled' => true, 'boost' => 3),
            'comment' => 'Name of the opportunity',
            'merge_filter' => 'selected',
            'importable' => 'required',
            'required' => true,
        ),
        'opportunity_type' => array(
            'name' => 'opportunity_type',
            'vname' => 'LBL_TYPE',
            'type' => 'enum',
            'options' => 'opportunity_type_dom',
            'len' => '255',
            'audited' => true,
            'comment' => 'Type of opportunity (ex: Existing, New)',
            'merge_filter' => 'enabled',
        ),
        'account_name' => array(
            'name' => 'account_name',
            'rname' => 'name',
            'id_name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_NAME',
            'type' => 'relate',
            'table' => 'accounts',
            'join_name' => 'accounts',
            'isnull' => true,
            'module' => 'Accounts',
            'dbType' => 'varchar',
            'link' => 'accounts',
            'len' => '255',
            'source' => 'non-db',
            'unified_search' => true,
            'required' => true,
            'importable' => 'required',
            'required' => true,
            'related_field' => array(
                'account_id'
            ),
            'exportable'=> true,
            'export_link_type' => 'one',//relationship type to be used during export
        ),
        'account_id' => array(
            'name' => 'account_id',
            'vname' => 'LBL_ACCOUNT_ID',
            'id_name' => 'account_id',
            'type' => 'relate',
            'link' => 'accounts',
            'rname' => 'id',
            'source' => 'non-db',
            'audited' => true,
            'dbType' => 'id',
            'module' => 'Accounts',
            'massupdate' => false,
        ),
        'campaign_id' => array(
            'name' => 'campaign_id',
            'comment' => 'Campaign that generated lead',
            'vname' => 'LBL_CAMPAIGN_ID',
            'rname' => 'id',
            'type' => 'id',
            'dbType' => 'id',
            'table' => 'campaigns',
            'isnull' => true,
            'module' => 'Campaigns',
            //'dbType' => 'char',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
        ),
        'campaign_name' => array(
            'name' => 'campaign_name',
            'rname' => 'name',
            'id_name' => 'campaign_id',
            'vname' => 'LBL_CAMPAIGN',
            'type' => 'relate',
            'link' => 'campaign_opportunities',
            'isnull' => true,
            'table' => 'campaigns',
            'module' => 'Campaigns',
            'source' => 'non-db',
        ),
        'campaign_opportunities' => array(
            'name' => 'campaign_opportunities',
            'type' => 'link',
            'vname' => 'LBL_CAMPAIGN_OPPORTUNITY',
            'relationship' => 'campaign_opportunities',
            'source' => 'non-db',
        ),
        'lead_source' => array(
            'name' => 'lead_source',
            'vname' => 'LBL_LEAD_SOURCE',
            'type' => 'enum',
            'options' => 'lead_source_dom',
            'len' => '50',
            'comment' => 'Source of the opportunity',
            'merge_filter' => 'enabled',
        ),
        'amount' => array(
            'name' => 'amount',
            'vname' => 'LBL_LIKELY',
            //'function'=>array('vname'=>'getCurrencyType'),
            'type' => 'currency',
            //'disable_num_format' => true,
            'dbType' => 'currency',
            'comment' => 'Unconverted amount of the opportunity',
            'importable' => 'required',
            'duplicate_merge' => '1',
            //BEGIN SUGARCRM flav=pro && flav!=ent ONLY
            'required' => true,
            //END SUGARCRM flav=pro && flav!=ent ONLY
            'options' => 'numeric_range_search_dom',
            'enable_range_search' => true,
            //BEGIN SUGARCRM flav=ent ONLY
            'readonly' => true,
            'calculated' => true,
            'formula' => 'rollupCurrencySum($revenuelineitems, "likely_case")',
            'enforced' => true,
            'massupdate' => false,
            //END SUGARCRM flav=ent ONLY
            //BEGIN SUGARCRM flav!=ent ONLY
            'audited' => true,
            //END SUGARCRM flav!=ent ONLY
            'validation' => array('type' => 'range', 'min' => 0),
            'related_fields' => array(
                'currency_id',
                'base_rate'
            ),
            'convertToBase' => true,
            'showTransactionalAmount' => true,
        ),
        'base_rate' => array(
            'name' => 'base_rate',
            'vname' => 'LBL_CURRENCY_RATE',
            'type' => 'decimal',
            'len' => '26,6',
            'studio' => false
        ),
        'amount_usdollar' => array(
            'name' => 'amount_usdollar',
            'vname' => 'LBL_AMOUNT_USDOLLAR',
            'type' => 'currency',
            'group' => 'amount',
            'dbType' => 'currency',
            'disable_num_format' => true,
            'duplicate_merge' => '0',
            'comment' => 'Formatted amount of the opportunity',
            'studio' => array(
                'wirelesslistview' => false,
                'wirelesseditview' => false,
                'wirelessdetailview' => false,
                'wireless_basic_search' => false,
                'wireless_advanced_search' => false,
                'editview' => false,
                'detailview' => false,
                'quickcreate' => false,
                'mobile' => false,
            ),
            'readonly' => true,
            'is_base_currency' => true,
            'related_fields' => array(
                'currency_id',
                'base_rate'
            ),
            'formula' => 'currencyDivide($amount,$base_rate)',
            'calculated' => true,
            'enforced' => true,
        ),
        'currency_id' => array(
            'name' => 'currency_id',
            'type' => 'currency_id',
            'dbType' => 'id',
            'group' => 'currency_id',
            'vname' => 'LBL_CURRENCY',
            'function' => 'getCurrencies',
            'function_bean' => 'Currencies',
            'reportable' => false,
            'comment' => 'Currency used for display purposes',
            'default' => '-99'
        ),
        'currency_name' => array(
            'name' => 'currency_name',
            'rname' => 'name',
            'id_name' => 'currency_id',
            'vname' => 'LBL_CURRENCY_NAME',
            'type' => 'relate',
            'link' => 'currencies',
            'isnull' => true,
            'table' => 'currencies',
            'module' => 'Currencies',
            'source' => 'non-db',
            'function' => 'getCurrencies',
            'function_bean' => 'Currencies',
            'studio' => false,
            'duplicate_merge' => 'disabled',
            'massupdate' => false
        ),
        'currency_symbol' => array(
            'name' => 'currency_symbol',
            'rname' => 'symbol',
            'id_name' => 'currency_id',
            'vname' => 'LBL_CURRENCY_SYMBOL',
            'type' => 'relate',
            'link' => 'currencies',
            'isnull' => true,
            'table' => 'currencies',
            'module' => 'Currencies',
            'source' => 'non-db',
            'function' => 'getCurrencySymbols',
            'function_bean' => 'Currencies',
            'studio' => false,
            'duplicate_merge' => 'disabled',
            'massupdate' => false
        ),
        'date_closed' => array(
            'name' => 'date_closed',
            'vname' => 'LBL_DATE_CLOSED',
            'type' => 'date',
            'comment' => 'Expected or actual date the oppportunity will close',
            //BEGIN SUGARCRM flav=pro && flav!=ent ONLY
            'audited' => true,
            'importable' => 'required',
            'required' => true,
            //END SUGARCRM flav=pro && flav!=ent ONLY
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
            //BEGIN SUGARCRM flav=ent ONLY
            'readonly' => true,
            'importable' => true,
            'massupdate' => false,
            'calculated' => true,
            'formula' => 'maxRelatedDate($revenuelineitems, "date_closed")',
            'enforced' => true,
            //END SUGARCRM flav=ent ONLY
            'related_fields' => array(
                'date_closed_timestamp'
            )
        ),
        'date_closed_timestamp' => array(
            'name' => 'date_closed_timestamp',
            'vname' => 'LBL_DATE_CLOSED_TIMESTAMP',
            'type' => 'int',
            'studio' => false,
            'reportable' => false,
            'workflow' => false,
            'massupdate' => false,
            'enforced' => true,
            'calculated' => true,
            //BEGIN SUGARCRM flav=ent ONLY
            'formula' => 'rollupMax($revenuelineitems, "date_closed_timestamp")',
            //END SUGARCRM flav=ent ONLY
            //BEGIN SUGARCRM flav=pro && flav!=ent ONLY
            'formula' => 'timestamp($date_closed)',
            //END SUGARCRM flav=pro && flav!=ent ONLY
        ),
        'next_step' => array(
            'name' => 'next_step',
            'vname' => 'LBL_NEXT_STEP',
            'type' => 'varchar',
            'len' => '100',
            'comment' => 'The next step in the sales process',
            'merge_filter' => 'enabled',
            'massupdate' => true,
        ),
        'sales_stage' => array(
            'name' => 'sales_stage',
            'vname' => 'LBL_SALES_STAGE',
            'type' => 'enum',
            'options' => 'sales_stage_dom',
            'default' => 'Prospecting',
            'len' => '255',
            'comment' => 'Indication of progression towards closure',
            'merge_filter' => 'enabled',
            'importable' => 'required',
            //BEGIN SUGARCRM flav=pro && flav!=ent ONLY
            'audited' => true,
            'required' => true,
            //END SUGARCRM flav=pro && flav!=ent ONLY
            //BEGIN SUGARCRM flav=ent ONLY
            'studio' => false,
            'massupdate' => false,
            'reportable' => false,
            'workflow' => false,
            //END SUGARCRM flav=ent ONLY
        ),
        'sales_status' => array(
            'name' => 'sales_status',
            'vname' => 'LBL_SALES_STATUS',
            'type' => 'enum',
            'options' => 'sales_status_dom',
            'len' => '255',
            'readonly' => true,
            'duplicate_merge' => 'disabled',
            'workflow' => false,
            //BEGIN SUGARCRM flav!=ent ONLY
            'studio' => false,
            'reportable' => false,
            //END SUGARCRM flav!=ent ONLY
            //BEGIN SUGARCRM flav=ent ONLY
            'audited' => true,
            'massupdate' => false,
            //END SUGARCRM flav=ent ONLY
        ),
        'probability' => array(
            'name' => 'probability',
            'vname' => 'LBL_PROBABILITY',
            'type' => 'int',
            'dbType' => 'double',
            //BEGIN SUGARCRM flav!=ent ONLY
            'audited' => true,
            'formula' => 'getDropdownValue("sales_probability_dom",$sales_stage)',
            'calculated' => true,
            'enforced' => true,
            //END SUGARCRM flav!=ent ONLY
            'comment' => 'The probability of closure',
            'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
            'merge_filter' => 'enabled',
            //BEGIN SUGARCRM flav=ent ONLY
            'reportable' => false,
            'studio' => false,
            'massupdate' => false,
            //END SUGARCRM flav=ent ONLY
        ),
        'best_case' => array(
            'name' => 'best_case',
            'vname' => 'LBL_BEST',
            'dbType' => 'currency',
            'type' => 'currency',
            'len' => '26,6',
            'validation' => array('type' => 'range', 'min' => 0),
            //BEGIN SUGARCRM flav=ent ONLY
            'readonly' => true,
            'formula' => 'rollupCurrencySum($revenuelineitems, "best_case")',
            'calculated' => true,
            'enforced' => true,
            'massupdate' => false,
            //END SUGARCRM flav=ent ONLY
            //BEGIN SUGARCRM flav!=ent ONLY
            'audited' => true,
            //END SUGARCRM flav!=ent ONLY
            'related_fields' => array(
                'currency_id',
                'base_rate'
            ),
            'convertToBase' => true,
            'showTransactionalAmount' => true,
        ),
        'worst_case' => array(
            'name' => 'worst_case',
            'vname' => 'LBL_WORST',
            'dbType' => 'currency',
            'type' => 'currency',
            'len' => '26,6',
            'validation' => array('type' => 'range', 'min' => 0),
            //BEGIN SUGARCRM flav=ent ONLY
            'readonly' => true,
            'formula' => 'rollupCurrencySum($revenuelineitems, "worst_case")',
            'calculated' => true,
            'enforced' => true,
            'massupdate' => false,
            //END SUGARCRM flav=ent ONLY
            //BEGIN SUGARCRM flav!=ent ONLY
            'audited' => true,
            //END SUGARCRM flav!=ent ONLY
            'related_fields' => array(
                'currency_id',
                'base_rate'
            ),
            'convertToBase' => true,
            'showTransactionalAmount' => true,
        ),
        'commit_stage' => array(
            'name' => 'commit_stage',
            'vname' => 'LBL_COMMIT_STAGE',
            'type' => 'enum',
            'len' => '50',
            'comment' => 'Forecast commit ranges: Include, Likely, Omit etc.',
            'function' => 'getCommitStageDropdown',
            'function_bean' => 'Forecasts',
            //BEGIN SUGARCRM flav=ent ONLY
            'massupdate' => false,
            'studio' => false,
            'reportable' => false,
            'workflow' => false
            //END SUGARCRM flav=ent ONLY
        ),
        //BEGIN SUGARCRM flav=ent ONLY
        'total_revenue_line_items' => array(
            'name' => 'total_revenue_line_items',
            'vname' => 'LBL_TOTAL_RLIS',
            'type' => 'int',
            'formula' => 'count($revenuelineitems)',
            'calculated' => true,
            'enforced' => true,
            'studio' => false,
        ),
        'closed_revenue_line_items' => array(
            'name' => 'closed_revenue_line_items',
            'vname' => 'LBL_CLOSED_RLIS',
            'type' => 'int',
            'formula' => 'countConditional($revenuelineitems,"sales_stage",createList("Closed Won","Closed Lost"))',
            'calculated' => true,
            'enforced' => true,
            'studio' => false,
        ),
        //END SUGARCRM flav=ent ONLY
        'accounts' => array(
            'name' => 'accounts',
            'type' => 'link',
            'relationship' => 'accounts_opportunities',
            'source' => 'non-db',
            'link_type' => 'one',
            'module' => 'Accounts',
            'bean_name' => 'Account',
            'vname' => 'LBL_ACCOUNTS',
        ),
        'contacts' => array(
            'name' => 'contacts',
            'type' => 'link',
            'relationship' => 'opportunities_contacts',
            'source' => 'non-db',
            'module' => 'Contacts',
            'bean_name' => 'Contact',
            'rel_fields' => array(
                'contact_role' => array(
                    'type' => 'enum',
                    'options' => 'opportunity_relationship_type_dom'
                )
            ),
            'vname' => 'LBL_CONTACTS',
            'populate_list' => array(
                'account_id' => 'account_id',
                'account_name' => 'account_name',
            )
        ),
        'contact_role' => array(
            'name' => 'contact_role',
            'type' => 'enum',
            'source' => 'non-db',
            'vname' => 'LBL_OPPORTUNITY_ROLE',
            'options' => 'opportunity_relationship_type_dom',
            'link' => 'contacts',
            'rname_link' => 'contact_role',
        ),
        'tasks' => array(
            'name' => 'tasks',
            'type' => 'link',
            'relationship' => 'opportunity_tasks',
            'source' => 'non-db',
            'vname' => 'LBL_TASKS',
        ),
        'notes' => array(
            'name' => 'notes',
            'type' => 'link',
            'relationship' => 'opportunity_notes',
            'source' => 'non-db',
            'vname' => 'LBL_NOTES',
        ),
        'meetings' => array(
            'name' => 'meetings',
            'type' => 'link',
            'relationship' => 'opportunity_meetings',
            'source' => 'non-db',
            'vname' => 'LBL_MEETINGS',
        ),
        'calls' => array(
            'name' => 'calls',
            'type' => 'link',
            'relationship' => 'opportunity_calls',
            'source' => 'non-db',
            'vname' => 'LBL_CALLS',
        ),
        'emails' => array(
            'name' => 'emails',
            'type' => 'link',
            'relationship' => 'emails_opportunities_rel', /* reldef in emails */
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
        ),
        'archived_emails' => array(
            'name' => 'archived_emails',
            'type' => 'link',
            'link_file' => 'modules/Emails/ArchivedEmailsBeanLink.php',
            'link_class' => 'ArchivedEmailsBeanLink',
            'link' => 'contacts',
            'source' => 'non-db',
            'vname' => 'LBL_EMAILS',
            'module' => 'Emails',
            'link_type' => 'many',
            'relationship' => '',
            'hideacl' => true,
            'readonly' => true,
        ),
        'documents' => array(
            'name' => 'documents',
            'type' => 'link',
            'relationship' => 'documents_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_DOCUMENTS_SUBPANEL_TITLE',
        ),
        'quotes' => array(
            'name' => 'quotes',
            'type' => 'link',
            'relationship' => 'quotes_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_QUOTES',
        ),

        'project' => array(
            'name' => 'project',
            'type' => 'link',
            'relationship' => 'projects_opportunities',
            'source' => 'non-db',
            'vname' => 'LBL_PROJECTS',
        ),
        'leads' => array(
            'name' => 'leads',
            'type' => 'link',
            'relationship' => 'opportunity_leads',
            'source' => 'non-db',
            'vname' => 'LBL_LEADS',
        ),
        'campaigns' => array(
            'name' => 'campaigns',
            'type' => 'link',
            'relationship' => 'campaignlog_created_opportunities',
            'module' => 'CampaignLog',
            'bean_name' => 'CampaignLog',
            'source' => 'non-db',
            'vname' => 'LBL_CAMPAIGNS',
            'reportable' => false
        ),
        'campaign_link' => array(
            'name' => 'campaign_link',
            'type' => 'link',
            'relationship' => 'campaign_opportunities',
            'vname' => 'LBL_CAMPAIGN_LINK',
            'link_type' => 'one',
            'module' => 'Campaigns',
            'bean_name' => 'Campaign',
            'source' => 'non-db',
            'reportable' => false
        ),
        'currencies' => array(
            'name' => 'currencies',
            'type' => 'link',
            'relationship' => 'opportunity_currencies',
            'source' => 'non-db',
            'vname' => 'LBL_CURRENCIES',
        ),
        'contracts' => array(
            'name' => 'contracts',
            'type' => 'link',
            'vname' => 'LBL_CONTRACTS',
            'relationship' => 'contracts_opportunities',
            //'link_type' => 'one', bug# 31652 relationship is one to many from opportunities to contracts
            'source' => 'non-db',
            'populate_list' => array(
                'account_id' => 'account_id',
                'account_name' => 'account_name',
            )
        ),
        'revenuelineitems' => array(
            'name' => 'revenuelineitems',
            'type' => 'link',
            'vname' => 'LBL_RLI',
            'relationship' => 'opportunities_revenuelineitems',
            'source' => 'non-db',
        ),
        'forecastworksheets' =>  array(
            'name' => 'forecastworksheets',
            'type' => 'link',
            'relationship' => 'forecastworksheets_opportunities',
            'vname' => 'LBL_FORECAST_WORKSHEET',
            'module' => 'ForecastWorksheets',
            'bean_name' => 'ForecastWorksheet',
            'source' => 'non-db',
        ),
        'products' => array(
            'name' => 'products',
            'type' => 'link',
            'vname' => 'LBL_PRODUCTS',
            'relationship' => 'opportunities_products',
            'source' => 'non-db',
        ),

        // Marketo Fields
        'mkto_sync' =>
            array(
                'name' => 'mkto_sync',
                'vname' => 'LBL_MKTO_SYNC',
                'type' => 'bool',
                'default' => '0',
                'comment' => 'Should the Lead be synced to Marketo',
                'massupdate' => true,
                'audited' => true,
                'duplicate_merge' => true,
                'reportable' => true,
                'importable' => 'true',
            ),
        'mkto_id' =>
            array(
                'name' => 'mkto_id',
                'vname' => 'LBL_MKTO_ID',
                'comment' => 'Associated Marketo Lead ID',
                'type' => 'int',
                'default' => null,
                'audited' => true,
                'mass_update' => false,
                'duplicate_merge' => true,
                'reportable' => true,
                'importable' => 'false',
            ),
    ),
    'indices' => array(
        array(
            'name' => 'idx_opp_name',
            'type' => 'index',
            'fields' => array('name'),
        ),
        array(
            'name' => 'idx_opp_assigned_timestamp',
            'type' => 'index',
            'fields' => array('assigned_user_id', 'date_closed_timestamp', 'deleted'),
        ),
        array(
            'name' => 'idx_opp_id_deleted',
            'type' => 'index',
            'fields' => array('id', 'deleted'),
        ),
        array('name' => 'idx_opportunity_sales_status', 'type' => 'index', 'fields' => array('sales_status')),
        array('name' => 'idx_opportunity_opportunity_type', 'type' => 'index', 'fields' => array('opportunity_type')),
        array('name' => 'idx_opportunity_lead_source', 'type' => 'index', 'fields' => array('lead_source')),
        array('name' => 'idx_opportunity_next_step', 'type' => 'index', 'fields' => array('next_step')),
        array('name' => 'idx_opportunity_date_entered', 'type' => 'index', 'fields' => array('date_entered')),
        array(
            'name' => 'idx_opportunity_mkto_id',
            'type' => 'index',
            'fields' => array('mkto_id')
        ),
    ),
    'relationships' => array(
        'opportunity_calls' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Calls',
            'rhs_table' => 'calls',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Opportunities'
        ),
        'opportunity_meetings' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Meetings',
            'rhs_table' => 'meetings',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Opportunities'
        ),
        'opportunity_tasks' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Tasks',
            'rhs_table' => 'tasks',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Opportunities'
        ),
        'opportunity_notes' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Opportunities'
        ),
        'opportunity_emails' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Emails',
            'rhs_table' => 'emails',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'Opportunities'
        ),
        'opportunity_leads' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'Leads',
            'rhs_table' => 'leads',
            'rhs_key' => 'opportunity_id',
            'relationship_type' => 'one-to-many'
        ),
        'opportunity_currencies' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'currency_id',
            'rhs_module' => 'Currencies',
            'rhs_table' => 'currencies',
            'rhs_key' => 'id',
            'relationship_type' => 'one-to-many'
        ),
        'opportunities_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many',
        ),
        'opportunities_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'opportunities_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'Opportunities',
            'rhs_table' => 'opportunities',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
        'opportunities_revenuelineitems' => array(
            'lhs_module' => 'Opportunities',
            'lhs_table' => 'opportunities',
            'lhs_key' => 'id',
            'rhs_module' => 'RevenueLineItems',
            'rhs_table' => 'revenue_line_items',
            'rhs_key' => 'opportunity_id',
            //BEGIN SUGARCRM flav!=ent ONLY
            'relationship_type' => 'one-to-one',
            //END SUGARCRM  flav!=ent ONLY
            //BEGIN SUGARCRM flav=ent ONLY
            'relationship_type' => 'one-to-many',
            //END SUGARCRM flav=ent ONLY
        ),
    ),
    'duplicate_check' => array(
        'enabled' => true,
        'FilterDuplicateCheck' => array(
            'filter_template' => array(
                array(
                    '$and' => array(
                        array('name' => array('$starts' => '$name')),

                        //BEGIN SUGARCRM flav=ent ONLY
                        array('sales_status' => array('$not_equals' => 'Closed Lost')),
                        array('sales_status' => array('$not_equals' => 'Closed Won')),
                        //END SUGARCRM flav=ent ONLY

                        //BEGIN SUGARCRM flav!=ent ONLY
                        array('sales_stage' => array('$not_equals' => 'Closed Lost')),
                        array('sales_stage' => array('$not_equals' => 'Closed Won')),
                        //END SUGARCRM flav!=ent ONLY

                        array('accounts.id' => array('$equals' => '$account_id')),
                    )
                ),
            ),
            'ranking_fields' => array(
                array('in_field_name' => 'name', 'dupe_field_name' => 'name'),
            )
        )
    ),
//This enables optimistic locking for Saves From EditView
    'optimistic_locking' => true,
);
VardefManager::createVardef(
    'Opportunities',
    'Opportunity',
    array(
        'default',
        'assignable',
        'team_security',
    )
);
