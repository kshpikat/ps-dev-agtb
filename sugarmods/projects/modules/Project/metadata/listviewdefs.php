<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

 // $Id: listviewdefs.php 16705 2006-09-12 23:59:52 +0000 (Tue, 12 Sep 2006) jenny $

$listViewDefs['Project'] = array(
	'NAME' => array(
		'width' => '40',  
		'label' => 'LBL_LIST_NAME', 
		'link' => true,
        'default' => true),
    'ESTIMATED_START_DATE' => array(
        'width' => '20',  
        'label' => 'LBL_DATE_START', 
        'link' => false,
        'default' => true),    
    'ESTIMATED_END_DATE' => array(
        'width' => '20',  
        'label' => 'LBL_DATE_END', 
        'link' => false,
        'default' => true), 
    'STATUS' => array(
        'width' => '20',  
        'label' => 'LBL_STATUS', 
        'link' => false,
        'default' => true),         
	'ASSIGNED_USER_NAME' => array(
		'width' => '10', 
		'label' => 'LBL_LIST_ASSIGNED_USER_ID',
        'default' => true),
    //BEGIN SUGARCRM flav=pro ONLY 
    'TEAM_NAME' => array(
        'width' => '2', 
        'label' => 'LBL_LIST_TEAM',
        'related_fields' => array('team_id'),        
        'default' => true),        
    //END SUGARCRM flav=pro ONLY 

);

$listViewDefs['ProjectTemplates'] = array(
	'NAME' => array(
		'width' => '40',  
		'label' => 'LBL_LIST_NAME', 
		'link' => true,
        'default' => true,
        'customCode'=>'<a href="index.php?record={$ID}&action=ProjectTemplatesDetailView&module=Project" class="listViewTdLinkS1">{$NAME}</a>'),
    'ESTIMATED_START_DATE' => array(
        'width' => '20',  
        'label' => 'LBL_DATE_START', 
        'link' => false,
        'default' => true),    
    'ESTIMATED_END_DATE' => array(
        'width' => '20',  
        'label' => 'LBL_DATE_END', 
        'link' => false,
        'default' => true), 
    //BEGIN SUGARCRM flav=pro ONLY 
    'TEAM_NAME' => array(
        'width' => '2', 
        'label' => 'LBL_LIST_TEAM',
        'related_fields' => array('team_id'),        
        'default' => true),        
    //END SUGARCRM flav=pro ONLY 
);

?>
