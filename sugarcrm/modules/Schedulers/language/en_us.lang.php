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
/*********************************************************************************
 * $Id: en_us.lang.php 56115 2010-04-26 17:08:09Z kjing $
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
global $sugar_config;
 
$mod_strings = array (
// OOTB Scheduler Job Names:
'LBL_OOTB_WORKFLOW'		=> 'Process Workflow Tasks',
'LBL_OOTB_REPORTS'		=> 'Run Report Generation Scheduled Tasks',
'LBL_OOTB_IE'			=> 'Check Inbound Mailboxes',
'LBL_OOTB_BOUNCE'		=> 'Run Nightly Process Bounced Campaign Emails',
'LBL_OOTB_CAMPAIGN'		=> 'Run Nightly Mass Email Campaigns',
'LBL_OOTB_PRUNE'		=> 'Prune Database on 1st of Month',
'LBL_OOTB_TRACKER'		=> 'Prune Tracker Tables',
'LBL_UPDATE_TRACKER_SESSIONS' => 'Update tracker_sessions Table',
//BEGIN SUGARCRM flav=dce ONLY
'LBL_OOTB_DCE_CLNUP'          => 'Close loop on completed DCE actions',
'LBL_OOTB_DCE_REPORT'         => 'Create Action to gather daily reports',
'LBL_OOTB_DCE_SALES_REPORT'   => 'Create weekly Sales Report Email',
//END SUGARCRM flav=dce ONLY

// List Labels
'LBL_LIST_JOB_INTERVAL' => 'Interval:',
'LBL_LIST_LIST_ORDER' => 'Schedulers:',
'LBL_LIST_NAME' => 'Scheduler:',
'LBL_LIST_RANGE' => 'Range:',
'LBL_LIST_REMOVE' => 'Remove:',
'LBL_LIST_STATUS' => 'Status:',
'LBL_LIST_TITLE' => 'Schedule List:',
'LBL_LIST_EXECUTE_TIME' => 'Will Run At:',
// human readable:
'LBL_SUN'		=> 'Sunday',
'LBL_MON'		=> 'Monday',
'LBL_TUE'		=> 'Tuesday',
'LBL_WED'		=> 'Wednesday',
'LBL_THU'		=> 'Thursday',
'LBL_FRI'		=> 'Friday',
'LBL_SAT'		=> 'Saturday',
'LBL_ALL'		=> 'Every Day',
'LBL_EVERY_DAY'	=> 'Every day ',
'LBL_AT_THE'	=> 'At the ',
'LBL_EVERY'		=> 'Every ',
'LBL_FROM'		=> 'From ',
'LBL_ON_THE'	=> 'On the ',
'LBL_RANGE'		=> ' to ',
'LBL_AT' 		=> ' at ',
'LBL_IN'		=> ' in ',
'LBL_AND'		=> ' and ',
'LBL_MINUTES'	=> ' minutes ',
'LBL_HOUR'		=> ' hours',
'LBL_HOUR_SING'	=> ' hour',
'LBL_MONTH'		=> ' month',
'LBL_OFTEN'		=> ' As often as possible.',
'LBL_MIN_MARK'	=> ' minute mark',


// crontabs
'LBL_MINS' => 'min',
'LBL_HOURS' => 'hrs',
'LBL_DAY_OF_MONTH' => 'date',
'LBL_MONTHS' => 'mo',
'LBL_DAY_OF_WEEK' => 'day',
'LBL_CRONTAB_EXAMPLES' => 'The above uses standard crontab notation.',
'LBL_CRONTAB_SERVER_TIME_PRE' =>  'The cron specifications run based on the server timezone (',
'LBL_CRONTAB_SERVER_TIME_POST' => '). Please specify the scheduler execution time accordingly.',
// Labels
'LBL_ALWAYS' => 'Always',
'LBL_CATCH_UP' => 'Execute If Missed',
'LBL_CATCH_UP_WARNING' => 'Uncheck if this job may take more than a moment to run.',
'LBL_DATE_TIME_END' => 'Date & Time End',
'LBL_DATE_TIME_START' => 'Date & Time Start',
'LBL_INTERVAL' => 'Interval',
'LBL_JOB' => 'Job',
'LBL_LAST_RUN' => 'Last Successful Run',
'LBL_MODULE_NAME' => 'Sugar Scheduler',
'LBL_MODULE_TITLE' => 'Schedulers',
'LBL_NAME' => 'Job Name',
'LBL_NEVER' => 'Never',
'LBL_NEW_FORM_TITLE' => 'New Schedule',
'LBL_PERENNIAL' => 'perpetual',
'LBL_SEARCH_FORM_TITLE' => 'Scheduler Search',
'LBL_SCHEDULER' => 'Scheduler:',
'LBL_STATUS' => 'Status',
'LBL_TIME_FROM' => 'Active From',
'LBL_TIME_TO' => 'Active To',
'LBL_WARN_CURL_TITLE' => 'cURL Warning:',
'LBL_WARN_CURL' => 'Warning:',
'LBL_WARN_NO_CURL' => 'This system does not have the cURL libraries enabled/compiled into the PHP module (--with-curl=/path/to/curl_library).  Please contact your administrator to resolve this issue.  Without the cURL functionality, the Scheduler cannot thread its jobs.',
'LBL_BASIC_OPTIONS' => 'Basic Setup',
'LBL_ADV_OPTIONS'		=> 'Advanced Options',
'LBL_TOGGLE_ADV' => 'Show Advanced Options',
'LBL_TOGGLE_BASIC' => 'Show Basic Options',
// Links
'LNK_LIST_SCHEDULER' => 'Schedulers',
'LNK_NEW_SCHEDULER' => 'Create Scheduler',
'LNK_LIST_SCHEDULED' => 'Scheduled Jobs',
//BEGIN SUGARCRM flav=int ONLY
'LNK_TEST_SCHEDULER' => 'Test Run Schedulers',
//END SUGARCRM flav=int ONLY
// Messages
'SOCK_GREETING' => "\nThis is the interface for SugarCRM Schedulers Service. \n[ Available daemon commands: start|restart|shutdown|status ]\nTo quit, type 'quit'.  To shutdown the service 'shutdown'.\n",
'ERR_DELETE_RECORD' => 'You must specify a record number to delete the schedule.',
'ERR_CRON_SYNTAX' => 'Invalid Cron syntax',
'NTC_DELETE_CONFIRMATION' => 'Are you sure you want to delete this record?',
'NTC_STATUS' => 'Set status to Inactive to remove this schedule from the Scheduler dropdown lists',
'NTC_LIST_ORDER' => 'Set the order this schedule will appear in the Scheduler dropdown lists',
'LBL_CRON_INSTRUCTIONS_WINDOWS' => 'To Setup Windows Scheduler',
'LBL_CRON_INSTRUCTIONS_LINUX' => 'To Setup Crontab',
'LBL_CRON_LINUX_DESC' => 'Note: In order to run Sugar Schedulers, add the following line to the crontab file: ',
'LBL_CRON_WINDOWS_DESC' => 'Note: In order to run the Sugar schedulers, create a batch file to run using Windows Scheduled Tasks. The batch file should include the following commands: ',
'LBL_NO_PHP_CLI' => 'If your host does not have the PHP binary available, you can use wget or curl to launch your Jobs.<br>for wget: <b>*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;wget --quiet --non-verbose '.$sugar_config['site_url'].'/cron.php > /dev/null 2>&1</b><br>for curl: <b>*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;curl --silent '.$sugar_config['site_url'].'/cron.php > /dev/null 2>&1', 
// Subpanels
'LBL_JOBS_SUBPANEL_TITLE'	=> 'Job Log',
'LBL_EXECUTE_TIME'			=> 'Execute Time',

//jobstrings
'LBL_REFRESHJOBS' => 'Refresh Jobs',
'LBL_POLLMONITOREDINBOXES' => 'Check Inbound Mail Accounts',
'LBL_PERFORMFULLFTSINDEX' => 'Full-text Search Index System',

//BEGIN SUGARCRM flav!=dce ONLY
'LBL_RUNMASSEMAILCAMPAIGN' => 'Run Nightly Mass Email Campaigns',
'LBL_POLLMONITOREDINBOXESFORBOUNCEDCAMPAIGNEMAILS' => 'Run Nightly Process Bounced Campaign Emails',
//END SUGARCRM flav!=dce ONLY
'LBL_PRUNEDATABASE' => 'Prune Database on 1st of Month',
'LBL_TRIMTRACKER' => 'Prune Tracker Tables',
//BEGIN SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav!=dce ONLY
'LBL_PROCESSWORKFLOW' => 'Process Workflow Tasks',
//END SUGARCRM flav!=dce ONLY
'LBL_PROCESSQUEUE' => 'Run Report Generation Scheduled Tasks',
'LBL_UPDATETRACKERSESSIONS' => 'Update Tracker Session Tables',
//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav=dce ONLY
'LBL_DCEACTIONCLEANUP' => 'dceActionCleanup',
'LBL_DCECREATEREPORTDATA' => 'dceCreateReportData',
'LBL_DCECREATESALESREPORT' => 'dceCreateSalesReport',
//END SUGARCRM flav=dce ONLY
//BEGIN SUGARCRM flav=int ONLY
'LBL_TESTEMAIL' => 'testEmail',
//END SUGARCRM flav=int ONLY
);
?>
