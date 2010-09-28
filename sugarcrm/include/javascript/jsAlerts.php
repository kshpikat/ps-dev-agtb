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

require_once("include/utils/db_utils.php");

class jsAlerts{
	var $script;
	
	function jsAlerts(){
		global $app_strings;
		$this->script .= <<<EOQ
		if(window.addEventListener){
			window.addEventListener("load", checkAlerts, false);
		}else{
			window.attachEvent("onload", checkAlerts);
		}
		
EOQ;
		$this->addActivities();
		if(!empty($GLOBALS['sugar_config']['enable_timeout_alerts'])){
			$this->addAlert($app_strings['ERROR_JS_ALERT_SYSTEM_CLASS'], $app_strings['ERROR_JS_ALERT_TIMEOUT_TITLE'],'', $app_strings['ERROR_JS_ALERT_TIMEOUT_MSG_1'], (session_cache_expire() - 2) * 60 );	
			$this->addAlert($app_strings['ERROR_JS_ALERT_SYSTEM_CLASS'], $app_strings['ERROR_JS_ALERT_TIMEOUT_TITLE'],'', $app_strings['ERROR_JS_ALERT_TIMEOUT_MSG_2'], (session_cache_expire()) * 60 , 'index.php');
		}
	}
	function addAlert($type, $name, $subtitle, $description, $countdown, $redirect=''){
		$this->script .= 'addAlert("' . addslashes($type) .'", "' . addslashes($name). '","' . addslashes($subtitle). '", "'. addslashes(str_replace(array("\r", "\n"), array('','<br>'),$description)) . '",' . $countdown . ',"'.addslashes($redirect).'")' . "\n";
	}
	
	function getScript(){
		return "<script>" . $this->script . "</script>";	
	}
	
