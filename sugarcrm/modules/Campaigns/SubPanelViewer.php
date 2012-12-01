<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * SubPanelViewer
 *
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
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
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: SubPanelViewer.php 50894 2009-09-17 06:26:45Z weidong $

global $gridline;
global $theme;
global $beanList;
global $beanFiles;


if(empty($_REQUEST['module']))
{
	die("'module' was not defined");
}

if(empty($_REQUEST['record']))
{
	die("'record' was not defined");
}

if(!isset($beanList[$_REQUEST['module']]))
{
	die("'".$_REQUEST['module']."' is not defined in \$beanList");
}

$subpanel = $_REQUEST['subpanel'];
$record = $_REQUEST['record'];
$module = $_REQUEST['module'];


$image_path = 'themes/'.$theme.'/images/';

if(empty($_REQUEST['inline']))
{
	insert_popup_header($theme);
}

include('include/SubPanel/SubPanel.php');
$layout_def_key = '';
if(!empty($_REQUEST['layout_def_key'])){
	$layout_def_key = $_REQUEST['layout_def_key'];
}

$subpanel_object = new SubPanel($module, $record, $subpanel,null, $layout_def_key);

$subpanel_object->setTemplateFile('include/SubPanel/SubPanelDynamic.html');

if(!empty($_REQUEST['mkt_id']) && $_REQUEST['mkt_id'] != 'all') {// bug 32910
    $mkt_id = $_REQUEST['mkt_id'];
}

if(!empty($mkt_id)) {
    $subpanel_object->subpanel_defs->_instance_properties['function_parameters']['EMAIL_MARKETING_ID_VALUE'] = $mkt_id;
}
echo (empty($_REQUEST['inline']))?$subpanel_object->get_buttons():'' ;

$subpanel_object->display();

if(empty($_REQUEST['inline']))
{
	insert_popup_footer($theme);
}

?>