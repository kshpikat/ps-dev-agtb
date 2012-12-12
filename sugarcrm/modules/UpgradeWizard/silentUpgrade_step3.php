<?php

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-professional-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2004-2010 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

//////////////////////////////////////////////////////////////////////////////////////////
//// This is a stand alone file that can be run from the command prompt for upgrading a
//// Sugar Instance. Three parameters are required to be defined in order to execute this file.
//// php.exe -f silentUpgrade.php [Path to Upgrade Package zip] [Path to Log file] [Path to Instance]
//// See below the Usage for more details.
/////////////////////////////////////////////////////////////////////////////////////////
ini_set('memory_limit',-1);

function rebuildRelations($pre_path = '')
{
	$_REQUEST['silent'] = true;
	include($pre_path.'modules/Administration/RebuildRelationship.php');
	$_REQUEST['upgradeWizard'] = true;
	include($pre_path.'modules/ACL/install_actions.php');
}

//Clean_string cleans out any file  passed in as a parameter
$_SERVER['PHP_SELF'] = 'silentUpgrade.php';

///////////////////////////////////////////////////////////////////////////////
////	STANDARD REQUIRED SUGAR INCLUDES AND PRESETS
if(!defined('sugarEntry')) define('sugarEntry', true);

$_SESSION = array();
$_SESSION['schema_change'] = 'sugar'; // we force-run all SQL
$_SESSION['silent_upgrade'] = true;
$_SESSION['step'] = 'silent'; // flag to NOT try redirect to 4.5.x upgrade wizard

$_REQUEST = array();
$_REQUEST['addTaskReminder'] = 'remind';
$_SERVER['SERVER_SOFTWARE'] = '';

define('SUGARCRM_INSTALL', 'SugarCRM_Install');
$upgradeType = 'SugarCRM_Install';
touch($argv[2]);
$path = realpath($argv[2]); // custom log file, if blank will use ./upgradeWizard.log

$errors = array();
$cwd = $argv[3];
chdir($cwd);

require_once('include/entryPoint.php');
require_once('modules/UpgradeWizard/uw_utils.php');
require_once('include/utils/zip_utils.php');
require_once('include/utils/sugar_file_utils.php');
require_once('include/SugarObjects/SugarConfig.php');
global $sugar_config;


require_once("sugar_version.php"); // provides $sugar_version & $sugar_flavor

$configOptions = $sugar_config['dbconfig'];

    $GLOBALS['log']	= LoggerManager::getLogger('SugarCRM');
	$patchName		= basename($argv[1]);
	$zip_from_dir	= substr($patchName, 0, strlen($patchName) - 4); // patch folder name (minus ".zip")
    $db				= DBManagerFactory::getInstance();
	$UWstrings		= return_module_language('en_us', 'UpgradeWizard');
	$adminStrings	= return_module_language('en_us', 'Administration');
    $app_list_strings = return_app_list_strings_language('en_us');
	$mod_strings	= array_merge($adminStrings, $UWstrings);
	$subdirs		= array('full', 'langpack', 'module', 'patch', 'theme', 'temp');
	global $unzip_dir;
	//////////////////////////////////////////////////////////////////////////////
	//Adding admin user to the silent upgrade
	 $current_user = new User();
	 $user_name = $argv[4];
	 $q = "select id from users where user_name = '" . $user_name . "' and is_admin=1";
	 $result = $db->query($q, false);
	 $logged_user = $db->fetchByAssoc($result);
	 $current_user->retrieve($logged_user['id']);
/////retrieve admin user

$unzip_dir = sugar_cached("upgrades/temp");
$install_file = $sugar_config['upload_dir']."/upgrades/patch/".basename($argv[1]);

$_SESSION['unzip_dir'] = $unzip_dir;
$_SESSION['install_file'] = $install_file;
$_SESSION['zip_from_dir'] = $zip_from_dir;

////	END UPGRADE PREP
//////////////////////////////////////////////////////////////////////////////
set_upgrade_vars();
///////////////////////////////////////////////////////////////////////////////
////	RUN SILENT UPGRADE
ob_start();
set_time_limit(0);