	function addActivities(){
		global $app_list_strings, $timedate, $current_user, $app_strings;
		global $sugar_config;

		if (empty($current_user->id)) {
			return;
		}
			
		// cn: get a boundary limiter
		$dateTimeMax = gmdate($GLOBALS['timedate']->get_db_date_time_format(), time() + $app_list_strings['reminder_max_time']);
		$dateTimeNow = TimeDate2::getInstance()->nowDb();
		$dateMax = gmdate($GLOBALS['timedate']->dbDayFormat, time() + $app_list_strings['reminder_max_time']);
		$todayGMT = gmdate($GLOBALS['timedate']->dbDayFormat);
		
		global $db;
		// Prep Meetings Query
		if ($db->dbType == 'mysql') {
			$selectMeetings = "
				SELECT meetings.id, name,reminder_time, description,location, date_start, assigned_user_id
				FROM meetings LEFT JOIN meetings_users ON meetings.id = meetings_users.meeting_id 
				WHERE meetings_users.user_id ='".$current_user->id."' 
					AND meetings.reminder_time != -1
					AND meetings_users.deleted != 1
					AND meetings.status != 'Held'
				    AND date_start >= '".$dateTimeNow."'"; 
			
			// if we're looking at bridging into the next day as 
			if($dateMax == $todayGMT) {
				$selectMeetings .= " AND date_start <= '".$dateTimeMax."'";
			}
		} 

		elseif ($db->dbType == 'oci8')
		{  	
			//BEGIN SUGARCRM flav=ent ONLY
			//assuming oracle, since we only support 2 databases.	
			$selectMeetings = "
				SELECT meetings.id, name,reminder_time, description,location, date_start,  assigned_user_id
				FROM meetings LEFT JOIN meetings_users ON meetings.id = meetings_users.meeting_id 
				WHERE meetings_users.user_id ='".$current_user->id."' 
					AND meetings.reminder_time != -1
					AND meetings_users.deleted != 1
					AND meetings.status != 'Held'". 
					"AND date_start >= to_date('$dateTimeNow','YYYY-MM-DD hh24:mi:ss')	";
			
			// if we're looking at bridging into the next day as 
			if($dateMax == $todayGMT) {
					$selectMeetings .=" AND date_start <= to_date('$dateTimeNow','YYYY-MM-DD hh24:mi:ss')	";
			}
			       	//END SUGARCRM flav=ent ONLY
		}elseif($db->dbType == 'mssql')
		{
			$selectMeetings = "
				SELECT meetings.id, name,reminder_time, CAST(description AS varchar(8000)),location, date_start, assigned_user_id 
				FROM meetings LEFT JOIN meetings_users ON meetings.id = meetings_users.meeting_id 
				WHERE meetings_users.user_id ='".$current_user->id."' 
					AND meetings.reminder_time != -1
					AND meetings_users.deleted != 1
					AND meetings.status != 'Held'
					AND date_start  >= '".$dateTimeNow."'";
			
			// if we're looking at bridging into the next day as 
			if($dateMax == $todayGMT) 
			{
				$selectMeetings .= " AND date_start  <= '".$dateTimeMax."'";
			}
		}

		$result = $db->query($selectMeetings);

		///////////////////////////////////////////////////////////////////////
		////	MEETING INTEGRATION
		$meetingIntegration = null;
		if(isset($sugar_config['meeting_integration']) && !empty($sugar_config['meeting_integration'])) {
			if(!class_exists($sugar_config['meeting_integration'])) {
				require_once("modules/{$sugar_config['meeting_integration']}/{$sugar_config['meeting_integration']}.php");
			}
			$meetingIntegration = new $sugar_config['meeting_integration']();
		}
		////	END MEETING INTEGRATION
		///////////////////////////////////////////////////////////////////////
		
		while($row = $db->fetchByAssoc($result)) {
			// need to concatenate since GMT times can bridge two local days
			$timeStart = strtotime($row['date_start']);
			$timeRemind = $row['reminder_time'];
			$timeStart -= $timeRemind;
			
			$url = 'index.php?action=DetailView&module=Meetings&record=' . $row['id'];
			$instructions = $app_strings['MSG_JS_ALERT_MTG_REMINDER_MEETING_MSG'];
		
			///////////////////////////////////////////////////////////////////
			////	MEETING INTEGRATION
			if(!empty($meetingIntegration) && $meetingIntegration->isIntegratedMeeting($row['id'])) {
				$url = $meetingIntegration->miUrlGetJsAlert($row);
				$instructions = $meetingIntegration->miGetJsAlertInstructions();
			}
			////	END MEETING INTEGRATION
			///////////////////////////////////////////////////////////////////
			
			// sanitize topic
			$meetingName = '';
			if(!empty($row['name'])) {
				$meetingName = from_html($row['name']);
				// addAlert() uses double-quotes to pass to popup - escape double-quotes
				//$meetingName = str_replace('"', '\"', $meetingName);
			}
			
			// sanitize agenda
			$desc = '';
			if(!empty($row['description'])) {
				$desc = from_html($row['description']);
				// addAlert() uses double-quotes to pass to popup - escape double-quotes
				//$desc = str_replace('"', '\"', $desc);
			}

			$description = empty($desc) ? '' : $app_strings['MSG_JS_ALERT_MTG_REMINDER_AGENDA'].$desc."\n";
			
			//BEGIN SUGARCRM flav=notifications ONLY
			//Add the notification
			$n = new Notifications();
			$n->name = $app_strings['MSG_JS_ALERT_MTG_REMINDER_MEETING'] .": " . $meetingName;
			$n->description = $app_strings['MSG_JS_ALERT_MTG_REMINDER_LOC'].$row['location']. $description.$instructions;
			$n->assigned_user_id = $current_user->id;
			$n->save(FALSE);
			//END SUGARCRM flav=notifications ONLY
			
			// standard functionality
			$this->addAlert($app_strings['MSG_JS_ALERT_MTG_REMINDER_MEETING'], $meetingName, 
				$app_strings['MSG_JS_ALERT_MTG_REMINDER_TIME'].$timedate->to_display_date_time($row['date_start']), 
				$app_strings['MSG_JS_ALERT_MTG_REMINDER_LOC'].$row['location']. 
				$description. 
				$instructions, 
				$timeStart - strtotime($dateTimeNow), 
				$url
			);
		}

		// Prep Calls Query
		if ($db->dbType == 'mysql') {
	
			$selectCalls = "
				SELECT calls.id, name, reminder_time, description, date_start 
				FROM calls LEFT JOIN calls_users ON calls.id = calls_users.call_id 
				WHERE calls_users.user_id ='".$current_user->id."' 
					AND calls.reminder_time != -1 
					AND calls_users.deleted != 1
					AND calls.status != 'Held'
				and date_start >= '".$dateTimeNow."'"; 
	
			if($dateMax == $todayGMT) {
				$selectCalls .= " AND date_start <= '".$dateTimeMax."'";
			}
		}elseif ($db->dbType == 'oci8') 
		{
			//BEGIN SUGARCRM flav=ent ONLY
			//assuming oracle, since we only support 2 databases.	
			$selectCalls = "
				SELECT calls.id, name, reminder_time, description, date_start
				FROM calls LEFT JOIN calls_users ON calls.id = calls_users.call_id 
				WHERE calls_users.user_id ='".$current_user->id."' 
					AND calls.reminder_time != -1
					AND calls_users.deleted != 1
					AND calls.status != 'Held'" .
				"AND date_start >= to_date('$dateTimeNow','YYYY-MM-DD hh24:mi:ss')	";
			
			// if we're looking at bridging into the next day as 
			if($dateMax == $todayGMT) {
				$selectCalls .=" AND date_start <= to_date('$dateTimeNow','YYYY-MM-DD hh24:mi:ss')	";
			}
			//END SUGARCRM flav=ent ONLY
		}elseif ($db->dbType == 'mssql')  
		{
			
			$selectCalls = "
				SELECT calls.id, name, reminder_time, CAST(description AS varchar(8000)), date_start
				FROM calls LEFT JOIN calls_users ON calls.id = calls_users.call_id 
				WHERE calls_users.user_id ='".$current_user->id."' 
					AND calls.reminder_time != -1 
					AND calls_users.deleted != 1 
					AND calls.status != 'Held'
					AND date_start  >= '".$dateTimeNow."'"; 
							
			if($dateMax == $todayGMT) {
				$selectCalls .= " AND date_start  <= '".$dateTimeMax."'";
			}
		}
       		

		global $db;
		$result = $db->query($selectCalls);

		while($row = $db->fetchByAssoc($result)){
			// need to concatenate since GMT times can bridge two local days
			$timeStart = strtotime($row['date_start']);
			$timeRemind = $row['reminder_time'];
			$timeStart -= $timeRemind;
			$row['description'] = (isset($row['description'])) ? $row['description'] : '';
			
			//BEGIN SUGARCRM flav=notifications ONLY
			//Add the notification
			$n = new Notifications();
			$n->name = $app_strings['MSG_JS_ALERT_MTG_REMINDER_CALL'] . ": ".$row['name'];
			$n->description = $app_strings['MSG_JS_ALERT_MTG_REMINDER_DESC'].$row['description']. $app_strings['MSG_JS_ALERT_MTG_REMINDER_CALL_MSG'];
			$n->assigned_user_id = $current_user->id;
			$n->save(FALSE);
			//END SUGARCRM flav=notifications ONLY
			
			$this->addAlert($app_strings['MSG_JS_ALERT_MTG_REMINDER_CALL'], $row['name'], $app_strings['MSG_JS_ALERT_MTG_REMINDER_TIME'].$timedate->to_display_date_time($row['date_start']) , $app_strings['MSG_JS_ALERT_MTG_REMINDER_DESC'].$row['description']. $app_strings['MSG_JS_ALERT_MTG_REMINDER_CALL_MSG'] , $timeStart - strtotime($dateTimeNow), 'index.php?action=DetailView&module=Calls&record=' . $row['id']);
		}
	}
	
	
}

?>
