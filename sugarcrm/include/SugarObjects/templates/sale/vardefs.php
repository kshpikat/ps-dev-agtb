<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
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
 * $Id$
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
 
$vardefs = array(
	'fields'=>array(
		'name'=>
		 array(
			'name'=>'name',
			'type'=>'name',
		    'link' => true, // bug 39288 
			'dbType'=>'varchar',
			'vname'=>'LBL_NAME',
			'comment'=>'Name of the Sale',
			'unified_search'=>true,
			'full_text_search'=>array('boost' => 3),
			'audited'=>true,
			'merge_filter'=>'selected',
			'required' => true,
            'importable' => 'required',
            'duplicate_on_record_copy' => 'always',
         ),
		strtolower($object_name).'_type' =>
		 array(
			'name'=>strtolower($object_name).'_type',
			'vname'=>'LBL_TYPE',
			'type'=>'enum',
			'options'=>strtolower($object_name).'_type_dom',
			'len' => 100,
            'duplicate_on_record_copy' => 'always',
			'comment'=>'The Sale is of this type',
		),
		'description'=>
		 array(
		 	'name'=>'description',
		 	'vname'=>'LBL_DESCRIPTION',
		 	'type'=>'text',
		 	'comment'=>'Description of the sale',
            'rows' => 6,
            'cols' => 80,
            'duplicate_on_record_copy' => 'always',
         ),
		 'lead_source' =>
		  array (
		    'name' => 'lead_source',
		    'vname' => 'LBL_LEAD_SOURCE',
		    'type' => 'enum',
		    'options' => 'lead_source_dom',
		    'len' => '50',
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Source of the sale',
		  ),
		    'amount' =>
		  array (
		    'name' => 'amount',
		    'vname' => 'LBL_AMOUNT',
		    'type' => 'currency',
		    'dbType' => 'double',
		    'comment' => 'Unconverted amount of the sale',
		    'duplicate_merge'=>'disabled',
		    'required' => true,
            'duplicate_on_record_copy' => 'always',
          ),
		  'amount_usdollar' =>
		  array (
		    'name' => 'amount_usdollar',
		    'vname' => 'LBL_AMOUNT_USDOLLAR',
		    'type' => 'currency',
		    'group'=>'amount',
		    'dbType' => 'double',
		    'disable_num_format' => true,
		    'audited'=>true,
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Formatted amount of the sale',
            'studio' => array(
                'mobile' => false,
            ),
            'readonly' => true,
            'is_base_currency' => true,
		  ),
          'currency_id' =>
          array (
            'name' => 'currency_id',
            'type' => 'currency_id',
            'dbType' => 'id',
            'group'=>'currency_id',
            'vname' => 'LBL_CURRENCY',
            'function'=>array('name'=>'getCurrencyDropDown', 'returns'=>'html'),
            'reportable'=>false,
            'default'=>'-99',
            'duplicate_on_record_copy' => 'always',
            'comment' => 'Currency used for display purposes'
          ),
        'base_rate' => array(
            'name' => 'base_rate',
            'vname' => 'LBL_CURRENCY_RATE',
            'type' => 'decimal',
            'len' => '26,6',
            'required' => true,
            'studio' => false
        ),
          'currency_name'=>
               array(
                'name'=>'currency_name',
                'rname'=>'name',
                'id_name'=>'currency_id',
                'vname'=>'LBL_CURRENCY_NAME',
                'type'=>'relate',
                'isnull'=>'true',
                'table' => 'currencies',
                'module'=>'Currencies',
                'source' => 'non-db',
                'function'=>array('name'=>'getCurrencyNameDropDown', 'returns'=>'html'),
                'studio' => 'false',
                'duplicate_on_record_copy' => 'always',
               ),
           'currency_symbol'=>
               array(
                'name'=>'currency_symbol',
                'rname'=>'symbol',
                'id_name'=>'currency_id',
                'vname'=>'LBL_CURRENCY_SYMBOL',
                'type'=>'relate',
                'isnull'=>'true',
                'table' => 'currencies',
                'module'=>'Currencies',
                'source' => 'non-db',
                'function'=>array('name'=>'getCurrencySymbolDropDown', 'returns'=>'html'),
                'duplicate_on_record_copy' => 'always',
               ),
		   'date_closed' =>
		  array (
		    'name' => 'date_closed',
		    'vname' => 'LBL_DATE_CLOSED',
		    'type' => 'date',
		    'audited'=>true,
		    'required' => true,
		    'comment' => 'Expected or actual date the sale will close',
		    'enable_range_search' => true,
		    'options' => 'date_range_search_dom',
            'duplicate_on_record_copy' => 'always',
          ),
		  'next_step' =>
		  array (
		    'name' => 'next_step',
		    'vname' => 'LBL_NEXT_STEP',
		    'type' => 'varchar',
		    'len' => '100',
		    'comment' => 'The next step in the sales process',
            'duplicate_on_record_copy' => 'always',
            //BEGIN SUGARCRM flav=pro ONLY
		    'merge_filter' => 'enabled',
		    //END SUGARCRM flav=pro ONLY
		  ),
		  'sales_stage' =>
		  array (
		    'name' => 'sales_stage',
		    'vname' => 'LBL_SALES_STAGE',
		    'type' => 'enum',
		    'options' => 'sales_stage_dom',
		    'len' => 100,
		    'audited'=>true,
		    'comment' => 'Indication of progression towards closure',
			'required'=>true,
            'importable' => 'required',
            'duplicate_on_record_copy' => 'always',
            //BEGIN SUGARCRM flav=pro ONLY
		    'merge_filter' => 'enabled',
		    //END SUGARCRM flav=pro ONLY
		  ),
		  'probability' =>
		  array (
		    'name' => 'probability',
		    'vname' => 'LBL_PROBABILITY',
		    'type' => 'int',
		    'dbType' => 'double',
		    'audited'=>true,
		    'comment' => 'The probability of closure',
		    'validation' => array('type' => 'range', 'min' => 0, 'max' => 100),
            'duplicate_on_record_copy' => 'always',
            //BEGIN SUGARCRM flav=pro ONLY
		    'merge_filter' => 'enabled',
            //END SUGARCRM flav=pro ONLY
		  )
	)
);
