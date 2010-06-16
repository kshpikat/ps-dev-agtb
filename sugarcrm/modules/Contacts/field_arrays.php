<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: field_arrays.php 51841 2009-10-26 20:33:15Z jmertic $
 * Description:  Contains field arrays that are used for caching
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$fields_array['Contact'] = array ('column_fields' => Array("id"
		,"date_entered"
		,"date_modified"
		,"modified_user_id"
		,"assigned_user_id"
		, "created_by"
//BEGIN SUGARCRM flav=pro ONLY
		,"team_id"
//END SUGARCRM flav=pro ONLY
		,"salutation"
		,"first_name"
		,"last_name"
		,"lead_source"
		,"title"
		,"department"
		,"birthdate"
		,"reports_to_id"
		,"do_not_call"
		,"phone_home"
		,"phone_mobile"
		, "portal_name"
		, "portal_app"
		,"portal_active"
        ,"portal_password"
		,"phone_work"
		,"phone_other"
		,"phone_fax"
		,"assistant"
		,"assistant_phone"
		,"email_opt_out"
		,"primary_address_street"
		,"primary_address_city"
		,"primary_address_state"
		,"primary_address_postalcode"
		,"primary_address_country"
		,"alt_address_street"
		,"alt_address_city"
		,"alt_address_state"
		,"alt_address_postalcode"
		,"alt_address_country"
		,"description"
		,'invalid_email'
//BEGIN SUGARCRM flav!=sales ONLY
		,"campaign_id"
//END SUGARCRM flav!=sales ONLY
		),
        'list_fields' => Array('id', 'first_name', 'last_name', 'account_name', 'account_id', 'title', 'phone_work', 'assigned_user_name', 'assigned_user_id', "case_role", 'case_rel_id', 'opportunity_role', 'opportunity_rel_id'
//BEGIN SUGARCRM flav=pro ONLY
	, 'quote_role'
	, 'quote_rel_id'
	, "team_id"
	, "team_name"
//END SUGARCRM flav=pro ONLY
    ,'invalid_email'
//BEGIN SUGARCRM flav=dce ONLY
    ,'dceinstance_role' 
    ,'dceinstance_rel_id'
//END SUGARCRM flav=dce ONLY
		),
        'required_fields' => array("last_name"=>1),
);
?>