<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
$viewdefs['Bugs']['DetailView'] = array(
'templateMeta' => array('form' => array('buttons'=>array('EDIT', 'DUPLICATE', 'DELETE', 'FIND_DUPLICATES',)),
                        'maxColumns' => '2',
                        'widths' => array(
                                        array('label' => '10', 'field' => '30'),
                                        array('label' => '10', 'field' => '30')
                                        ),
                        ),

'panels' =>array (
  'lbl_bug_information'=>array(
	  array (
	    'bug_number',
	    'priority',
	  ),

	  array (
	    array (
	      'name' => 'name',
	      'label' => 'LBL_SUBJECT',
	    ),
	    'status',
	  ),

	  array (
	    'type',
	    'source',
	  ),

	  array (
	    'product_category',
	    'resolution',
	  ),

	  array (
	    array (
	      'name' => 'found_in_release',
	      'label' => 'LBL_FOUND_IN_RELEASE',
	    ),
	    'fixed_in_release',
	  ),

	  array (
	    'description',
	  ),

	  array (
	    'work_log',
	  ),

	  //BEGIN SUGARCRM flav=ent ONLY
	  array (
	     array('name'=>'portal_viewable',
			   'label' => 'LBL_SHOW_IN_PORTAL',
		       'hideIf' => 'empty($PORTAL_ENABLED)',
	         ),
	  ),
	  //END SUGARCRM flav=ent ONLY
  ),

      'LBL_PANEL_ASSIGNMENT' =>
      array (

        array (

          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),

          array (
            'name' => 'date_modified',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),

        array (
          //BEGIN SUGARCRM flav=pro ONLY
          'team_name',
          //END SUGARCRM flav=pro ONLY

          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
            'label' => 'LBL_DATE_ENTERED',
          ),

        ),
      ),
)
);
?>