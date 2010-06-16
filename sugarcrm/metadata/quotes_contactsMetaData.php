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
//FILE SUGARCRM flav=pro ONLY
$dictionary['quotes_contacts'] = array ( 'table' => 'quotes_contacts'
                                  , 'fields' => array (
       array('name' =>'id', 'type' =>'varchar', 'len'=>'36')
      , array('name' =>'contact_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'quote_id', 'type' =>'varchar', 'len'=>'36', )
      , array('name' =>'contact_role', 'type' =>'varchar', 'len'=>'20', )
      , array ('name' => 'date_modified','type' => 'datetime')
      , array('name' =>'deleted', 'type' =>'bool', 'len'=>'1', 'default'=>'0', 'required'=>false)
		)                                  
		
  , 'indices' => array (
       array('name' =>'quotes_contactspk', 'type' =>'primary', 'fields'=>array('id'))
      , array('name' =>'idx_con_qte_con', 'type' =>'index', 'fields'=>array('contact_id'))
      , array('name' =>'idx_con_qte_opp', 'type' =>'index', 'fields'=>array('quote_id'))
      , array('name' => 'idx_quote_contact_role', 'type'=>'alternate_key', 'fields'=>array('quote_id','contact_role'))
                                                      )
                                                      
  , 'relationships' => array ('quotes_contacts_shipto' => 
   								array('lhs_module'=> 'Quotes', 'lhs_table'=> 'quotes', 
								'lhs_key' => 'id','rhs_module'=> 'Contacts', 'rhs_table'=> 'contacts', 
								'rhs_key' => 'id','relationship_type'=>'many-to-many',
						  		'join_table'=> 'quotes_contacts', 'join_key_lhs'=>'quote_id', 'join_key_rhs'=>'contact_id',
							    'relationship_role_column'=>'contact_role', 'relationship_role_column_value'=>'Ship To'
						  		),
					   			'quotes_contacts_billto' => 
   								array('lhs_module'=> 'Quotes', 'lhs_table'=> 'quotes', 
								'lhs_key' => 'id','rhs_module'=> 'Contacts', 'rhs_table'=> 'contacts', 
								'rhs_key' => 'id','relationship_type'=>'many-to-many',
						  		'join_table'=> 'quotes_contacts', 'join_key_lhs'=>'quote_id', 'join_key_rhs'=>'contact_id',
							    'relationship_role_column'=>'contact_role', 'relationship_role_column_value'=>'Bill To'
						  		)						  		
							)
)
?>
