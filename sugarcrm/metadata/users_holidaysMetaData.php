<?php
//FILE SUGARCRM flav=pro ONLY
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
 * $Id: additionalDetails.php 13782 2006-06-06 17:58:55Z majed $
 *********************************************************************************/
// adding user-to-holiday relationship
$dictionary['users_holidays'] = array (
    'table' => 'users_holidays',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'user_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'holiday_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => false),
    ),
    'indices' => array (
        array('name' => 'users_holidays_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_user_holi_user', 'type' =>'index', 'fields'=>array('user_id')),
        array('name' => 'idx_user_holi_holi', 'type' =>'index', 'fields'=>array('holiday_id')),
        array('name' => 'users_quotes_alt', 'type'=>'alternate_key', 'fields'=>array('user_id','holiday_id')),
    ),
    'relationships' => array (
        'users_holidays' => array(
			'lhs_module' => 'Users', 
			'lhs_table' => 'users', 
			'lhs_key' => 'id',
			'rhs_module' => 'Holidays', 
			'rhs_table' => 'holidays', 
			'rhs_key' => 'person_id',
			'relationship_type' => 'one-to-many', 
			'relationship_role_column' => 'related_module', 
			'relationship_role_column_value' => NULL,
		),
    ),
);

?>
