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

$dictionary['OutboundEmail'] = array (
    'table' => 'outbound_email',
    'acls' => array(
        'SugarACLOutboundEmail' => true,
    ),
	'fields' => array (
		'id' => array (
			'name' => 'id',
			'vname' => 'LBL_ID',
			'type' => 'id',
			'required' => true,
			'reportable' => false,
            'mandatory_fetch' => true,
		),
		'name' => array (
			'name' => 'name',
			'vname' => 'LBL_NAME',
			'type' => 'varchar',
			'len' => 50,
			'required' => true,
			'reportable' => false,
		),
		'type' => array (
			'name' => 'type',
			'vname' => 'LBL_TYPE',
			'type' => 'varchar',
			'len' => 15,
			'required' => true,
			'default' => 'user',
			'reportable' => false,
            'mandatory_fetch' => true,
            'readonly' => true,
		),
		'user_id' => array (
			'name' => 'user_id',
			'vname' => 'LBL_USER_ID',
			'type' => 'id',
			'required' => true,
			'reportable' => false,
            'mandatory_fetch' => true,
            'readonly' => true,
		),
		'mail_sendtype' => array(
			'name' => 'mail_sendtype',
			'vname' => 'LBL_MAIL_SENDTYPE',
			'type' => 'varchar',
			'len' => 8,
			'required' => true,
			'default' => 'smtp',
			'reportable' => false,
		),
		'mail_smtptype' => array(
			'name' => 'mail_smtptype',
            'vname' => 'LBL_EMAIL_PROVIDER',
            'type' => 'enum',
            'options' => 'mail_smtptype_options',
			'len' => 20,
			'required' => true,
            'default' => 'other',
			'reportable' => false,
		),
		'mail_smtpserver' => array(
			'name' => 'mail_smtpserver',
			'vname' => 'LBL_MAIL_SMTPSERVER',
			'type' => 'varchar',
			'len' => 100,
			'required' => false,
			'reportable' => false,
		),
		'mail_smtpport' => array(
			'name' => 'mail_smtpport',
			'vname' => 'LBL_MAIL_SMTPPORT',
			'type' => 'int',
			'len' => 5,
            'default' => 465,
            'reportable' => false,
            'disable_num_format' => true,
		),
		'mail_smtpuser' => array(
			'name' => 'mail_smtpuser',
			'vname' => 'LBL_MAIL_SMTPUSER',
			'type' => 'varchar',
			'len' => 100,
			'reportable' => false,
		),
		'mail_smtppass' => array(
			'name' => 'mail_smtppass',
			'vname' => 'LBL_MAIL_SMTPPASS',
			'type' => 'encrypt',
			'len' => 100,
			'reportable' => false,
            'duplicate_on_record_copy' => 'no',
		),
		'mail_smtpauth_req' => array(
			'name' => 'mail_smtpauth_req',
			'vname' => 'LBL_MAIL_SMTPAUTH_REQ',
			'type' => 'bool',
			'default' => 0,
			'reportable' => false,
		),
		'mail_smtpssl' => array(
			'name' => 'mail_smtpssl',
			'vname' => 'LBL_MAIL_SMTPSSL',
            'type' => 'enum',
            'options' => 'email_settings_for_ssl',
			'len' => 1,
            'default' => 1,
			'reportable' => false,
		),
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'duplicate_on_record_copy' => 'no',
        ),
	),
	'indices' => array (
		array(
			'name' => 'outbound_email_pk',
			'type' =>'primary',
			'fields' => array(
				'id'
			)
		),
		array(
			'name' => 'oe_user_id_idx',
			'type' =>'index',
			'fields' => array(
				'id',
				'user_id',
			)
		),
	), /* end indices */
);
