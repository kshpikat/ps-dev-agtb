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

 * Description:  Contains field arrays that are used for caching
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$fields_array['EmailTemplate'] = array ('column_fields' => Array("id"
		, "date_entered"
		, "date_modified"
		, "modified_user_id"
		, "created_by"
		, "description"
		, "subject"
		, "body"
		, "body_html"
		, "name"
		, "published"
//BEGIN SUGARCRM flav=pro ONLY
		,"team_id"
		,"team_name"
		,"base_module"
		,"from_name"
		,"from_address"
//END SUGARCRM flav=pro ONLY
		),
        'list_fields' =>  Array('id', 'name', 'description','date_modified'
//BEGIN SUGARCRM flav=pro ONLY
	, "team_id"
//END SUGARCRM flav=pro ONLY
	),
    'required_fields' => array("name"=>1),
);
?>