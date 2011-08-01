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
 ********************************************************************************/
/*********************************************************************************
 * $Id: ConfigureTabs.php 51995 2009-10-28 21:55:55Z clee $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once 'modules/SNIP/SugarSNIP.php';
if (!is_admin($current_user)) {
    sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
}
global $sugar_config;

/**
    use SugarSNIP instead of SugarSNIP_offlinetest for production
**/

$snip = SugarSNIP::getInstance();
$title = getClassicModuleTitle('Administration', array(
	"<a href='index.php?module=Administration&action=index'>".translate('LBL_MODULE_NAME','Administration')."</a>",
	translate('LBL_CONFIGURE_SNIP')), false);
$sugar_smarty = new Sugar_Smarty();

$sugar_smarty->assign('APP', $GLOBALS['app_strings']);
$sugar_smarty->assign('MOD', $GLOBALS['mod_strings']);
$status=$snip->getStatus();

$message=$status['message'];
$status=$status['status'];

if ($status=='notpurchased'){
    $snipuser = $snip->getSnipUser();
    $sugar_smarty->assign('SNIP_PURCHASEURL',$snip->createPurchaseURL($snipuser));
}

if ($status=='purchased_error'){
	$sugar_smarty->assign('SNIP_ERROR_MESSAGE',$message);
}

$sugar_smarty->assign('TITLE',$title);
$sugar_smarty->assign('SNIP_STATUS',$status);
$sugar_smarty->assign('SNIP_URL',$snip->getSnipURL());
$sugar_smarty->assign('SUGAR_URL',$snip->getURL());

echo $sugar_smarty->fetch('modules/SNIP/RegisterForSnip.tpl');