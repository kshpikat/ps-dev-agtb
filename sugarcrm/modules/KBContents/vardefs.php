<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
$dictionary['KBContent'] = array(
    'table' => 'kbcontents',
    'audited' => true,
    'activity_enabled' => true,
    'unified_search' => true,
    'full_text_search' => true,
    'unified_search_default_enabled' => true,
    'comment' => 'A content represents information about document',
    'duplicate_merge' => true,
    'fields' => array(
        'kbdocument_body' => array(
            'name' => 'kbdocument_body',
            'vname' => 'LBL_TEXT_BODY',
            'dbType' => 'longtext',
            'type' => 'htmleditable_tinymce',
            'comment' => 'Article body',
            'full_text_search' => array(
                'enabled' => true,
                'type' => 'text',
                'elastic' => array(
                    'term_vector' => 'yes',
                    'store' => 'yes',
                ),
            ),
            'audited' => true,
        ),
        'language' => array(
            'name' => 'language',
            'type' => 'varchar',
            'len' => '2',
            'required' => true,
            'vname' => 'LBL_LANG',
            'audited' => false,
            'studio' => false,
            'full_text_search' => array(
                'enabled' => true,
            ),
        ),
        'active_date' => array(
            'name' => 'active_date',
            'vname' => 'LBL_PUBLISH_DATE',
            'type' => 'date',
            'sortable' => true,
            'studio' => false,
            'massupdate' => false,
        ),
        'exp_date' => array(
            'name' => 'exp_date',
            'vname' => 'LBL_EXP_DATE',
            'type' => 'date',
            'sortable' => true,
            'studio' => false,
        ),
        'approved' => array(
            'name' => 'approved',
            'vname' => 'LBL_APPROVED',
            'type' => 'bool',
            'sortable' => true,
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
            'massupdate' => false,
        ),
        'status' => array(
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'len' => 100,
            'options' => 'kbdocument_status_dom',
            'default' => KBContent::ST_DRAFT,
            'reportable' => true,
            'audited' => true,
            'studio' => true,
            'full_text_search' => array(
                'enabled' => true,
            ),
        ),
        'viewcount' => array(
            'name' => 'viewcount',
            'vname' => 'LBL_VIEWED_COUNT',
            'type' => 'int',
            'importable' => 'required',
            'default' => 0,
            'sortable' => true,
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'revision' => array(
            'name' => 'revision',
            'vname' => 'LBL_REVISION',
            'type' => 'int',
            'default' => '0',
            'duplicate_on_record_copy' => 'no',
            'studio' => true,
        ),
        'useful' => array(
            'name' => 'useful',
            'vname' => 'LBL_USEFUL',
            'type' => 'int',
            'default' => '0',
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
            'group' => 'usefulness',
        ),
        'notuseful' => array(
            'name' => 'notuseful',
            'vname' => 'LBL_NOT_USEFUL',
            'type' => 'int',
            'default' => '0',
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
            'group' => 'usefulness',
        ),
        'attachment_list' => array(
            'name' => 'attachment_list',
            'type' => 'file',
            'source' => 'non-db',
            'vname' => 'LBL_RATING',
            'studio' => false,
            'group' => 'attachments',
        ),
        'notes' => array(
            'name' => 'notes',
            'vname' => 'LBL_NOTES',
            'type' => 'link',
            'relationship' => 'kbcontent_notes',
            'module' => 'Notes',
            'bean_name' => 'Note',
            'source' => 'non-db',
        ),
        'attachments' => array(
            'name' => 'attachments',
            'vname' => 'LBL_ATTACHMENTS',
            'type' => 'link',
            'relationship' => 'kbcontent_attachments',
            'module' => 'Notes',
            'bean_name' => 'Note',
            'source' => 'non-db',
            'group' => 'attachments',
        ),
        'kbdocuments_kbcontents' => array(
            'name' => 'kbdocuments_kbcontents',
            'type' => 'link',
            'vname' => 'LBL_KBDOCUMENTS',
            'relationship' => 'kbdocuments_kbcontents',
            'source' => 'non-db',
        ),
        'kbdocument_id' => array(
            'name' => 'kbdocument_id',
            'id_name' => 'kbdocument_id',
            'vname' => 'LBL_KBDOCUMENT_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'kbdocuments',
            'isnull' => 'true',
            'module' => 'KBDocuments',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),
        'kbdocument_name' => array(
            'name' => 'kbdocument_name',
            'rname' => 'name',
            'vname' => 'LBL_KBDOCUMENT',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'kbdocuments',
            'id_name' => 'kbdocument_id',
            'link' => 'kbdocuments_kbcontents',
            'module' => 'KBDocuments',
            'duplicate_merge' => 'disabled',
            'studio' => false,
            'massupdate' => false,
        ),
        'active_rev' => array(
            'name' => 'active_rev',
            'vname' => 'LBL_ACTIVE_REV',
            'type' => 'tinyint',
            'isnull' => 'true',
            'comment' => 'Active revision flag',
            'default' => 0,
            'duplicate_on_record_copy' => 'no',
            'full_text_search' => array(
                'enabled' => true,
                'type' => 'bool',
            ),
            'studio' => array(
                'list' => false,
                'quickcreate' => false,
                'basic_search' => false,
                'advanced_search' => false,
            ),
            'readonly' => true,
        ),
        'is_external' => array(
            'name' => 'is_external',
            'vname' => 'LBL_IS_EXTERNAL',
            'type' => 'tinyint',
            'isnull' => 'true',
            'comment' => 'External article flag',
            'default' => 0,
            'duplicate_on_record_copy' => 'no',
            'studio' => false,
        ),
        'kbarticles_kbcontents' => array(
            'name' => 'kbarticles_kbcontents',
            'type' => 'link',
            'vname' => 'LBL_KBARTICLES',
            'relationship' => 'kbarticles_kbcontents',
            'source' => 'non-db',
        ),
        'kbarticle_id' => array(
            'name' => 'kbarticle_id',
            'id_name' => 'kbarticle_id',
            'vname' => 'LBL_KBARTICLE_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'kbarticles',
            'isnull' => 'true',
            'module' => 'KBArticles',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'importable' => false,
            'audited' => true,
            'studio' => false,
        ),
        'kbarticle_name' => array(
            'name' => 'kbarticle_name',
            'rname' => 'name',
            'vname' => 'LBL_KBARTICLE',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'kbarticles',
            'id_name' => 'kbarticle_id',
            'link' => 'kbarticles_kbcontents',
            'module' => 'KBArticles',
            'duplicate_merge' => 'disabled',
            'studio' => false,
            'massupdate' => false,
        ),
        'localizations' => array(
            'name' => 'localizations',
            'type' => 'link',
            'link_file' => 'modules/KBContents/LocalizationsLink.php',
            'link_class' => 'LocalizationsLink',
            'source' => 'non-db',
            'vname' => 'LBL_KBSLOCALIZATIONS',
            'relationship' => 'localizations',
            'studio' => false,
            'massupdate' => false,
        ),
        'revisions' => array(
            'name' => 'revisions',
            'type' => 'link',
            'link_file' => 'modules/KBContents/RevisionsLink.php',
            'link_class' => 'RevisionsLink',
            'source' => 'non-db',
            'vname' => 'LBL_KBSREVISIONS',
            'relationship' => 'revisions',
            'studio' => false,
            'massupdate' => false,
        ),
        'related_languages' => array(
            'name' => 'related_languages',
            'type' => 'enum',
            'function' => 'getLanguages',
            'function_bean' => 'KBContents',
            'source' => 'non-db',
            'vname' => 'LBL_KBSLOCALIZATIONS',
            'studio' => false,
            'massupdate' => false,
        ),
        'kbsapprovers_kbcontents' => array(
            'name' => 'kbsapprovers_kbcontents',
            'type' => 'link',
            'vname' => 'LBL_KBSAPPROVERS',
            'relationship' => 'kbsapprovers_kbcontents',
            'source' => 'non-db',
        ),
        'kbsapprover_id' => array(
            'name' => 'kbsapprover_id',
            'id_name' => 'kbsapprover_id',
            'vname' => 'LBL_KBSAPPROVER_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'users',
            'isnull' => 'true',
            'module' => 'Users',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'audited' => true,
            'studio' => false,
            'comment' => 'User who approved article',
        ),
        'kbsapprover_name' => array(
            'name' => 'kbsapprover_name',
            'rname' => 'full_name',
            'vname' => 'LBL_KBSAPPROVER',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'kbsapprover_id',
            'link' => 'kbsapprovers_kbcontents',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'studio' => false,
        ),

        'relcases_kbcontents' => array(
            'name' => 'relcases_kbcontents',
            'type' => 'link',
            'vname' => 'LBL_KBSCASES',
            'relationship' => 'relcases_kbcontents',
            'source' => 'non-db',
        ),
        'kbscase_id' => array(
            'name' => 'kbscase_id',
            'id_name' => 'kbscase_id',
            'vname' => 'LBL_KBSCASE_ID',
            'rname' => 'id',
            'type' => 'id',
            'table' => 'cases',
            'isnull' => 'true',
            'module' => 'Cases',
            'reportable' => false,
            'massupdate' => false,
            'duplicate_merge' => 'disabled',
            'audited' => true,
            'studio' => false,
            'comment' => 'Related case',
            'importable' => true,
        ),
        'kbscase_name' => array(
            'name' => 'kbscase_name',
            'rname' => 'name',
            'vname' => 'LBL_KBSCASE',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'cases',
            'id_name' => 'kbscase_id',
            'link' => 'relcases_kbcontents',
            'module' => 'Cases',
            'duplicate_merge' => 'disabled',
            'studio' => false,
            'importable' => false,
        ),
        'category_id' => array(
            'name' => 'category_id',
            'vname' => 'LBL_CATEGORY_ID',
            'type' => 'id',
            'isnull' => 'true',
            'comment' => 'Category ID',
            'audited' => true,
            'studio' => false,
        ),
        'category_name' => array(
            'name' => 'category_name',
            'rname' => 'name',
            'id_name' => 'category_id',
            'vname' => 'LBL_CATEGORY_NAME',
            'type' => 'nestedset',
            'isnull' => 'true',
            'config_provider' => 'KBContents',
            'category_provider' => 'Categories',
            'module' => 'Categories',
            'table' => 'categories',
            'massupdate' => false,
            'source' => 'non-db',
            'studio' => 'visible',
        ),
    ),
    'relationships' => array(
        'kbcontent_notes' => array(
            'lhs_module' => 'KBContents',
            'lhs_table' => 'kbcontents',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'KBContentsNotes',
        ),
        'kbcontent_attachments' => array(
            'lhs_module' => 'KBContents',
            'lhs_table' => 'kbcontents',
            'lhs_key' => 'id',
            'rhs_module' => 'Notes',
            'rhs_table' => 'notes',
            'rhs_key' => 'parent_id',
            'relationship_type' => 'one-to-many',
            'relationship_role_column' => 'parent_type',
            'relationship_role_column_value' => 'KBContentsAttachments',
        ),
        'kbdocuments_kbcontents' => array (
            'lhs_module' => 'KBDocuments',
            'lhs_table' => 'kbdocuments',
            'lhs_key' => 'id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbdocument_id',
            'relationship_type' => 'one-to-many'
        ),
        'kbarticles_kbcontents' => array (
            'lhs_module' => 'KBArticles',
            'lhs_table' => 'kbarticles',
            'lhs_key' => 'id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbarticle_id',
            'relationship_type' => 'one-to-many'
        ),
        'localizations' => array (
            'lhs_module' => 'KBContents',
            'lhs_table' => 'kbcontents',
            'lhs_key' => 'kbdocument_id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbdocument_id',
            'join_table' => 'kbcontents',
            'join_key_lhs' => 'kbdocument_id',
            'join_key_rhs' => 'kbdocument_id',
            'relationship_type' => 'one-to-many',
        ),
        'revisions' => array (
            'lhs_module' => 'KBContents',
            'lhs_table' => 'kbcontents',
            'lhs_key' => 'kbarticle_id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbarticle_id',
            'join_table' => 'kbcontents',
            'join_key_lhs' => 'kbarticle_id',
            'join_key_rhs' => 'kbarticle_id',
            'relationship_type' => 'one-to-many',
        ),
        'kbsapprovers_kbcontents' => array (
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbsapprover_id',
            'relationship_type' => 'one-to-many'
        ),
        'relcases_kbcontents' => array (
            'lhs_module' => 'Cases',
            'lhs_table' => 'cases',
            'lhs_key' => 'id',
            'rhs_module' => 'KBContents',
            'rhs_table' => 'kbcontents',
            'rhs_key' => 'kbscase_id',
            'relationship_type' => 'one-to-many'
        ),
    ),
    'indices' => array(
        array(
            'name' => 'idx_kbcontent_name',
            'type' => 'index',
            'fields' => array('name'),
        ),
    ),
    'duplicate_check' => array(
        'enabled' => false,
    ),
    'acls' => array(
        'SugarACLStatic' => true,
        'SugarACLKB' => true,
    ),
    'visibility' => array(
        'KBVisibility' => true,
        'TeamSecurity' => true,
    ),
);

VardefManager::createVardef(
    'KBContents',
    'KBContent',
    array(
        'basic',
        'team_security',
        'assignable',
    )
);
$dictionary['KBContent']['fields']['name']['audited'] = true;
$dictionary['KBContent']['fields']['name']['importable'] = 'required';
$dictionary['KBContent']['fields']['name']['full_text_search'] = array(
    'enabled' => true,
    'elastic' => array(
        'term_vector' => 'yes',
        'store' => 'yes',
    ),
);
