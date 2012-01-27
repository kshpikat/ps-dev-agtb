<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
 ********************************************************************************/
/*********************************************************************************
 * $Id: en_us.lang.php 56874 2010-06-09 18:30:46Z smalyshev $
 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = array (
  'LBL_MODULE_NAME' => 'Reports',
  'LBL_REPORT_MODULES' => 'Report Modules',
  'LBL_REPORT_ATT_MODULES' => 'Modules',
  'LBL_REPORT_EXPAND_ALL' => 'Expand All',
  'LBL_REPORT_COLLAPSE_ALL' => 'Collapse All',
  'LBL_REPORT_SHOW_CHART' => 'Show Chart',
  'LBL_REPORT_HIDE_CHART' => 'Hide Chart',
  'LBL_REPORT_SHOW_DETAILS' => 'Show Details',
  'LBL_REPORT_HIDE_DETAILS' => 'Hide Details',
  'LNK_NEW_CONTACT' => 'Create Contact',
  'LNK_NEW_ACCOUNT' => 'Create Account',
  'LNK_NEW_OPPORTUNITY' => 'Create Opportunity',
  'LNK_NEW_CASE' => 'Create Case',
  'LNK_NEW_NOTE' => 'Create Note or Attachment',
  'LNK_NEW_CALL' => 'Log Call',
  'LNK_NEW_EMAIL' => 'Archive Email',
  'LNK_NEW_MEETING' => 'Schedule Meeting',
  'LNK_NEW_TASK' => 'Create Task',
  'LBL_REPORTS' => 'Reports',
  'LBL_TITLE' => 'Title',
  'LBL_UNTITLED' => 'untitled',
  'LBL_MODULE' => 'Module',
  'LBL_ACCOUNTS' => 'Accounts',
  'LBL_OPPORTUNITIES' => 'Opportunities',
  'LBL_CONTACTS' => 'Contacts',
  'LBL_LEADS' => 'Leads',
  'LBL_ACCOUNT' => 'Account',
  'LBL_OPPORTUNITY' => 'Opportunity',
  'LBL_CONTACT' => 'Contact',
  'LBL_LEAD' => 'Lead',
  'LBL_DELETE_ERROR'=>'Only owners of reports or Administrators can delete reports.',
  'LBL_ROWS_AND_COLUMNS_REPORT' => 'Rows and Columns Report',
  'LBL_ROWS_AND_COLUMNS_REPORT_DESC' => 'Create a tabular report that contains the values of selected display fields for records matching the specified criteria.',
  'LBL_SUMMATION_REPORT' => 'Summation Report',
  'LBL_SUMMATION_REPORT_DESC' => 'Create a tabular report that provides computed data for records matching the specified criteria. The data can also be represented within a chart.',
  'LBL_MATRIX_REPORT'=>'Matrix Report',
  'LBL_MATRIX_REPORT_DESC'=>'Create a summation report that displays results in a grid format and grouped by a maximum of three fields.',
  'LBL_SUMMATION_REPORT_WITH_DETAILS_DESC'=>'Create a summation report that displays additional data related to the records in the results.',
  'LBL_SUMMATION_REPORT_WITH_DETAILS'=>'Summation Report with Details',
  'LBL_SHOW_QUERY' => 'Show Query',
  'LBL_DO_ROUND' => 'Round Numbers Over 100000',
  'LBL_SAVE_AS' => 'Save As',
  'LBL_FILTERS' => 'Filters',
  'LBL_NO_CHART_DRAWN_MESSAGE' => 'Chart not able to be drawn because of insufficient data',
  'LBL_RUNTIME_FILTERS' => 'Run-time Filters',
  'LBL_VIEWER_RUNTIME_HELP'=> 'Specify values for <b>Run-time Filters</b> and click the <b>Apply Filters</b> button to re-run the report.',
  'LBL_REPORT_RESULTS' => 'Results',
  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_REPORT_RESULTS_MESSAGE' => 'Click on Run Report to view results.',
  //END SUGARCRM flav!=sales ONLY
  //BEGIN SUGARCRM flav=sales ONLY
  'LBL_REPORT_RESULTS_MESSAGE' => 'Click on Refresh Report to view results.',
  //END SUGARCRM flav=sales ONLY
  'LBL_REPORT_FILTER_MODIFIED_MESSAGE' => 'Report filters have been modified since last run.',
  'LBL_REPORT_MODIFIED_MESSAGE' => 'Report has been modified since last run.',
  'LBL_ADD_NEW_FILTER' => 'Add New Filter',
  'LBL_DISPLAY_COLUMNS' => 'Display Columns',
  'LBL_SUMMARY_COLUMNS' => 'Summary Columns',
  'LBL_HIDE_COLUMNS' => 'Hide Columns',
  'LBL_SUBMIT_QUERY' => 'Submit Query',
  'LBL_QUERY' => 'Query',
  'LBL_CHANGE' => 'Change',
  'LBL_REMOVE' => 'Remove',
  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_CREATE_CUSTOM_REPORT' => 'Report Wizard',
  'LBL_CREATE_REPORT' => 'Create Report',
  //END SUGARCRM flav!=sales ONLY
  'LBL_MY_SAVED_REPORTS' => 'My Saved Reports',
  'LBL_MY_TEAMS_REPORTS' => 'My Team\'s Reports',
  'LBL_REPORT_NAME' => 'Report Name',
  'LBL_REPORT_ATT_NAME' => 'Name',
  'LBL_CURRENT_QUARTER_FORECAST' => 'Current Quarter Forecast',
  'LBL_ALL_PUBLISHED_REPORTS' => 'All Published Reports',
  'LBL_DETAILED_FORECAST' => 'Detailed Forecast',
  'LBL_PARTNER_ACCOUNT_LIST' => 'Partner Account List',
  'LBL_CUSTOMER_ACCOUNT_LIST' => 'Customer Account List',
  'LBL_CALL_LIST_BY_LAST_DATE_CONTACTED' => 'Call list by last date contacted',
  'LBL_OPPORTUNITIES_BY_LEAD_SOURCE' => 'Opportunities by Lead Source',
  'LBL_CURRENT_QUARTER_COMMITTED_DEALS' => 'Current Quarter Committed Deals',
  'LBL_VIEW' => 'view',
  'LBL_DELETE' => 'Delete',
  'LBL_PUBLISH' => 'publish',
  'LBL_UN_PUBLISH' => 'un-publish',
  'LBL_SCHEDULE_REPORT' => 'Schedule Report',
  'LBL_START_DATE'=>'Start Date',
  'LBL_END_DATE'=>'End Date',
  'LBL_FILTER_DATE_RANGE_START' => 'From',
  'LBL_FILTER_DATE_RANGE_FINISH' => ' To ',
  'LBL_SELECT_RECORD'=>'Select Record',
  'LBL_TIME_INTERVAL'=>'Time Interval',
  'LBL_SCHEDULE_ACTIVE'=>'Active',
  'LBL_SCHEDULE_EMAIL'=>'Schedule Report',
  'LBL_NEXT_RUN'=>'Next Email',
  'LBL_UPDATE_SCHEDULE'=>'Update Schedule',
  'LBL_YOU_HAVE_NO_SAVED_REPORTS.' => 'You have no saved reports.',
  'LBL_MY_REPORTS' => 'My Reports',
  'LBL_ACCOUNT_REPORTS' => 'Account Reports',
  'LBL_CONTACT_REPORTS' => 'Contact Reports',
  'LBL_OPPORTUNITY_REPORTS' => 'Opportunity Reports',

  'LBL_CASE_REPORTS' => 'Case Reports',

  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_BUG_REPORTS' => 'Bug Reports',
  'LBL_LEAD_REPORTS' => 'Lead Reports',
  'LBL_QUOTE_REPORTS' => 'Quote Reports',
  //END SUGARCRM flav!=sales ONLY

  'LBL_CALL_REPORTS' => 'Call Reports',
  'LBL_MEETING_REPORTS' => 'Meeting Reports',
  'LBL_TASK_REPORTS' => 'Task Reports',
  'LBL_EMAIL_REPORTS' => 'Email Reports',
  'LBL_ALL_REPORTS' => 'View Reports',
  'LBL_ACTIVITIES_REPORTS' => 'Activities Reports',
  'LBL_CHART_TYPE' => 'Chart Type',
  'LBL_NO_REPORTS' => 'No results.',

  'LBL_SAVED_SEARCH' => 'Saved Search & Layout',

  'LBL_MY_TEAM_ACCOUNT_REPORTS' => 'My Team\'s Account Reports',
  'LBL_MY_TEAM_OPPORTUNITY_REPORTS' => 'My Team\'s Opportunity Reports',
  'LBL_MY_TEAM_CONTACT_REPORTS' => 'My Team\'s Contact Reports',
  'LBL_MY_TEAM_CASE_REPORTS' => 'My Team\'s Case Reports',

  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_MY_TEAM_BUG_REPORTS' => 'My Team\'s Bug Reports',
  'LBL_MY_TEAM_LEAD_REPORTS' => 'My Team\'s Lead Reports',
  'LBL_MY_TEAM_QUOTE_REPORTS' => 'My Team\'s Quote Reports',
  //END SUGARCRM flav!=sales ONLY

  'LBL_MY_TEAM_CALL_REPORTS' => 'My Team\'s Call Reports',
  'LBL_MY_TEAM_MEETING_REPORTS' => 'My Team\'s Meeting Reports',
  'LBL_MY_TEAM_TASK_REPORTS' => 'My Team\'s Task Reports',
  'LBL_MY_TEAM_EMAIL_REPORTS' => 'My Team\'s Email Reports',

   //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_MY_TEAM_FORECAST_REPORTS' => 'My Team\'s Forecast Reports',
  'LBL_MY_TEAM_PROSPECT_REPORTS' =>'My Team\'s Target Reports',
  'LBL_MY_TEAM_CONTRACT_REPORTS' => 'My Team\'s Contract Reports',
  'LBL_MY_TEAM_PROJECT_TASK_REPORTS' => 'My Team\'s Project Task Reports',
  //END SUGARCRM flav!=sales ONLY

  'LBL_MY_ACCOUNT_REPORTS' => 'My Account Reports',
  'LBL_MY_OPPORTUNITY_REPORTS' => 'My Opportunity Reports',
  'LBL_MY_CONTACT_REPORTS' => 'My Contact Reports',

  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_MY_CASE_REPORTS' => 'My Case Reports',
  'LBL_MY_BUG_REPORTS' => 'My Bug Reports',
  'LBL_MY_LEAD_REPORTS' => 'My Lead Reports',
  'LBL_MY_QUOTE_REPORTS' => 'My Quote Reports',
  //END SUGARCRM flav!=sales ONLY

  'LBL_MY_CALL_REPORTS' => 'My Call Reports',
  'LBL_MY_MEETING_REPORTS' => 'My Meeting Reports',
  'LBL_MY_TASK_REPORTS' => 'My Task Reports',
  'LBL_MY_EMAIL_REPORTS' => 'My Email Reports',
  'LBL_MY_FORECAST_REPORTS' => 'My Forecast Reports',
  'LBL_EXPORT' => 'Export',
  'LBL_OF' => 'of',
  'LBL_SUCCESS_REPORT' => 'SUCCESS: Report',
  'LBL_MY_PROSPECT_REPORTS' =>'My Target Reports',
// report_name
  'LBL_WAS_SAVED' => 'was saved',
  'LBL_FAILURE_REPORT' => 'FAILURE: Report',
  'LBL_WAS_NOT_SAVED' => 'was not saved',
  'LBL_EQUALS' => 'Equals',
  'LBL_LESS_THAN' => 'Less Than',
  'LBL_GREATER_THAN' => 'Greater Than',
  'LBL_DOES_NOT_EQUAL' => 'Does Not Equal',
  'LBL_ON' => 'On',
  'LBL_BEFORE' => 'Before',
  'LBL_AFTER' => 'After',
  'LBL_IS_BETWEEN' => 'Is Between',
  'LBL_NOT_ON' => 'Not On',
  'LBL_CONTAINS' => 'Contains',
  'LBL_DOES_NOT_CONTAIN' => 'Does Not Contain',
  'LBL_STARTS_WITH' => 'Starts With',
  'LBL_ENDS_WITH' => 'Ends With',
  'LBL_TO_PDF' => 'Save as PDF',
  'LBL_PDF_TIMESTAMP'=> 'Y_m_d_H_i',
  'LBL_CSV_TIMESTAMP'=> 'Y_m_d_H_i_s',
  'LBL_IS' => 'Is',
  'LBL_IS_NOT' => 'Is Not',
  'LBL_ONE_OF' => 'Is One Of',
  'LBL_IS_NOT_ONE_OF' => 'Is Not One Of',
  'LBL_ANY' => 'Any',
  'LBL_ALL' => 'At Least',
  'LBL_EXACT' => 'Exact',
  'MSG_UNABLE_PUBLISH_ANOTHER' => 'Unable to publish. There is another published Report by the same name.',
  'MSG_UNABLE_PUBLISH_YOU_OWN' => 'Unable to un-publish a Report owned by another user. You own an Report by the same name.',
  'LBL_PUBLISHED_ACCOUNT_REPORTS' => 'Published Account Reports',
  'LBL_PUBLISHED_CONTACT_REPORTS' => 'Published Contact Reports',
  'LBL_PUBLISHED_OPPORTUNITY_REPORTS' => 'Published Opportunity Reports',
  'LBL_PUBLISHED_CASE_REPORTS' => 'Published Case Reports',

  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_PUBLISHED_BUG_REPORTS' => 'Published Bug Reports',
  'LBL_PUBLISHED_LEAD_REPORTS' => 'Published Lead Reports',
  'LBL_PUBLISHED_QUOTE_REPORTS' => 'Published Quote Reports',
  //END SUGARCRM flav!=sales ONLY

  'LBL_PUBLISHED_CALL_REPORTS' => 'Published Call Reports',
  'LBL_PUBLISHED_MEETING_REPORTS' => 'Published Meeting Reports',
  'LBL_PUBLISHED_TASK_REPORTS' => 'Published Task Reports',
  'LBL_PUBLISHED_EMAIL_REPORTS' => 'Published Email Reports',
  'LBL_PUBLISHED_FORECAST_REPORTS' => 'Published Forecast Reports',
  'LBL_PUBLISHED_PROSPECT_REPORTS' =>'Published Target Reports',
  'LBL_THERE_ARE_NO_REPORTS_OF_THIS_TYPE' => 'There are no reports of this type.',
  'LBL_AND' => 'and',
  'LBL_MISSING_FIELDS' => 'Missing fields',
  'LBL_AT_LEAST_ONE_DISPLAY_COLUMN' => 'Select at least one display column.',
  'LBL_MISSING_INPUT_VALUE' => 'missing input value.',
  'LBL_MISSING_SECOND_INPUT_VALUE' => 'missing second input value.',
  'LBL_NOTHING_WAS_SELECTED' => 'nothing was selected.',
  'LBL_TOTAL' => 'Total',
  'LBL_MODULE_NAME_SAVED' => 'Module Name',
  'LBL_REPORT_TYPE' => 'Report Type',
  'LBL_REPORT_LAST_RUN_DATE' => 'Accessed On',
  'LBL_REPORT__ATT_TYPE' => 'Type',
  'LBL_REPORT_RUN_WITH_FILTER' => 'Apply',
  'LBL_REPORT_RESET_FILTER' => 'Reset',
  'LBL_DISPLAY_SUMMARIES'=>'Choose Display Summaries',
  'LBL_HIDE_SUMMARIES'=>'Hide Summaries',
  //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_RUN_BUTTON_TITLE'=>'Run Report',
  'LBL_RUN_REPORT_BUTTON_LABEL' => 'Run Report',
  //END SUGARCRM flav!=sales ONLY
  //BEGIN SUGARCRM flav=sales ONLY
  'LBL_RUN_BUTTON_TITLE'=>'Refresh Report',
  'LBL_RUN_REPORT_BUTTON_LABEL' => 'Refresh Report',
  //END SUGARCRM flav=sales ONLY
  'LBL_RUN_REPORT_BUTTON_KEY' => 'R',
  'LBL_DUPLICATE_AS_ORIGINAL' => 'As Original Type',
  'LBL_DUPLICATE_AS_ROWS_AND_COLS' => 'As Rows and Columns',
  'LBL_DUPLICATE_AS_SUMMATON' => 'As Summation',
  'LBL_DUPLICATE_AS_SUMMATION_DETAILS' => 'As Summation with Details',
  'LBL_SUMMATION_WITH_DETAILS' => 'Summation with Details',
  'LBL_DUPLICATE_AS_MATRIX' => 'As Matrix',
  'LBL_SAVE_RUN'=> 'Save and Run',
  'LBL_WITH_DETAILS' => 'With details',
  'LBL_CHOOSE_COLUMNS' => 'Choose columns to display' ,
  'LBL_CHOOSE_SUMMARIES' => 'Choose summaries to display' ,
  'LBL_GROUP_BY' => 'Group By',
  'LBL_ADD_COLUMN' => 'Add Column',
  'LBL_GRAND_TOTAL' => 'Grand Total',
   'LBL_SEARCH_FORM_TITLE' => 'Reports Search',
 //BEGIN SUGARCRM flav!=sales ONLY
  'LBL_FORECAST_REPORTS' => 'Forecast Reports',
  'LBL_MY_PROJECT_TASK_REPORTS'=>'My Project Tasks Reports',
  'LBL_PUBLISHED_PROJECT_TASK_REPORTS'=>'Published Project Tasks Reports',
  'LBL_PROJECT_TASK_REPORTS'=>'Project Task Reports',
 //END SUGARCRM flav!=sales ONLY

  'DROPDOWN_SCHEDULE_INTERVALS'=>array(
  							'3600'=>'Hourly',
  							'21600'=>'Every 6 Hours',
  							'43200'=>'Every 12 Hours',
  							'86400'=>'Daily',
  							'604800'=>'Weekly',
  							'1209600'=>'Every 2 Weeks',
  							'2419200'=>'Every 4 Weeks',

),
  	'LBL_WEIGHTED_AVG_AMOUNT' => "Weighted Avg Amount",
  	'LBL_WEIGHTED_SUM_AMOUNT' => "Weighted Sum Amount",
    'ERR_SELECT_COLUMN' => 'Please select a display column first.',
    'LBL_BY_MONTH' => 'By Month',
    'LBL_BY_YEAR' => 'By Year',
    'LBL_BY_QUARTER' => 'By Quarter',
    'LBL_COUNT' => 'Count',
    'LBL_SUM' => 'SUM',
    'LBL_AVG' => 'AVG',
    'LBL_MAX' => 'MAX',
    'LBL_MIN' => 'MIN',
    'LBL_QUARTER_ABBREVIATION' => 'Q',
    'LBL_MONTH' => 'Month',
    'LBL_YEAR' => 'Year',
    'LBL_QUARTER' => 'Quarter',
	'LBL_YESTERDAY'=>'Yesterday',
	'LBL_TODAY'=>'Today',
	'LBL_TOMORROW'=>'Tomorrow',
	'LBL_LAST_WEEK'=>'Last Week',
	'LBL_NEXT_WEEK'=>'Next Week',
	'LBL_LAST_7_DAYS'=>'Last 7 Days',
	'LBL_NEXT_7_DAYS'=>'Next 7 Days',
	'LBL_LAST_MONTH'=>'Last Month',
	'LBL_NEXT_MONTH'=>'Next Month',
	'LBL_LAST_QUARTER'=>'Last Quarter',
	'LBL_THIS_QUARTER'=>'This Quarter',
	'LBL_LAST_YEAR'=>'Last Year',
	'LBL_NEXT_YEAR'=>'Next Year',
  'LBL_SELECT' => 'Select',
  'LBL_AT_LEAST_ONE_SUMMARY_COLUMN' => 'At least one summary column.',
  'LBL_SHOW_DETAILS' => 'Show Details',
  'LBL_1_REPORT_ON'=>'1. Report On  ',
  'LBL_2_FILTER'=>'2. Filter  ',
  'LBL_3_GROUP'=>'3. Group  ',
  'LBL_3_CHOOSE'=>'3. Choose Display Columns  ',
  'LBL_4_CHOOSE'=>'4. Choose Display Columns  ',
  'LBL_5_CHART_OPTIONS'=>'5. Chart Options  ',
  'LBL_LABEL'=>'Label',
  'LBL_THIS_MONTH'=>'This Month',
  'LBL_LAST_30_DAYS'=>'Last 30 Days',
  'LBL_NEXT_30_DAYS'=>'Next 30 Days',
  'LBL_THIS_YEAR'=>'This Year',
  'LBL_LIST_FORM_TITLE' =>'Reports',
  'LBL_PROSPECT_REPORTS'=>'Target Reports',
  'LBL_CHART_TYPE'=>'Chart Type',
  'LBL_IS_EMPTY'=>'Is Empty',
  'LBL_IS_NOT_EMPTY'=>'Is Not Empty',
	'LBL_CHART_DESCRIPTION'=>'Description',
	'LBL_USE_COLUMN_FOR'=>'Data Series',
	'LBL_RELATED' => 'Related: ',
	'LBL_OWNER' => 'Assigned to',
	'LBL_TEAM' => 'Teams',
  'LBL_TOTAL_IS'=>'Total is',
  'CHART_COUNT_PATTERN'=>"{count} {module} where {group_label} is {group_text}",
  'LBL_WITH_A_TOTAL' =>'with a total',
  'LBL_WITH_AN_AVERAGE'=>'with an average',
  'CHART_SUMAVG_PATTERN'=>"{count} {module} {numerical_function} {numerical_label} of {currency_symbol}{numerical_value}{thousands} where {group_label} is {group_text}",
  'LBL_WHOSE_MAXIMUM' =>'whose maximum',
  'LBL_WHOSE_MINIMUM'=>'whose minimum',
  'CHART_MINMAX_PATTERN' => "{count} {module} {numerical_function} {numerical_label} is {numerical_value}{thousands} where {group_label} is {group_text}",
  'LBL_ROLLOVER'=>"Rollover a bar for details.",
  'LBL_ROLLOVER_WEDGE'=>"Rollover a wedge for details.",
  'LBL_ROLLOVER_SQUARE'=>"Rollover a square for details.",
  'LBL_NO_CHART'=>'No Chart',
  'LBL_HORIZ_BAR'=>'Horizontal Bar',
  'LBL_VERT_BAR'=>'Vertical Bar',
  'LBL_PIE'=>'Pie',
  'LBL_LINE'=>'Line',
  'LBL_FUNNEL'=>'Funnel',
  'LBL_GROUP_BY_REQUIRED'=>'At least one Group By and one Summary column are required to render a chart.<br>',
	'MSG_NO_PERMISSIONS' => 'You do not have permission to edit this report',
	'LBL_NONE' => '-- none --',
	'LBL_NONE_STRING' => 'None',
  	'LBL_DATE_BASED_FILTERS' => '<i>Date filters are relative to the time zone of the report\'s <b>Assigned To</b> user</i>',
//BEGIN SUGARCRM flav=pro ONLY
	'LBL_CONTRACT_REPORTS'=>'Contract Reports',
  	'LBL_MY_CONTRACT_REPORTS' => 'My Contract Reports',
  	'LBL_PUBLISHED_CONTRACT_REPORTS'=>'Published Contract Reports',
//END SUGARCRM flav=pro ONLY
	'LBL_HELLO' => 'Hello',
	'LBL_SCHEDULED_REPORT_MSG_INTRO' => 'Attached is an auto-generated report sent to you from the Sugar application.  This report was created on ',
	'LBL_SCHEDULED_REPORT_MSG_BODY1' => ' and saved with the name "',
	'LBL_SCHEDULED_REPORT_MSG_BODY2' => "\". If you wish to change your report settings, login to the Sugar application and click on the \"Reports\" tab.\n\n",
    'LBL_LIST_PUBLISHED' => 'Published',
    'LBL_THIS_WEEK'=>'This Week',
    'LBL_NEXT_QUARTER'=>'Next Quarter',
    'LBL_ADD_RELATE' => "Add Related",
    'LBL_DEL_THIS' => "Remove",
    'LBL_ALERT_CANT_ADD' => 'You cannot add a related module until you select a table to relate from.\nSelect a module in the dropdown left of the \'Add Related\' button you clicked.',
    'LBL_BY_DAY' => 'By Day',
    'LBL_DAY' => 'Day',
    'LBL_OUTER_JOIN_CHECKBOX' => 'Optional Related Modules',
    'LBL_ANY_ONE_OF' => 'Any One Of',
    'LBL_RELATED_TABLE_BLANK' => 'Please select a module to relate to.',
    'LBL_FILTER_CONDITIONS' => 'Select Operator:',
    'LBL_FILTER_OR' => 'OR',
    'LBL_FILTER_AND' => 'AND',
    'LBL_FILTERS_END' => 'of the following filters.',
    'LBL_SEARCH_FORM_TITLE' => 'Report List',
    'LBL_FAVORITE_REPORTS' => 'My Favorite Reports',
    'LBL_FAVORITE_REPORTS_TITLE' => 'My Favorite Reports',
    'LBL_ADDED_FAVORITES' => ' report(s) added to My Favorite Reports.',
    'LBL_REMOVED_FAVORITES' => ' report(s) removed from My Favorite Reports.',
    'LBL_MODULE_TITLE' => 'Reports: Home',
    'LBL_MODULE_VIEWER_TITLE' => 'Report Viewer: Home',
    'LBL_REPORT_MODULE_VIEWER_TITLE' => 'Report Viewer',
    'LBL_REPORT_SCHEDULE_TITLE' => 'Schedule',
    'LBL_FAVORITES_TITLE' => 'My Favorite Reports',
    'LBL_TABLE_CHANGED' => 'Module list has been modified, please double check the criteria entered in the Group tab.',
    'LBL_OPTIONAL_HELP' => 'Select the boxes to display the primary module records even if the related module records do not exist. When the box is not selected, primary module records will display only if they have one or more related module records.',
    'LBL_RUNTIME_HELP' => 'Select this box to allow users to change the filter value before running the report.',
  	'LBL_USER_EMPTY_HELP'=>'In order to view records that are not assigned to any Users, check the \'Optional Related Modules\' checkbox in the \'Reports Details\' step in addition to using the \'Is Empty\' option for the Assigned User filter. This will display all records that are not related to any Users.',

    // Default Report Titles
    'DEFAULT_REPORT_TITLE_1' => 'Current Quarter Forecast',
    'DEFAULT_REPORT_TITLE_2' => 'Detailed Forecast',
    'DEFAULT_REPORT_TITLE_3' => 'Partner Account List',
    'DEFAULT_REPORT_TITLE_4' => 'Customer Account List',
    'DEFAULT_REPORT_TITLE_5' => 'Call List By Last Date Contacted',
    'DEFAULT_REPORT_TITLE_6' => 'Opportunities By Lead Source',

    // Cases
    'DEFAULT_REPORT_TITLE_7' => 'Open Cases By User By Status',
    'DEFAULT_REPORT_TITLE_8' => 'Open Cases By Month By User',
    'DEFAULT_REPORT_TITLE_9' => 'Open Cases By Priority By User',
    'DEFAULT_REPORT_TITLE_10' => 'New Cases By Month',

    // Pipeline
    'DEFAULT_REPORT_TITLE_11' => 'Pipeline By Type By Team',
    'DEFAULT_REPORT_TITLE_12' => 'Pipeline By Team By User',
    'DEFAULT_REPORT_TITLE_17' => 'Opportunities Won By Lead Source',

    // Activity
    'DEFAULT_REPORT_TITLE_13' => 'Tasks By Team By User',
    'DEFAULT_REPORT_TITLE_14' => 'Calls By Team By User',
    'DEFAULT_REPORT_TITLE_15' => 'Meetings By Team By User',

    // Accounts
    'DEFAULT_REPORT_TITLE_16' => 'Accounts By Type By Industry',

    // Leads
    'DEFAULT_REPORT_TITLE_18' => 'Leads By Lead Source',

    // Tracker
    'DEFAULT_REPORT_TITLE_19' => 'My Usage Metrics (Today)',
    'DEFAULT_REPORT_TITLE_20' => 'My Usage Metrics (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_21' => 'My Usage Metrics (Last 30 Days)',
    'DEFAULT_REPORT_TITLE_22' => 'My Module Usage (Today)',
    'DEFAULT_REPORT_TITLE_23' => 'My Module Usage (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_24' => 'My Module Usage (Last 30 Days)',
    'DEFAULT_REPORT_TITLE_25' => 'Users Usage Metrics (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_26' => 'Users Usage Metrics (Last 30 Days)',
    'DEFAULT_REPORT_TITLE_27' => 'Modules Used By My Direct Reports (Last 30 Days)',
    'DEFAULT_REPORT_TITLE_28' => 'Slow Queries',
    'DEFAULT_REPORT_TITLE_29' => 'My Records Modified (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_31' => 'My Recently Modified Records (Last 30 Days)',
    'DEFAULT_REPORT_TITLE_32' => 'Records Modified By My Direct Reports (Last 30 Days)',
    'DEFAULT_REPORT_TITLE_41' => 'Active User Sessions (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_42' => 'User Sessions Summary (Last 7 Days)',

    //BEGIN SUGARCRM flav=sales ONLY
    'DEFAULT_REPORT_TITLE_54' => 'Pipeline By User',
  	'DEFAULT_REPORT_TITLE_55' => 'Pipeline By Type',
    //END SUGARCRM flav=sales ONLY

    //BEGIN SUGARCRM flav=sales || flav=pro || flav=ent ONLY
    'DEFAULT_REPORT_TITLE_43' => 'Customer Account Owners',
  	'DEFAULT_REPORT_TITLE_44' => 'My New Customer Accounts',
  	'DEFAULT_REPORT_TITLE_45' => 'Opportunities By Sales Stage',
  	'DEFAULT_REPORT_TITLE_46' => 'Opportunities By Type',
  	'DEFAULT_REPORT_TITLE_47' => 'Open Calls',
  	'DEFAULT_REPORT_TITLE_48' => 'Open Meetings',
  	'DEFAULT_REPORT_TITLE_49' => 'Open Tasks',
  	'DEFAULT_REPORT_TITLE_50' => 'Opportunities Won By Account',
  	'DEFAULT_REPORT_TITLE_51' => 'Opportunities Won By User',
  	'DEFAULT_REPORT_TITLE_52' => 'All Open Opportunities',
  	'DEFAULT_REPORT_TITLE_53' => 'All Closed Opportunities',
    //END SUGARCRM flav=sales || flav=pro || flav=ent ONLY

    //'LBL_CHART_ON_DASHLET' => 'Do not Display Chart on Dashlet',
    'LBL_ASSIGNED_TO_NAME'=>'Assigned To:',
    'LBL_CONTENT'=>'Content',
    'LBL_IS_PUBLISHED'=>'Is Published',
    'LBL_FAVORITE'=>'Favorite',
    'LBL_SCHEDULE_TYPE'=>'Schedule Type',
  	'LBL_NO_ACCESS' => 'You are not able to access this report due to permissions restrictions.',
  	'LBL_SELECT_REPORT_TYPE' => 'Select the type of report you would like to create:',
  	'LBL_SELECT_MODULE'=> 'Select the module that you want to report on:',
  	'LBL_NEXT' =>'Next >',
  	'LBL_PREVIOUS' =>'< Back',
  	'LBL_CANCEL'=>'Cancel',
  	'LBL_AVAILABLE_FIELDS'=>'Available Fields',
  	'LBL_RELATED_MODULES'=>'Related Modules',
  	'LBL_FIELD_NAME'=>'Field Name',
  	'LBL_RUN_TIME_LABEL'=>'Run-time',
  	'LBL_NO_IMAGE'=>'No Image',
  	'LBL_BASIC_FILTERS'=>'Basic Filters',
  	'LBL_ADVANCED_FILTERS'=>'Advanced Filters',
  	'LBL_ADD_GROUP'=>'Add Filter Group',
  	'LBL_REMOVE_GROUP'=>'Remove Filter Group',
  	'LBL_FILTER'=> 'Filter',
  	'LBL_ADD_FILTER_TO'=> 'Add Filter To',
  	'LBL_COLUMN_NAME'=>'Column Name',
  	'LBL_OPTIONAL_MODULES'=>'Optional Modules',
  	'LBL_SELECT_REPORT_TYPE'=>'Click an icon to select a Report Type.',
  	'LBL_SELECT_REPORT_TYPE_ICON'=>'Select Report Type',
  	'LBL_SELECT_MODULE'=>'Select Module',
  	'LBL_SELECT_MODULE_BUTTON'=>'Click an icon to select a Module.',
  	'LBL_DEFINE_FILTERS'=>'Define Filters',
  	'LBL_SELECT_GROUP_BY'=>'Define Group By',
  	'LBL_CHOOSE_DISPLAY_COLS'=>'Choose Display Columns',
  	'LBL_REPORT_DETAILS'=>'Report Details',
  	'LBL_REPORT_GROUP_BY'=>'Group By',
  	'LBL_CLEAR'=>'Clear',
  	'LBL_CHART_OPTIONS'=>'Chart Options',
  	'LBL_CHART_DATA_HELP'=>'Select the Summary that will be displayed in the chart.',
  	'LBL_DO_ROUND_HELP'=>'Numbers over 100000 will be rounded in charts.<br>Example: 350000 will be expressed as 350K.',
  	'LBL_COMBO_TYPE_AHEAD'=>'Search for Field',
  	'LBL_MAXIMUM_3_GROUP_BY'=>'A Matrix Report cannot have more than 3 group-by clauses.',
  	'LBL_MINIMUM_2_GROUP_BY'=>'A Matrix Report must have at least 2 group-by clauses.',
  	'LBL_MATRIX_LAYOUT'=>'Layout Options:',
  	'LBL_REMOVE_BTN_HELP'=>'Click to remove this Filter Group.',
  	'LBL_ADD_BTN_HELP'=>'Click to add a new Filter Group.  Use groups to apply AND/OR logic to sets of filters.',
  	'LBL_ORDER_BY'=>'Sort By',
  	'LBL_ASCENDING'=>'Ascending',
  	'LBL_DESCENDING'=>'Descending',
  	'LBL_TYPE'=>'Type',
  	'LBL_SUBJECT'=>'Subject',
  	'LBL_STATUS'=>'Status',
  	'LBL_DATE'=>'Date Start',
  	'LBL_1X2'=>'1 X 2',
  	'LBL_2X1'=>'2 X 1',
  	'LBL_NO_FILTERS'=>' has no filters.',
    'LBL_DELETED_FIELD_IN_REPORT1' => 'The following field in this report is no longer valid: ',
    'LBL_DELETED_FIELD_IN_REPORT2'=>'Please Edit the report and check to make sure that the other parameters are still relevant.',
  	'LBL_CURRENT_USER'=>'Current User',
  	'LBL_MODULE_CHANGE_PROMPT'=> 'Changing the selected module will result in a loss of filters, display columns, etc. Do you still wish to continue?',
  	'LBL_CANNOT_BE_EMPTY'=>' cannot be empty.',
  	'LBL_FIELDS_PANEL_HELP_DESC'=> 'All reportable fields from the selected module in <B>Related Modules</B> appear here. Select a field.',
  	'LBL_RELATED_MODULES_PANEL_HELP_DESC'=> 'The primary module and all modules related to the primary module appear here. Select a module.',
  	'LBL_PREVIEW_REPORT'=>'Preview',
	'LBL_FILTERS_HELP_DESC'=>"<b>Steps to Define Filters:</b><br/><br/>1) Click on the Module in the <b>Related Modules</b> pane that you would like to use to define filters. By default, the primary module (top node in the tree view) is selected. <br/><br/>
	You can select a related module (child node in the tree view) by clicking on the module. Expand the node to view additional modules related to the related module. The module that you select determines which reportable fields appear in the <b>Available Fields</b> pane.<br/><br/>
	2) Click on a Field in the <b>Available Fields</b> pane to add it to the filters. You can also search for the field by typing in the text box in the pane.<br/><br/>
	After selecting any number of fields from the module selected in the <b>Related Modules</b> pane, you can choose a different module from which you can select any number of fields to use as filters.<br/><br/>
	3) Choose <b>AND</b> or <b>OR</b> to designate whether all filters or any filters, respectively, are used to find results for the report.<br/><br/>
	4) [Optional] Click on <b>Add Filter Group</b> to create groups of filters. You can have any number of filter groups and any number of filters in a group to create nested filters.<br/><br/>
	5) [Optional] Select the Run-time option for a Filter to allow users to use the filter to further customize the results of the reports while viewing the report.",
	'LBL_GROUP_BY_HELP_DESC'=>"<b>Steps to Define Group By:</b><br></br>1) Click on a Module in the <b>Related Modules</b> pane that you would like to use to group records in your report. By default, the primary module (top node in the tree view) is selected. <br/><br/>
	You can select a related module (child node in the tree view) by clicking on the module. Expand the node to view additional modules related to the related module. The module that you select determines which reportable fields appear in the <b>Available Fields</b> pane.<br/><br/>
	2) Click on the Field in the <b>Available Fields</b> pane to group records by the field in your report. You can also search for the field by typing in the text box in the pane.<br/><br/>
	After selecting any number of fields from the module selected in the <b>Related Modules</b> pane, you can choose a different module from which you can select any number of fields to group records. However, the report becomes less readable when you group by more than several fields.<br/><br/>
	You can change the order of the fields by dragging and dropping them to the desired position.  Changing the order affects the way the results are displayed.<br/><br/>  For Matrix Reports, you can use a maximum of three fields to group records.",
	'LBL_DISPLAY_COLS_HELP_DESC'=>"<b>Steps to Choose Display Columns:</b><br/><br/>1) Click on a Module in the <b>Related Modules</b> pane that you would like to use to display data in your report. By default, the primary module (top node in the tree view) that you chose during the 'Select Module' step is selected.<br/><br/>
	You can select fields from a related module (child node in the tree view) by clicking on the module. Modules related to the modules related to the primary module can also be selected. The module that you select determines which reportable fields appear in the <b>Available Fields</b> pane.<br/><br/>
	2) Click on the Field in the <b>Available Fields</b> pane to display the field data in the records in your report. You can also search for the field by typing in the text box in the pane.<br/><br/>
	After selecting any number of fields from the module selected in the <b>Related Modules</b> pane, you can choose a different module from which you can select additional fields. You can select any number of fields, but the report is generated more slowly and becomes less readable when you add more than necessary fields in the report.<br/><br/>
	You can change the order fields by dragging and dropping them to the desired position. Changing the field order changes the order in which the columns are displayed in the results." ,
	'LBL_DISPLAY_SUMMARY_HELP_DESC'=>"<b>Steps to Choose Display Summaries:</b><br/><br/>1) Click on the Module in the <b>Related Modules</b> pane that you would like to use for the summaries in your report. By default, the primary module (top node in the tree view) is selected.<br/><br/>
	You can select a related module (child node in the tree view) by clicking on the module. Expand the node to view additional modules related to the related module. The module that you select determines which reportable fields appear in the <b>Available Fields</b> pane.<br/><br/>
	2) Click on a Field in the <b>Available Fields</b> pane to select summaries for your report. You can also search for the field by typing in the text box in the pane.<br/><br/>
	After selecting any number of fields from the module selected in the <b>Related Modules</b> pane, you can choose a different module from which you can select additional fields for the summaries in your report.<br/><br/>" .
	"For Matrix Reports, you can select more than one field to display multiple values within a single cell in your report." ,

// BEGIN DCE SUGARCRM ONLY
    'LBL_DCE_LICENSING_REPORT'=>'Licensing Report',
    'DEFAULT_REPORT_TITLE_33' => 'Template Usage',
    'DEFAULT_REPORT_TITLE_34' => 'Max Sessions Per Day (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_35' => 'Request per Day (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_36' => 'Logins Per Day (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_37' => 'Queries Per Day (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_38' => 'Files Per Day (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_39' => 'Users Per Day (Last 7 Days)',
    'DEFAULT_REPORT_TITLE_40' => 'Memory Usage Per Day (Last 7 Days)',
// END DCE SUGARCRM ONLY
	'LBL_ALT_SHOW' => 'Show',
  	'LBL_REPORT_DATA_COLUMN_ORDERS' => 'This report contains data in following column orders:',
	'LBL_HELP' => 'Help' /*for 508 compliance fix*/,
	'LBL_EDITLAYOUT' => 'Edit Layout' /*for 508 compliance fix*/,
	'LBL_SORT' => 'Sort' /*for 508 compliance fix*/,
	'LBL_EDIT' => 'Edit' /*for 508 compliance fix*/,
	'LBL_SHOW' => 'Show' /*for 508 compliance fix*/,
	'LBL_MORE' => 'More' /*for 508 compliance fix*/,
	'LBL_LEFT' => 'Left' /*for 508 compliance fix*/,
	'LBL_RIGHT' => 'Right' /*for 508 compliance fix*/,
	'LBL_DOWN' => 'Down' /*for 508 compliance fix*/,
	'LBL_UP' => 'Up' /*for 508 compliance fix*/,
    'LBL_ALT_INFORMATION' => 'Information',
);
?>
