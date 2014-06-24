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
/*********************************************************************************
 * $Id: field_arrays.php 51719 2009-10-22 17:18:00Z mitani $
 * Description:  Contains field arrays that are used for caching
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$fields_array['Bug'] = array ('column_fields' => Array("id"
		, "name"
		, "bug_number"
		, "date_entered"
		, "date_modified"
		, "modified_user_id"
		, "assigned_user_id"
		//BEGIN SUGARCRM flav=pro ONLY
		,"team_id"
		//END SUGARCRM flav=pro ONLY
		, "status"
		, "found_in_release"
		, "created_by"
		, "resolution"
		, "priority"
		, "description"
		,'type'
		, "fixed_in_release"
		, "work_log"
		, "source"
		, "product_category"
		),
        'list_fields' => Array('id', 'priority', 'status', 'name', 'bug_number', 'assigned_user_name', 'assigned_user_id', 'release', 'found_in_release', 'resolution', 'type'
	//BEGIN SUGARCRM flav=pro ONLY
	, "team_id"
	, "team_name"
	//END SUGARCRM flav=pro ONLY
		),
        'required_fields' => array('name'=>1),
);
?>