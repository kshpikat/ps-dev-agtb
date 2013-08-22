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
$dictionary['Audit'] = array('fields' =>
array(
    'parent_id' => array(
        'name' => 'parent_id',
        'type' => 'id',
        'source' => 'non-db',
    ),
    'date_created' => array(
        'name' => 'date_created',
        'type' => 'datetime',
        'source' => 'non-db',
    ),
    'created_by' => array(
        'name' => 'created_by',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'created_by_username' => array(
        'name' => 'created_by_username',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'field_name' => array(
        'name' => 'field_name',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'data_type' => array(
        'name' => 'data_type',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'before_value_string' => array(
        'name' => 'before_value_string',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'after_value_string' => array(
        'name' => 'after_value_string',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'before' => array(
        'name' => 'before',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'after' => array(
        'name' => 'after',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
    'data_type' => array(
        'name' => 'data_type',
        'type' => 'varchar',
        'source' => 'non-db',
    ),
));