///    RELOAD NEW DEFINITIONS
global $ACLActions, $beanList, $beanFiles;

require_once('modules/Trackers/TrackerManager.php');
$trackerManager = TrackerManager::getInstance();
$trackerManager->pause();
$trackerManager->unsetMonitors();

include('modules/ACLActions/actiondefs.php');
include('include/modules.php');

require_once('modules/Administration/upgrade_custom_relationships.php');
upgrade_custom_relationships();

logThis('Upgrading user preferences start .', $path);
if(function_exists('upgradeUserPreferences')){
   upgradeUserPreferences();
}
logThis('Upgrading user preferences finish .', $path);

// clear out the theme cache
if(is_dir($GLOBALS['sugar_config']['cache_dir'].'themes')){
    $allModFiles = array();
    $allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'themes',$allModFiles);
    foreach($allModFiles as $file){
        //$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
        if(file_exists($file)){
            unlink($file);
        }
    }
}

// re-minify the JS source files
$_REQUEST['root_directory'] = getcwd();
$_REQUEST['js_rebuild_concat'] = 'rebuild';
require_once('jssource/minify.php');

//Add the cache cleaning here.
if(function_exists('deleteCache'))
{
	logThis('Call deleteCache', $path);
	@deleteCache();
}

// creating full text search logic hooks
// this will be merged into application/Ext/LogicHooks/logichooks.ext.php
// when rebuild_extensions is called
logThis(' Writing FTS hooks');
if (!function_exists('createFTSLogicHook')) {
    $customFileLoc = create_custom_directory('Extension/application/Ext/LogicHooks/SugarFTSHooks.php');
    $fp = sugar_fopen($customFileLoc, 'wb');
    $contents = <<<CIA
<?php
if (!isset(\$hook_array) || !is_array(\$hook_array)) {
    \$hook_array = array();
}
if (!isset(\$hook_array['after_save']) || !is_array(\$hook_array['after_save'])) {
    \$hook_array['after_save'] = array();
}
\$hook_array['after_save'][] = array(1, 'fts', 'include/SugarSearchEngine/SugarSearchEngineQueueManager.php', 'SugarSearchEngineQueueManager', 'populateIndexQueue');
CIA;

    fwrite($fp,$contents);
    fclose($fp);
} else {
    createFTSLogicHook('Extension/application/Ext/LogicHooks/SugarFTSHooks.php');
}

//First repair the databse to ensure it is up to date with the new vardefs/tabledefs
logThis('About to repair the database.', $path);
//Use Repair and rebuild to update the database.
global $dictionary;
require_once("modules/Administration/QuickRepairAndRebuild.php");
$rac = new RepairAndClear();
$rac->clearVardefs();
$rac->rebuildExtensions();
//bug: 44431 - defensive check to ensure the method exists since upgrades to 6.2.0 may not have this method define yet.
if(method_exists($rac, 'clearExternalAPICache'))
{
    $rac->clearExternalAPICache();
}

$repairedTables = array();
foreach ($beanFiles as $bean => $file) {
	if(file_exists($file)){
		unset($GLOBALS['dictionary'][$bean]);
		require_once($file);
		$focus = new $bean ();
		if(empty($focus->table_name) || isset($repairedTables[$focus->table_name])) {
		   continue;
		}

		if (($focus instanceOf SugarBean)) {
			if(!isset($repairedTables[$focus->table_name]))
			{
				$sql = $GLOBALS['db']->repairTable($focus, true);
                if(trim($sql) != '')
                {
				    logThis('Running sql:' . $sql, $path);
                }
				$repairedTables[$focus->table_name] = true;
			}

			//Check to see if we need to create the audit table
		    if($focus->is_AuditEnabled() && !$focus->db->tableExists($focus->get_audit_table_name())){
               logThis('Creating audit table:' . $focus->get_audit_table_name(), $path);
		       $focus->create_audit_table();
            }
		}
	}
}

