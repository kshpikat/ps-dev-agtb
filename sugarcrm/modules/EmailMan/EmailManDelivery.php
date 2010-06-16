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
//FILE SUGARCRM flav!=sales ONLY

require_once('include/SugarPHPMailer.php');

$test=false;
if (isset($_REQUEST['mode']) && $_REQUEST['mode']=='test') {
	$test=true;
}
if (isset($_REQUEST['send_all']) && $_REQUEST['send_all']== true) {
	$send_all= true;
}
else  {
	$send_all=false; //if set to true email delivery will continue..to run until all email have been delivered.
}
$GLOBALS['log'] = LoggerManager::getLogger('SugarCRM');
$mail = new SugarPHPMailer();
$admin = new Administration();
$admin->retrieveSettings();
if (isset($admin->settings['massemailer_campaign_emails_per_run'])) {
	$max_emails_per_run=$admin->settings['massemailer_campaign_emails_per_run'];
}
if (empty($max_emails_per_run)) {
	$max_emails_per_run=500;//default
}
//save email copies?
$massemailer_email_copy=0;  //default: save copies of the email.
if (isset($admin->settings['massemailer_email_copy'])) {
    $massemailer_email_copy=$admin->settings['massemailer_email_copy'];
}

$emailsPerSecond = 10;

$mail->setMailerForSystem();
$mail->From     = "no-reply@example.com";
$mail->FromName = "no-reply";
$mail->ContentType="text/html";

$campaign_id=null;
if (isset($_REQUEST['campaign_id']) && !empty($_REQUEST['campaign_id'])) {
	$campaign_id=$_REQUEST['campaign_id'];
}

$db = DBManagerFactory::getInstance();
$emailman = new EmailMan();

    if($test){
        //if this is in test mode, then
        //find all the message that meet the following criteria.
        //1. scheduled send date time is now
        //2. campaign matches the current campaign
        //3. recipient belongs to a propsect list of type test, attached to this campaign 
    
        $select_query =" SELECT em.* FROM emailman em";
        $select_query.=" join prospect_list_campaigns plc on em.campaign_id = plc.campaign_id";
        $select_query.=" join prospect_lists pl on pl.id = plc.prospect_list_id ";
        $select_query.=" WHERE em.list_id = pl.id and pl.list_type = 'test'";
        $select_query.=" AND em.send_date_time <= ". db_convert("'".gmdate($GLOBALS['timedate']->get_db_date_time_format())."'" ,"datetime");    
        $select_query.=" AND (em.in_queue ='0' OR ( em.in_queue ='1' AND em.in_queue_date <= " .db_convert("'". gmdate($GLOBALS['timedate']->get_db_date_time_format(), strtotime("-1 day")) ."'" ,"datetime")."))";
        $select_query.=" AND em.campaign_id='{$campaign_id}'";
        $select_query.=" ORDER BY em.send_date_time ASC, em.user_id, em.list_id";
    }else{
        //this is not a test.. 
        //find all the message that meet the following criteria.
        //1. scheduled send date time is now
        //2. were never processed or last attempt was 24 hours ago
        $select_query =" SELECT *";
        $select_query.=" FROM $emailman->table_name";
        $select_query.=" WHERE send_date_time <= ". db_convert("'".gmdate($GLOBALS['timedate']->get_db_date_time_format())."'" ,"datetime");
        $select_query.=" AND (in_queue ='0' OR ( in_queue ='1' AND in_queue_date <= " .db_convert("'". gmdate($GLOBALS['timedate']->get_db_date_time_format(), strtotime("-1 day")) ."'" ,"datetime")."))"; 
        
        if (!empty($campaign_id)) {
            $select_query.=" AND campaign_id='{$campaign_id}'";
        }
        $select_query.=" ORDER BY send_date_time ASC,user_id, list_id";
        
    }

