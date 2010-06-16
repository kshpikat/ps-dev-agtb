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
 * $Id: field_arrays.php 44886 2009-03-09 16:02:52Z maubert $
 * Description:  Contains field arrays that are used for caching
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
$fields_array['ProductBundle'] = array ('column_fields' => Array("id"
		,"name"
		,"tax"
		,'tax_usdollar'
		,"shipping"
		,'shipping_usdollar'
		,"subtotal"
		,'deal_tot'
		,'deal_tot_usdollar'
		,'new_sub'
		,'new_sub_usdollar'
		,'subtotal_usdollar'
		,"total"
		,'total_usdollar'
		,'currency_id'
		,'bundle_stage'
		,"is_template"
		,"is_editable"
		,"description"
		,"date_entered"
		,"date_modified"
		,"modified_user_id"
		, "created_by"
		, 'team_id'
		),
        'list_fields' =>  array('id', 'name', 'tax', 'shipping', 'subtotal', 'deal_tot', 'new_sub', 'new_sub_usdollar', 'total',
			'tax_usdollar', 'shipping_usdollar', 'subtotal_usdollar', 'deal_tot_usdollar','total_usdollar','bundle_stage','team_id'),

);
?>