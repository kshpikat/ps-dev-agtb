<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/*********************************************************************************

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
'LBL_OOTB_PRUNE_RECORDLISTS'		=> 'Prune Old Record Lists',
'LBL_OOTB_REMOVE_TMP_FILES' => 'Remove temporary files',
'LBL_OOTB_REMOVE_DIAGNOSTIC_FILES' => 'Remove diagnostic tool files',
'LBL_OOTB_REMOVE_PDF_FILES' => 'Remove temporary PDF files',
'LBL_UPDATE_TRACKER_SESSIONS' => 'Update tracker_sessions Table',
'LBL_OOTB_SEND_EMAIL_REMINDERS' => 'Run Email Reminder Notifications',
'LBL_OOTB_CLEANUP_QUEUE' => 'Clean Jobs Queue',
'LBL_OOTB_CREATE_NEXT_TIMEPERIOD' => 'Create Future TimePeriods',
'LBL_OOTB_HEARTBEAT' => 'Sugar Heartbeat',
'LBL_OOTB_KBCONTENT_UPDATE' => 'Update KBContent articles.',
'LBL_OOTB_KBSCONTENT_EXPIRE' => 'Publish approved articles & Expire KB Articles.',
'LBL_OOTB_PROCESS_AUTHOR_JOB' => 'Advanced Workflow Scheduled Job',

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
'LBL_JOB_URL' => 'Job URL',
'LBL_LAST_RUN' => 'Last Successful Run',
'LBL_MODULE_NAME' => 'Sugar Scheduler',
'LBL_MODULE_NAME_SINGULAR' => 'Sugar Scheduler',
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
'LBL_SUGARJOBREMOVEPDFFILES' => 'Remove temporary PDF files',
'LBL_SUGARJOBKBCONTENTUPDATEARTICLES' => 'Publish approved articles & Expire KB Articles.',
'LBL__SUGARCRM_SUGARCRM_ELASTICSEARCH_QUEUE_SCHEDULER' => 'Elasticsearch Queue Scheduler',
'LBL_SUGARJOBREMOVEDIAGNOSTICFILES' => 'Remove diagnostic tool files',
'LBL_SUGARJOBREMOVETMPFILES' => 'Remove temporary files',

'LBL_RUNMASSEMAILCAMPAIGN' => 'Run Nightly Mass Email Campaigns',
'LBL_ASYNCMASSUPDATE' => 'Perform Asynchronous Mass Updates',
'LBL_POLLMONITOREDINBOXESFORBOUNCEDCAMPAIGNEMAILS' => 'Run Nightly Process Bounced Campaign Emails',
'LBL_PRUNEDATABASE' => 'Prune Database on 1st of Month',
'LBL_TRIMTRACKER' => 'Prune Tracker Tables',
'LBL_PROCESSWORKFLOW' => 'Process Workflow Tasks',
'LBL_PROCESSQUEUE' => 'Run Report Generation Scheduled Tasks',
'LBL_UPDATETRACKERSESSIONS' => 'Update Tracker Session Tables',
'LBL_SUGARJOBCREATENEXTTIMEPERIOD' => 'Create Future TimePeriods',
'LBL_SUGARJOBHEARTBEAT' => 'Sugar Heartbeat',
'LBL_SENDEMAILREMINDERS'=> 'Run Email Reminders Sending',
'LBL_CLEANJOBQUEUE' => 'Cleanup Job Queue',
'LBL_CLEANOLDRECORDLISTS' => 'Cleanup Old Record Lists',
//BEGIN SUGARCRM flav=ent ONLY
'LBL_PMSEENGINECRON' => 'Advanced Workflow Scheduler',
//END SUGARCRM flav=ent ONLY
);