unset ($dictionary);
include ("{$argv[3]}/modules/TableDictionary.php");
foreach ($dictionary as $meta) {
	$tablename = $meta['table'];

	if(isset($repairedTables[$tablename])) {
	   continue;
	}

	$fielddefs = $meta['fields'];
	$indices = $meta['indices'];
	$sql = $GLOBALS['db']->repairTableParams($tablename, $fielddefs, $indices, true);
	if(!empty($sql)) {
	    logThis($sql, $path);
	    $repairedTables[$tablename] = true;
	}

}

logThis('database repaired', $path);

logThis('Start rebuild relationships.', $path);
@rebuildRelations();
logThis('End rebuild relationships.', $path);

require_once('modules/Administration/Administration.php');
$admin = new Administration();
$admin->saveSetting('system','adminwizard',1);

include("$unzip_dir/manifest.php");
$ce_to_pro_ent = isset($manifest['name']) && preg_match('/^SugarCE.*?(Pro|Ent|Corp|Ult)$/', $manifest['name']);
$sugar_version = getSilentUpgradeVar('origVersion');
if (!$sugar_version)
{
    global $silent_upgrade_vars_loaded;
    logThis("Error retrieving silent upgrade var for origVersion: cache dir is {$GLOBALS['sugar_config']['cache_dir']} -- full cache for \$silent_upgrade_vars_loaded is ".var_export($silent_upgrade_vars_loaded, true), $path);
}

//BEGIN SUGARCRM flav=pro ONLY
logThis("Begin: Update custom module built using module builder to add favorites", $path);
add_custom_modules_favorites_search();
logThis("Complete: Update custom module built using module builder to add favorites", $path);
//END SUGARCRM flav=pro ONLY

if($ce_to_pro_ent) {
	//add the global team if it does not exist
	$globalteam = new Team();
	$globalteam->retrieve('1');
	require_once($unzip_dir.'/'.$zip_from_dir.'/modules/Administration/language/en_us.lang.php');
	if(isset($globalteam->name)){
		echo 'Global '.$mod_strings['LBL_UPGRADE_TEAM_EXISTS'].'<br>';
		logThis(" Finish Building Global Team", $path);
	}else{
		$globalteam->create_team("Global", $mod_strings['LBL_GLOBAL_TEAM_DESC'], $globalteam->global_team);
	}

	logThis(" Start Building private teams", $path);

    upgradeModulesForTeam();
    logThis(" Finish Building private teams", $path);

    logThis(" Start Building the team_set and team_sets_teams", $path);
    upgradeModulesForTeamsets();
    logThis(" Finish Building the team_set and team_sets_teams", $path);

	logThis(" Start modules/Administration/upgradeTeams.php", $path);
        include('modules/Administration/upgradeTeams.php');
        logThis(" Finish modules/Administration/upgradeTeams.php", $path);

    if(check_FTS()){
    	$GLOBALS['db']->full_text_indexing_setup();
    }
}

//BEGIN SUGARCRM flav=pro ONLY
// we need to add templates when either conversion from CE to Pro+, or upgrade of Pro+ flavors from older versions
// this needs to be outside of if($ce_to_pro_ent) because it does not cover second case where $ce_to_pro_ent is 'SugarPro'
logThis("Starting to add pdf template", $path);
addPdfManagerTemplate();
logThis("Finished adding pdf template", $path);
//END SUGARCRM flav=pro ONLY

//bug: 37214 - merge config_si.php settings if available
logThis('Begin merge_config_si_settings', $path);
merge_config_si_settings(true, '', '', $path);
logThis('End merge_config_si_settings', $path);

//Upgrade connectors
logThis('Begin upgrade_connectors', $path);
upgrade_connectors();
logThis('End upgrade_connectors', $path);

//Unlink files that have been removed
if(function_exists('unlinkUpgradeFiles')) {
    unlinkUpgradeFiles($sugar_version);
}

if(function_exists('rebuildSprites') && function_exists('imagecreatetruecolor'))
{
    rebuildSprites(true);
}

//Patch for bug57431 : Module name isn't updated in portal layout editor
updateRenamedModulesLabels();

