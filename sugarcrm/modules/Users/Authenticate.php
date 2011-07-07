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
 *(i) the "Powered by SugarCRM" logo and
 *(ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright(C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: Authenticate.php 53116 2009-12-10 01:24:37Z mitani $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
session_regenerate_id(false); 
global $mod_strings;
//BEGIN SUGARCRM flav=pro ONLY
$res = $GLOBALS['sugar_config']['passwordsetting'];
//END SUGARCRM flav=pro ONLY
$authController->login($_REQUEST['user_name'], $_REQUEST['user_password']);
// authController will set the authenticated_user_id session variable
if(isset($_SESSION['authenticated_user_id'])) {
	// Login is successful
	if( $_SESSION['hasExpiredPassword'] == '1' && $_REQUEST['action'] != 'Save'){
		$GLOBALS['module'] = 'Users';
        $GLOBALS['action'] = 'ChangePassword';
        ob_clean();
        header("Location: index.php?module=Users&action=ChangePassword");
        sugar_cleanup(true);
}
    global $record;
    global $current_user;
    
    //BEGIN SUGARCRM flav=pro ONLY
    if ( isset($_SESSION['isMobile']) 
            && ( empty($_REQUEST['login_module']) || $_REQUEST['login_module'] == 'Users' ) 
            && ( empty($_REQUEST['login_action']) || $_REQUEST['login_action'] == 'wirelessmain' ) ) {
        $last_module = $current_user->getPreference('wireless_last_module');
        if ( !empty($last_module) ) {
            $_REQUEST['login_module'] = $last_module;
            $_REQUEST['login_action'] = 'wirelessmodule';
        }
    }
    //END SUGARCRM flav=pro ONLY
    
    $GLOBALS['module'] = !empty($_REQUEST['login_module']) ? '?module='.$_REQUEST['login_module'] : '?module=Home';
   	$GLOBALS['action'] = !empty($_REQUEST['login_action']) ? '&action='.$_REQUEST['login_action'] : '&action=index';
    $GLOBALS['record']= !empty($_REQUEST['login_record']) ? '&record='.$_REQUEST['login_record'] : '';

	// awu: $module is somehow undefined even though the super globals is set, so we set the local variable here
	$module = $GLOBALS['module'];
	$action = $GLOBALS['action'];
	$record = $GLOBALS['record'];
     
    global $current_user;
    //C.L. Added $hasHistory check to respect the login_XXX settings if they are set
    $hasHistory = (!empty($_REQUEST['login_module']) || !empty($_REQUEST['login_action']) || !empty($_REQUEST['login_record']));
    if(isset($current_user) && !$hasHistory){
	    $modListHeader = query_module_access_list($current_user);
	    //try to get the user's tabs
	    $tempList = $modListHeader;
	    $idx = array_shift($tempList);
	    if(!empty($modListHeader[$idx])){
	    	$module = '?module='.$modListHeader[$idx];
	    	$action = '&action=index';
	    	$record = '';
	    }
    }

} else {
	// Login has failed
	$module ="?module=Users";
    $action="&action=Login";
    $record="";
}

// construct redirect url
$url = 'Location: index.php'.$module.$action.$record;
//BEGIN SUGARCRM flav=pro ONLY
// check for presence of a mobile device, redirect accordingly
if(isset($_SESSION['isMobile'])){
    $url = $url . '&mobile=1';
}
//END SUGARCRM flav=pro ONLY

//adding this for bug: 21712.
$GLOBALS['app']->headerDisplayed = true;
sugar_cleanup();
header($url);
?>