do {

	$no_items_in_queue=true;	
	
	$result = $db->limitQuery($select_query,0,$max_emails_per_run);
	global $current_user;
	if(isset($current_user)){
		$temp_user = $current_user;
	}	
	$current_user = new User();
	$startTime = microtime(true);
	
	while(($row = $db->fetchByAssoc($result))!= null){

        //verify the queue item before further processing.
        //we have found cases where users have taken away access to email templates while them message is in queue.
        if (empty($row['campaign_id'])) {
            $GLOBALS['log']->fatal('Skipping emailman entry with empty campaign id' . print_r($row,true));
            continue;  
        }
        if (empty($row['marketing_id'])) {
            $GLOBALS['log']->fatal('Skipping emailman entry with empty marketing id' . print_r($row,true));
            continue;  //do not process this row .
        }

        //fetch user that scheduled the campaign.
        if(empty($current_user) or $row['user_id'] != $current_user->id){
            $current_user->retrieve($row['user_id']);
        }
        
        if (!$emailman->verify_campaign($row['marketing_id'])) {
            $GLOBALS['log']->fatal('Error verifying templates for the campaign, exiting');
            continue; 
        }
        
        
        //verify the email template too..
        //find the template associated with marketing message. make sure that template has a subject and 
        //a non-empty body
        if (!isset($template_status[$row['marketing_id']])) {
            if (!class_exists('EmailMarketing')) {
                
            }
            $current_emailmarketing=new EmailMarketing();
            $current_emailmarketing->retrieve($row['marketing_id']);

            if (!class_exists('EmailTemplate')) {
                
            }
            $current_emailtemplate= new EmailTemplate();
            $current_emailtemplate->retrieve($current_emailmarketing->template_id);
                        
        }
		
		//acquire a lock.
		//if the database does not support repeatable read isolation by default, we might get data that does not meet 
        //the criteria in the original query, and we care most about the in_queue_date and process_date_time, 
        //if they are null or in past(older than 24 horus) then we are okay.
		$lock_query="UPDATE emailman SET in_queue=1, in_queue_date='". gmdate($GLOBALS['timedate']->get_db_date_time_format()) ."' WHERE id = '${row['id']}'";
		$lock_query.=" AND (in_queue ='0' OR ( in_queue ='1' AND in_queue_date <= " .db_convert("'". gmdate($GLOBALS['timedate']->get_db_date_time_format(), strtotime("-1 day")) ."'" ,"datetime")."))"; 

 		//if the query fails to execute.. terminate campaign email process.
 		$lock_result=$db->query($lock_query,true,'Error acquiring a lock for emailman entry.');
		if ($db->dbType=='oci8') {
//BEGIN SUGARCRM flav=ent ONLY
            $lock_count=ocirowcount($lock_result);
//END SUGARCRM flav=ent ONLY
		} else {
			$lock_count=$db->getAffectedRowCount();
		}

		//do not process the message if unable to acquire lock.
		if ($lock_count!= 1) {
			$GLOBALS['log']->fatal("Error acquiring lock for the emailman entry, skipping email delivery. lock status=$lock_count " . print_r($row,true));
			continue;  //do not process this row we will examine it after 24 hrs. the email address based dupe check is in place too.
		}
		
		$no_items_in_queue=false;	
		
	
		foreach($row as $name=>$value){
			$emailman->$name = $value;
		}
	
		//for the campaign process the supression lists.
		if (!isset($current_campaign_id) or empty($current_campaign_id) or $current_campaign_id != $row['campaign_id']) {
			$current_campaign_id= $row['campaign_id'];

			//is this email address suppressed?
			$plc_query= " SELECT prospect_list_id, prospect_lists.list_type,prospect_lists.domain_name FROM prospect_list_campaigns ";
			$plc_query.=" LEFT JOIN prospect_lists on prospect_lists.id = prospect_list_campaigns.prospect_list_id";
			$plc_query.=" WHERE ";
			$plc_query.=" campaign_id='{$current_campaign_id}' ";
			$plc_query.=" AND prospect_lists.list_type in ('exempt_address','exempt_domain')";
			$plc_query.=" AND prospect_list_campaigns.deleted=0";
			$plc_query.=" AND prospect_lists.deleted=0";
			
			$emailman->restricted_domains=array();
			$emailman->restricted_addresses=array();
			
			$result1=$db->query($plc_query);
			while($row1 = $db->fetchByAssoc($result1)){
				if ($row1['list_type']=='exempt_domain') {
					$emailman->restricted_domains[strtolower($row1['domain_name'])]=1;
				} else {
	   			    //find email address of targets in this prospect list.	
					$email_query = "SELECT email_address FROM email_addresses ea JOIN email_addr_bean_rel eabr ON ea.id = eabr.email_address_id JOIN prospect_lists_prospects plp ON eabr.bean_id = plp.related_id AND eabr.bean_module = plp.related_type AND plp.prospect_list_id = '{$row1['prospect_list_id']}' and plp.deleted = 0";
					$email_query_result=$db->query($email_query);

					while($email_address = $db->fetchByAssoc($email_query_result)){
						//ignore empty email addresses;
						if (!empty($email_address['email_address'])) {
                            $emailman->restricted_addresses[strtolower($email_address['email_address'])]=1;
						}
					}
				}
			}
		}

		if(!$emailman->sendEmail($mail,$massemailer_email_copy,$test)){
			$GLOBALS['log']->fatal("Email delivery FAILURE:" . print_r($row,true));		
		} else {
			$GLOBALS['log']->debug("Email delivery SUCCESS:" . print_r($row,true));		
	 	}
		if($mail->isError()){
			$GLOBALS['log']->fatal("Email delivery error:" . print_r($row,true). $mail->ErrorInfo);	
		}
	}

	$send_all=$send_all?!$no_items_in_queue:$send_all;
	
}while ($send_all == true);

if ($admin->settings['mail_sendtype'] == "SMTP") {
	$mail->SMTPClose();
}
if(isset($temp_user)){
	$current_user = $temp_user;	
}
if (isset($_REQUEST['return_module']) && isset($_REQUEST['return_action']) && isset($_REQUEST['return_id'])) {
    $from_wiz=' ';
    if(isset($_REQUEST['from_wiz'])&& $_REQUEST['from_wiz']==true){
        header("Location: index.php?module={$_REQUEST['return_module']}&action={$_REQUEST['return_action']}&record={$_REQUEST['return_id']}&from=test");
    }else{
		header("Location: index.php?module={$_REQUEST['return_module']}&action={$_REQUEST['return_action']}&record={$_REQUEST['return_id']}");
    } 	
} else {
	/* this will be triggered when manually sending off Email campaigns from the
	 * Mass Email Queue Manager.
 	*/
	if(isset($_POST['manual'])) {
		header("Location: index.php?module=EmailMan&action=index"); 
	}
}
?>