//BEGIN SUGARCRM flav=PRO ONLY
//setup forecast defualt settings
if (version_compare($sugar_version, '6.7.0', '<')) {
    require_once(clean_path($unzip_dir.'/scripts/upgrade_utils.php'));
    require_once($unzip_dir.'/'.$zip_from_dir.'/modules/Forecasts/ForecastsDefaults.php');
    ForecastsDefaults::setupForecastSettings(true, $sugar_version, getUpgradeVersion());
    ForecastsDefaults::upgradeColumns();
}
//END SUGARCRM flav=PRO ONLY

// Bug 57216 - Upgrade wizard dying on metadata upgrader because needed files were
// already called but news needed to replace them. This moves the metadata upgrader
// later in the process - rgonzalez
logThis('Checking for mobile/portal metadata upgrade...');
// 6.6 metadata enhancements for portal and wireless, should only be
// handled for upgrades FROM pre-6.6 to a version POST 6.6 and MUST be
// handled AFTER inclusion of the upgrade package files
if (!didThisStepRunBefore('commit','upgradePortalMobileMetadata')) {
    if (version_compare($sugar_version, '6.6.0', '<')) {
        if (file_exists('modules/UpgradeWizard/SidecarUpdate/SidecarMetaDataUpgrader.php')) {
            set_upgrade_progress('commit','in_progress','upgradePortalMobileMetadata','in_progress');
            logThis('Sidecar Upgrade: Preparing to upgrade metadata to 6.6.0 compatibility through the silent upgrader ...');
            require_once 'modules/UpgradeWizard/SidecarUpdate/SidecarMetaDataUpgrader.php';

            // Get the sidecar metadata upgrader
            logThis('Sidecar Upgrade: Instantiating the mobile/portal metadata upgrader ...');
            $smdUpgrader = new SidecarMetaDataUpgrader();

            // Run the upgrader
            logThis('Sidecar Upgrade: Beginning the mobile/portal metadata upgrade ...');
            $smdUpgrader->upgrade();
            logThis('Sidecar Upgrade: Mobile/portal metadata upgrade complete');

            // Log failures if any
            $failures = $smdUpgrader->getFailures();
            if (!empty($failures)) {
                logThis('Sidecar Upgrade: ' . count($failures) . ' metadata files failed to upgrade through the silent upgrader:');
                logThis(print_r($failures, true));
            } else {
                logThis('Sidecar Upgrade: Mobile/portal metadata upgrade ran with no failures:');
                logThis($smdUpgrader->getCountOfFilesForUpgrade() . ' files were upgraded.');
            }

            // Reset the progress
            set_upgrade_progress('commit','in_progress','upgradePortalMobileMetadata','done');
        }
    }
}
// END sidecar metadata updates
logThis('Mobile/portal metadata upgrade check complete');

///////////////////////////////////////////////////////////////////////////////
////	TAKE OUT TRASH
if(empty($errors)) {
	set_upgrade_progress('end','in_progress','unlinkingfiles','in_progress');
	logThis('Taking out the trash, unlinking temp files.', $path);
	unlinkUWTempFiles();
	removeSilentUpgradeVarsCache();
	logThis('Taking out the trash, done.', $path);
}

// rebuild cache
SugarAutoLoader::buildCache();
///////////////////////////////////////////////////////////////////////////////
////	RECORD ERRORS

$phpErrors = ob_get_contents();
ob_end_clean();
logThis("**** Potential PHP generated error messages: {$phpErrors}", $path);

if(count($errors) > 0) {
	foreach($errors as $error) {
		logThis("****** SilentUpgrade ERROR: {$error}", $path);
	}
	echo "FAILED\n";
} else {
	logThis("***** SilentUpgrade completed successfully.", $path);
	echo "********************************************************************\n";
	echo "*************************** SUCCESS ********************************\n";
	echo "********************************************************************\n";
	echo "******** If your pre-upgrade Leads data is not showing  ************\n";
	echo "******** Or you see errors in detailview subpanels  ****************\n";
	echo "************* In order to resolve them  ****************************\n";
	echo "******** Log into application as Administrator  ********************\n";
	echo "******** Go to Admin panel  ****************************************\n";
	echo "******** Run Repair -> Rebuild Relationships  **********************\n";
	echo "********************************************************************\n";
}


?>
