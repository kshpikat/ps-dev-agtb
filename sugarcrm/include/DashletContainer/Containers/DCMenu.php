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

class DCMenu extends DashletContainer
{	
	protected function getMenuItem($module)
	{
	    $imageURL = SugarThemeRegistry::current()->getImageURL("icon_{$module}_bar_32.png");
	    if (empty($imageURL))
	    	$imageURL = SugarThemeRegistry::current()->getImageURL("icon_generic_bar_32.png");
	    $module_mod_strings = return_module_language($GLOBALS['current_language'], $module);
	    if ( isset($module_mod_strings['LNK_NEW_RECORD']) )
	        $createRecordTitle = $module_mod_strings['LNK_NEW_RECORD'];
	    elseif ( isset($module_mod_strings['LNK_NEW_'.strtoupper($GLOBALS['beanList'][$module])]) )
	        $createRecordTitle = $module_mod_strings['LNK_NEW_'.strtoupper($GLOBALS['beanList'][$module])];
	    else
	        $createRecordTitle = $GLOBALS['app_strings']['LBL_CREATE_BUTTON_LABEL'].' '.$module_mod_strings['LBL_MODULE_NAME'];
	    return <<<EOQ
		<li><a href="javascript: if ( DCMenu.menu ) DCMenu.menu('$module','$createRecordTitle');"><img class='icon' src='{$imageURL}' alt='{$createRecordTitle}' title='{$createRecordTitle}'></a></li>	
EOQ;

	}
	
	protected function getDynamicMenuItem($def)
	{
		if (!empty($def['icon']))
			$imageURL = $def['icon'];
	    else
	    	$imageURL = SugarThemeRegistry::current()->getImageURL("icon_generic_bar_32.png");
	    
	    $module = !empty($def['module']) ? $def['module'] : "";
	    $label = isset($def['label']) ? translate($def['label'], $module) : "";
	    $action = isset($def['action']) ? $def['action'] : "DCMenu.menu('$module','$label');";
	    $script = isset($def['script_url']) ? '<script type="text/javascript" src="' . $def['script_url'] . '"></script>' : "";
	    return <<<EOQ
		<li>$script<a href="javascript: $action"><img class="icon" src="{$imageURL}" alt="{$label}" title="{$label}"></a></li>	
EOQ;
	}
	
	public function getLayout()
	{
		$record = !empty($_REQUEST['record'])?$_REQUEST['record']:'';
		$module = !empty($_REQUEST['module'])?$_REQUEST['module']:'';
		
		global $current_user;
				$notificationsHTML = '';
		if(is_admin($current_user)){
		    $shouldSkip = sugar_cache_retrieve('dcmenu_check_notifications');
		    $unreadNotifications = 0;
		    if ( $shouldSkip != '1' ) {
                require_once('modules/Notifications/Notifications.php');
                $n = new Notifications();
                $unreadNotifications = $n->getSystemNotificationsCount();
                if ( $unreadNotifications == 0 )
                    sugar_cache_put('dcmenu_check_notifications','1');
            }

		if($unreadNotifications > 0) {
			$iconImageUrl = SugarThemeRegistry::current()->getImageURL("dcMenuSugarCube_alert.png");
			$code = '<div id="notifCount" class="notifCount">'.$unreadNotifications.'</div>';
			$class = "";
		} else {
			$iconImageUrl = SugarThemeRegistry::current()->getImageURL("dcMenuSugarCube.png");
			$code = '';
			$class = ' class="none"';
		}
		
		$notificationsHTML = <<<EOQ
		
			<div id="dcmenuSugarCube" $class>
			  <a href="javascript: DCMenu.notificationsList();" class="notice"><img class='dc_notif_icon' src='$iconImageUrl' border="0"></a>
			  $code
			</div>	
			
EOQ;
		} else {
			$iconImageUrl = SugarThemeRegistry::current()->getImageURL("dcMenuSugarCube.png");
				$code = '';
				$class = ' class="none"';
				$notificationsHTML = <<<EOQ
			
			<div id="dcmenuSugarCube" $class>
			  <img class='dc_notif_icon' src='$iconImageUrl' border="0">
			  $code
			</div>	
EOQ;
		}
		$action = $GLOBALS['app']->controller->action;
		//BEGIN SUGARCRM flav=sugarsurvey ONLY
		$version = urlencode($GLOBALS['sugar_version']);
		$flavor = urlencode($GLOBALS['sugar_flavor']);
		$build = urlencode($GLOBALS['sugar_build']);
		$sugarsurvey = "http://survey-js.sugarcrm.com/SugarSurvey/index.php?do=jssurvey&version={$version}&build={$build}&flavor={$flavor}";
		//END SUGARCRM flav=sugarsurvey ONLY
		$html = "<script src='".getJSPath('include/DashletContainer/Containers/DCMenu.js')."'></script>";
		$html .= <<<EOQ
		<script>
			
		YUI().use('node-base', 'event-key', function(Y){
			
			function init(){
				DCMenu.module = '$module';
				DCMenu.record = '$record';
				DCMenu.action = '$action';
				//BEGIN SUGARCRM flav=notifications ONLY
				setInterval("DCMenu.checkForNewNotifications()",10000);
				//END SUGARCRM flav=notifications ONLY
				
				// store the return value from Y.on to remove the listener later
    			var handle = Y.on('key', function(e) {
        			DCMenu.spot(document.getElementById('sugar_spot_search').value);
        			e.halt();
				 }, '#sugar_spot_search', 'down:13', Y);


			}
	    	Y.on("domready", init);
			
		});
		</script>
		//BEGIN SUGARCRM flav=sugarsurvey ONLY
		<script src='{$sugarsurvey}'></script>
		//END SUGARCRM flav=sugarsurvey ONLY
EOQ;
		$html .= <<<EOQ
		<div id='dcmenutop'></div>
		<div id='dcmenu' class='dcmenu dcmenuFloat'>
		{$notificationsHTML}
		<div class="dcmenuDivider" style="float: left; margin-left: 15px;"></div>
		
		<div id="dcmenuContainer">
		<ul id="dcmenuitems">

EOQ;
        $DCActions = array();
        $dynamicDCActions = array();
		$actions_path = "include/DashletContainer/Containers/DCActions.php";
		if (is_file('custom/' . $actions_path))
		    include('custom/' . $actions_path);
		else
		    include($actions_path); 
		if (is_file('custom/application/Ext/DashletContainer/Containers/dcactions.ext.php'))
			include 'custom/application/Ext/DashletContainer/Containers/dcactions.ext.php';

		foreach($DCActions as $action){
			if(ACLController::checkAccess($action, 'edit', true)) {
			   $html .= $this->getMenuItem($action);	
			}
		}
		
		foreach($dynamicDCActions as $def){
			$html .= $this->getDynamicMenuItem($def);
		}

		$iconSearch = SugarThemeRegistry::current()->getImageURL("dcMenuSearchBtn.png");
		$html .= <<<EOQ
		</ul>
		</div>
		
		<div id="glblSearchBtn"><a href="javascript: DCMenu.spot(document.getElementById('sugar_spot_search').value);"><img src="$iconSearch" class="icon" align="top"></a></div>
		<div id="dcmenuSearchDiv"><div id="sugar_spot_search_div"><input size=20 id='sugar_spot_search'></div>
		</div>
		</div>
		
EOQ;
		return array('html'=>$html, 'jsfiles'=>array());
	}
}