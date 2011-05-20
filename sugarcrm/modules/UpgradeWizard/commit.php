<?php
if(!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise End User
 * License Agreement("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
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
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright(C) 2004-2007 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: commit.php 58188 2010-09-16 03:20:33Z kjing $
 * Description:
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * *******************************************************************************/
require_once('include/SugarLogger/SugarLogger.php');

$trackerManager = TrackerManager::getInstance();
$trackerManager->pause();
$trackerManager->unsetMonitors();

//BEGIN SUGARCRM flav=pro ONLY
//bug: 38017 - SugarView is not yet pulled out of memory and to avoid putting a check-in there for every call, will just
//put in here for one call
$timeStamp = gmdate('Y-m-d H:i:s');
$monitor3 = $trackerManager->getMonitor('tracker_sessions');
if(!empty($monitor3)) {
   $monitor3->setValue('date_start', $timeStamp);
}
//END SUGARCRM flav=pro ONLY
$_SESSION['upgrade_complete'] = '';
$_REQUEST['upgradeWizard'] = true;


logThis('[At commit.php]');
$stop = true; // flag to show "next"

//refreshing mod_strings

global $mod_strings;
$curr_lang = 'en_us';
if(isset($GLOBALS['current_language']) && ($GLOBALS['current_language'] != null)){
	$curr_lang = $GLOBALS['current_language'];
}
return_module_language($curr_lang, 'UpgradeWizard');


$standardErrorLevel = error_reporting();
logThis("Setting error_reporting() to E_ERROR while running upgrade");
error_reporting(E_ERROR);

set_time_limit(0);
/*
 * [unzip_dir] => /Users/curisu/www/head/cache/upload//upgrades/temp/QSugp3
 * [zip_from_dir]  => SugarEnt-Upgrade-4.0.1-to-4.2.1
 * rest_dir: /Users/curisu/www/head/cache/upload/SugarEnt-Upgrade-4.0.1-to-4.2.1-restore
 */

// flag upgradeSql script run method
$_SESSION['schema_change'] = $_REQUEST['schema_change'];
if(didThisStepRunBefore('commit')){
	$_SESSION['committed'] = true;
}
else{
	set_upgrade_progress('commit','in_progress','commit','in_progress');
}
//Initialize session errors array
if(!isset($_SESSION['sqlSkippedQueries']) && !is_array($_SESSION['sqlSkippedQueries'])){
	$_SESSION['sqlSkippedQueries'] = array();
}
// prevent "REFRESH" double commits
if(!isset($_SESSION['committed'])) {
	//$_SESSION['committed'] = true; // flag to prevent refresh double-commit
	//set the flag at the end though
	unset($_SESSION['rebuild_relationships']);
	unset($_SESSION['rebuild_extensions']);
    //put checks for follwing files

	if(!isset($_SESSION['unzip_dir']) || empty($_SESSION['unzip_dir'])) {
		logThis('unzipping files in upgrade archive...');
		$errors					= array();
		$base_upgrade_dir		= $sugar_config['upload_dir'] . "/upgrades";
		$base_tmp_upgrade_dir	= "$base_upgrade_dir/temp";
		$unzip_dir = '';
		//also come up with mechanism to read from upgrade-progress file
		if(!isset($_SESSION['install_file']) || empty($_SESSION['install_file']) || !is_file($_SESSION['install_file'])) {
			if (file_exists(clean_path($base_tmp_upgrade_dir)) && $handle = opendir(clean_path($base_tmp_upgrade_dir))) {
		    		while (false !== ($file = readdir($handle))) {
		    		if($file !="." && $file !="..")	{
					 //echo $base_tmp_upgrade_dir."/".$file.'</br>';
					 if(is_file($base_tmp_upgrade_dir."/".$file."/manifest.php")){
					 	require_once($base_tmp_upgrade_dir."/".$file."/manifest.php");
					 	$package_name= $manifest['copy_files']['from_dir'];
					 	//echo file_exists($base_tmp_upgrade_dir."/".$file."/".$package_name).'</br>';
					 	if(file_exists($base_tmp_upgrade_dir."/".$file."/".$package_name) && file_exists($base_tmp_upgrade_dir."/".$file."/scripts") && file_exists($base_tmp_upgrade_dir."/".$file."/manifest.php")){
					 		//echo 'Yeah this the directory '. $base_tmp_upgrade_dir."/".$file;
					 		$unzip_dir = $base_tmp_upgrade_dir."/".$file;
					 		if(file_exists($sugar_config['upload_dir'].'/upgrades/patch/'.$package_name.'.zip')){
					 			$_SESSION['install_file'] = $sugar_config['upload_dir'].'/upgrades/patch/'.$package_name.'.zip';
					 			break;
					 		}
						}
					  }
		    		}
		    	}
			}
		}
        if(!isset($_SESSION['install_file']) || empty($_SESSION['install_file'])){
        	unlinkTempFiles();
        	resetUwSession();
        	echo 'Upload File not found so redirecting to Upgrade Start ';
        	$redirect_new_wizard = $sugar_config['site_url' ].'/index.php?module=UpgradeWizard&action=index';
        	echo '<form name="redirect" action="' .$redirect_new_wizard. '"  method="POST">';
$upgrade_directories_not_found =<<<eoq
	<table cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th colspan="2" align="left">
				<span class='error'><b>'Upload file missing or has been deleted. Refresh the page to go back to UpgradeWizard start'</b></span>
			</th>
		</tr>
	</table>
eoq;
$uwMain = $upgrade_directories_not_found;
				return '';
        }
		$install_file			= urldecode( $_SESSION['install_file'] );
		$show_files				= true;
		if(empty($unzip_dir)){
			$unzip_dir				= mk_temp_dir( $base_tmp_upgrade_dir );
		}
		$zip_from_dir			= ".";
		$zip_to_dir				= ".";
		$zip_force_copy			= array();

		if(!$unzip_dir){
			logThis('Could not create a temporary directory using mk_temp_dir( $base_tmp_upgrade_dir )');
			die($mod_strings['ERR_UW_NO_CREATE_TMP_DIR']);
		}

		//double check whether unzipped .
		if(file_exists($unzip_dir ."/scripts") && file_exists($unzip_dir."/manifest.php")){
        	//already unzipped
		}
		else{
			unzip( $install_file, $unzip_dir );
		}

		// assumption -- already validated manifest.php at time of upload
		require_once( "$unzip_dir/manifest.php" );

		if( isset( $manifest['copy_files']['from_dir'] ) && $manifest['copy_files']['from_dir'] != "" ){
		    $zip_from_dir   = $manifest['copy_files']['from_dir'];
		}
		if( isset( $manifest['copy_files']['to_dir'] ) && $manifest['copy_files']['to_dir'] != "" ){
		    $zip_to_dir     = $manifest['copy_files']['to_dir'];
		}
		if( isset( $manifest['copy_files']['force_copy'] ) && $manifest['copy_files']['force_copy'] != "" ){
		    $zip_force_copy     = $manifest['copy_files']['force_copy'];
		}
		if( isset( $manifest['version'] ) ){
		    $version    = $manifest['version'];
		}
		if( !is_writable( "config.php" ) ){
			return $mod_strings['ERR_UW_CONFIG'];
		}

		$_SESSION['unzip_dir'] = clean_path($unzip_dir);
		$_SESSION['zip_from_dir'] = clean_path($zip_from_dir);
		logThis('unzip done.');
	} else {
		$unzip_dir = $_SESSION['unzip_dir'];
		$zip_from_dir = $_SESSION['zip_from_dir'];
	}

    //check if $_SESSION['unzip_dir'] and $_SESSION['zip_from_dir'] exist
	if(!isset($_SESSION['unzip_dir']) || !file_exists($_SESSION['unzip_dir'])
		|| !isset($_SESSION['install_file']) || empty($_SESSION['install_file']) || !file_exists($_SESSION['install_file'])){
		    //redirect to start
	    unlinkTempFiles();
		resetUwSession();
		echo 'Upload File not found so redirecting to Upgrade Start ';
		$redirect_new_wizard = $sugar_config['site_url' ].'/index.php?module=UpgradeWizard&action=index';
		echo '<form name="redirect" action="' .$redirect_new_wizard. '"  method="POST">';
$upgrade_directories_not_found =<<<eoq
	<table cellpadding="3" cellspacing="0" border="0">
		<tr>
			<th colspan="2" align="left">
				<span class='error'><b>'Upload file missing or has been deleted. Refresh the page to go back to UpgradeWizard start'</b></span>
			</th>
		</tr>
	</table>
eoq;
$uwMain = $upgrade_directories_not_found;
				return '';
	}
	$install_file		= urldecode($_SESSION['install_file']);
	$file_action		= "";
	$uh_status			= "";
	$errors				= array();
	$out				= '';
	$backupFilesExist	= false;
	$rest_dir			= clean_path(remove_file_extension($install_file) . "-restore");

	///////////////////////////////////////////////////////////////////////////////
	////	MAKE BACKUPS OF TARGET FILES
	if(!didThisStepRunBefore('commit','commitMakeBackupFiles')){
		set_upgrade_progress('commit','in_progress','commitMakeBackupFiles','in_progress');
		$errors = commitMakeBackupFiles($rest_dir, $install_file, $unzip_dir, $zip_from_dir, array());
		set_upgrade_progress('commit','in_progress','commitMakeBackupFiles','done');
	}
	////	END MAKE BACKUPS OF TARGET FILES
	///////////////////////////////////////////////////////////////////////////////


	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE PREINSTALL SCRIPTS
	if(empty($errors)) {
		$file = "$unzip_dir/" . constant('SUGARCRM_PRE_INSTALL_FILE');
		if(is_file($file)) {
			$out .= "{$mod_strings['LBL_UW_INCLUDING']}: {$file} <br>\n";
			include($file);
			if(!didThisStepRunBefore('commit','pre_install')){
				logThis('Running pre_install()...');
				set_upgrade_progress('commit','in_progress','pre_install','in_progress');
				pre_install();
				set_upgrade_progress('commit','in_progress','pre_install','done');
				logThis('pre_install() done.');
			}
		}
	}

	////	HANDLE PREINSTALL SCRIPTS
	///////////////////////////////////////////////////////////////////////////////
        //Clean smarty from cache
	    $cachedir = sugar_cached('smarty');
	    if(is_dir($cachedir)){
        	$allModFiles = array();
        	$allModFiles = findAllFiles($cachedir,$allModFiles);
           foreach($allModFiles as $file){
	           	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
	           	if(file_exists($file)){
					unlink($file);
	           	}
           }
        }


		//Also add the three-way merge here. The idea is after the 451 html files have
		//been converted run the 3-way merge. If 500 then just run the 3-way merge
        $ce_to_pro_ent = isset($manifest['name']) && ($manifest['name'] == 'SugarCE to SugarPro' || $manifest['name'] == 'SugarCE to SugarEnt');

		if(file_exists('modules/UpgradeWizard/SugarMerge/SugarMerge.php')){
		    require_once('modules/UpgradeWizard/SugarMerge/SugarMerge.php');
		    if(isset($_SESSION['unzip_dir']) && isset($_SESSION['zip_from_dir']) && !isset($_SESSION['sugarMergeRunResults'])){
		        $merger = new SugarMerge($_SESSION['unzip_dir'].'/'.$_SESSION['zip_from_dir']);
		        //Perform the actual merge and store which modules were merged.  We will rolllback the files if the
		        //user determines that they did not want to upgade a particular module.
		        $_SESSION['sugarMergeRunResults'] = $merger->mergeAll(TRUE,TRUE,TRUE);
		        logThis('Commit step finished SugarMerge run with the following results:' . print_r($_SESSION['sugarMergeRunResults'], true));
		    }
		}

         //COPY ALL FILES FROM UPLOADED UPGRADE PACKAGE

         if(!didThisStepRunBefore('commit','commitCopyNewFiles')){
				set_upgrade_progress('commit','in_progress','commitCopyNewFiles','in_progress');
				$split = commitCopyNewFiles($unzip_dir, $zip_from_dir);
		 		$copiedFiles = $split['copiedFiles'];
		 		$skippedFiles = $split['skippedFiles'];
				set_upgrade_progress('commit','in_progress','commitCopyNewFiles','done');
         }
		 //END COPY NEW FILES INTO TARGET INSTANCE
    ///////////////////////////////////////////////////////////////////////////////
	////	HANDLE POSTINSTALL SCRIPTS
	logThis('Starting post_install()...');
    if (!function_exists("inDeveloperMode")) {
    	//this function was introduced from tokyo in the file include/utils.php, so when upgrading from 5.1x and 5.2x we should declare the this function
        function inDeveloperMode()
        {
            return isset($GLOBALS['sugar_config']['developerMode']) && $GLOBALS['sugar_config']['developerMode'];
        }
    }
	if(empty($errors)) {
		if(!didThisStepRunBefore('commit','post_install')){
			$file = "$unzip_dir/" . constant('SUGARCRM_POST_INSTALL_FILE');
			if(is_file($file)) {
				//set_upgrade_progress('commit','in_progress','post_install','in_progress');
				$progArray['post_install']='in_progress';
				post_install_progress($progArray,'set');
				include($file);
				post_install();
				//set process to done
				$progArray['post_install']='done';
				//set_upgrade_progress('commit','in_progress','post_install','done');
				post_install_progress($progArray,'set');
			}
		}

	   require("sugar_version.php");

       if($_SESSION['current_db_version'] != $_SESSION['target_db_version']){
			logThis('Performing UWrebuild()...');
			UWrebuild();
			logThis('UWrebuild() done.');
       }

		//set the logger before rebuilding config
		if(!isset($sugar_config['logger'])){
			$sugar_config['logger'] =array (
				'level'=>'fatal',
			    'file' =>
			      array (
				      'ext' => '.log',
				      'name' => 'sugarcrm',
				      'dateFormat' => '%c',
				      'maxSize' => '10MB',
				      'maxLogs' => 10,
				      'suffix' => '%m_%Y',
			  	  ),
			);
		}

        // Set the default max tabs to 7
        $sugar_config['default_max_tabs'] = '7';

		if(!rebuildConfigFile($sugar_config, $sugar_version)) {
			logThis('*** ERROR: could not write config.php! - upgrade will fail!');
			$errors[] = $mod_strings['ERR_UW_CONFIG_WRITE'];
		}
	}
	logThis('post_install() done.');
	//// END POSTINSTALL SCRIPTS
	///////////////////////////////////////////////////////////////////////////////
	
logThis('check if current_db_version in $_SESSION equals target_db_version in $_SESSION');	
if($_SESSION['current_db_version'] == $_SESSION['target_db_version']){

	logThis('current_db_version in $_SESSION and target_db_version in $_SESSION are equal');
	$_SESSION['license_seats_needed'] = '';
	//Clean modules from cache
	$cachedir = sugar_cached("modules");
    if(is_dir($cachedir)){
		logThis("clear $cachedir files");
    	$allModFiles = array();
    	$allModFiles = findAllFiles($cachedir,$allModFiles);
       foreach($allModFiles as $file){
           	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
           	if(file_exists($file))
           	{
           		logThis('unlink ' . $file);
				unlink($file);
           	}
       }
    }
    //Clean jsLanguage from cache
    $cachedir = sugar_cached("jsLanguage");
    if(is_dir($cachedir)){
		logThis("clear $cachedir files");
    	$allModFiles = array();
    	$allModFiles = findAllFiles($cachedir,$allModFiles);
       foreach($allModFiles as $file){
           	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
           	if(file_exists($file))
           	{
           		logThis('unlink ' . $file);
				unlink($file);
           	}
       }
    }
    
}
logThis('finished check to see if current_db_version in $_SESSION equals target_db_version in $_SESSION');

//Look for chance folder and delete it if found. Bug 23595
if(function_exists('deleteChance'))
{
	logThis('running deleteChance() function');
	@deleteChance();
}

//also add the cache cleaning here.
if(function_exists('deleteCache'))
{
	logThis('running deleteCache() function');
	@deleteCache();
}

//add tabs
$from_dir = clean_path(remove_file_extension($install_file) . "-restore");
logThis('call addNewSystemTabsFromUpgrade(' . $from_dir . ')');
addNewSystemTabsFromUpgrade($from_dir);
logThis('finished addNewSystemTabsFromUpgrade');

//run fix on dropdown lists that may have been incorrectly named
//fix_dropdown_list();

	///////////////////////////////////////////////////////////////////////////////
	////	REGISTER UPGRADE

	logThis('Registering upgrade with UpgradeHistory');
	if(!didThisStepRunBefore('commit','upgradeHistory')){
		set_upgrade_progress('commit','in_progress','upgradeHistory','in_progress');
		if(empty($errors)) {
			$file_action = "copied";
			// if error was encountered, script should have died before now
			$new_upgrade = new UpgradeHistory();
			$new_upgrade->filename = $install_file;
			$new_upgrade->md5sum = md5_file($install_file);
			$new_upgrade->type = 'patch';
			$new_upgrade->version = $sugar_version;
			$new_upgrade->status = "installed";
			$new_upgrade->manifest =(!empty($_SESSION['install_manifest']) ? $_SESSION['install_manifest'] : '');
			$new_upgrade->save();
		}
		set_upgrade_progress('commit','in_progress','upgradeHistory','done');
	}
	////	REGISTER UPGRADE
	///////////////////////////////////////////////////////////////////////////////
}else{
    $backupFilesExist = false;
    $copiedFiles = array();
    $skippedFiles = array();
}

// flag to prvent double-commits via refresh
$_SESSION['committed'] = true;

///////////////////////////////////////////////////////////////////////////////
////	FINISH AND OUTPUT
if(empty($errors)) {
	$stop = false;
}

$backupDesc = '';
if($backupFilesExist) {
	$backupDesc .= "<b>{$mod_strings['LBL_UW_BACKUP_FILES_EXIST_TITLE']}</b><br />";
	$backupDesc .= $mod_strings['LBL_UW_BACKUP_FILES_EXIST'] . ': ' . $rest_dir;
}

$customized_mods_Desc = '';
$old_schema= '';
$old_schema_opt = '';
$skipped_queries = '';
if($_SESSION['current_db_version'] != $_SESSION['target_db_version']){
    global $sugar_version;
    $origVersion = substr(preg_replace("/[^0-9]/", "", $_SESSION['current_db_version']),0,3);
	$destVersion = substr(preg_replace("/[^0-9]/", "", $_SESSION['target_db_version']),0,3);

	//old schema to be dropped
	$old_schema_contents = '';
	if(file_exists($_SESSION['unzip_dir'].'/scripts/drop_'.$origVersion.'_schema_after_upgrade_'.$destVersion.'.php')){
		require_once($_SESSION['unzip_dir'].'/scripts/drop_'.$origVersion.'_schema_after_upgrade_'.$destVersion.'.php');
		ob_start();
			$old_schema_contents=@drop_preUpgardeSchema(true);
		ob_end_clean();
	}
	if($old_schema_contents != null && strlen($old_schema_contents) >0){
		$old_schema  = "<p><a href='javascript:void(0); toggleNwFiles(\"old_schemashow\");'>{$mod_strings['LBL_UW_SHOW_OLD_SCHEMA_TO_DROP']}</a>";
		$old_schema .= "<div id='old_schemashow' style='display:none;'>";
		$old_schema .= "<textarea readonly cols='80' rows='10'>{$old_schema_contents}</textarea>";
		$old_schema .= "</div></p>";

		$old_schema_opt="<b>{$mod_strings['LBL_UW_DROP_SCHEMA_METHOD']}</b>
						<select name=\"schema_drop\" id=\"select_schema_drop\" onchange=\"checkSchemaDropStatus();\">
							<option value=\"manual\">{$mod_strings['LBL_UW_DROP_SCHEMA_MANUAL']}</option>
							<option value=\"sugar\">{$mod_strings['LBL_UW_DROP_SCHEMA_UPGRADE_WIZARD']}</option>
						</select>
					";
	}

	//also add the cache cleaning here.
	if(function_exists('deleteCache')){
		@deleteCache();
	}
}

$copiedDesc = '';
if(count($copiedFiles) > 0) {
	$copiedDesc .= "<b>{$mod_strings['LBL_UW_COPIED_FILES_TITLE']}</b><br />";
	$copiedDesc .= "<a href='javascript:void(0); toggleNwFiles(\"copiedFiles\");'>{$mod_strings['LBL_UW_SHOW']}</a>";
	$copiedDesc .= "<div id='copiedFiles' style='display:none;'>";

	foreach($copiedFiles as $file) {
		$copiedDesc .= $file . "<br />";
	}
	$copiedDesc .= "</div>";
}

$skippedDesc = '';
if(count($skippedFiles) > 0) {
	$skippedDesc .= "<b>{$mod_strings['LBL_UW_SKIPPED_FILES_TITLE']}</b><br />";
	$skippedDesc .= "<a href='javascript:void(0); toggleNwFiles(\"skippedFiles\");'>{$mod_strings['LBL_UW_SHOW']}</a>";
	$skippedDesc .= "<div id='skippedFiles' style='display:none;'>";

	foreach($skippedFiles as $file) {
		$skippedDesc .= $file . "<br />";
	}
	$skippedDesc .= "</div>";
}

$rebuildResult = "<b>{$mod_strings['LBL_UW_REBUILD_TITLE']}</b><br />";
$rebuildResult .= "<a href='javascript:void(0); toggleRebuild();'>{$mod_strings['LBL_UW_SHOW']}</a> <div id='rebuildResult'></div>";

$rebuildResult = '';

//moving unlink files to last
//unlinkTempFiles();


$skipped_queries_Desc='';
if(isset($_SESSION['sqlSkippedQueries']) && $_SESSION['sqlSkippedQueries'] != null && is_array($_SESSION['sqlSkippedQueries']) && sizeof($_SESSION['sqlSkippedQueries'])>0){
	$skipped_queries_Desc .= "<b>{$mod_strings['LBL_UW_SKIPPED_QUERIES_ALREADY_EXIST']}</b><br />";
	$skipped_queries_Desc .= "<a href='javascript:void(0); toggleNwFiles(\"skippedQueries\");'>{$mod_strings['LBL_UW_SHOW']}</a>";
	$skipped_queries_Desc .= "<div id='skippedQueries' style='display:none;'>";
	if($_SESSION['sqlSkippedQueries'] != null){
	     $skipped_queries_Desc .= $mod_strings['LBL_UW_SKIPPED_QUERIES_ALREADY_EXIST']. "<br />";
	     foreach($_SESSION['sqlSkippedQueries'] as $skippedQ) {
			$skipped_queries_Desc .= $skippedQ . "<br />";
		 }
	}
 }
$delete_chance='';
if(isset($_SESSION['chance']) && $_SESSION['chance'] != null){
	$delete_chance .= "<b>Remove the folder: {$_SESSION['chance']}</b><br />";
 }
if(empty($mod_strings['LBL_UPGRADE_TAKES_TIME_HAVE_PATIENCE'])){
		$mod_strings['LBL_UPGRADE_TAKES_TIME_HAVE_PATIENCE'] = 'Upgrade may take some time';
}

///////////////////////////////////////////////////////////////////////////////
////	HANDLE REMINDERS
commitHandleReminders($skippedFiles);
////	HANDLE REMINDERS
///////////////////////////////////////////////////////////////////////////////


logThis("Resetting error_reporting() to system level.");
error_reporting($standardErrorLevel);

///////////////////////////////////////////////////////////////////////////////
////	OUTPUT
$uwMain =<<<eoq
<script type="text/javascript" language="javascript">
	function toggleRebuild() {
		var target = document.getElementById('rebuildResult');

		if(target.innerHTML == '') {
			target.innerHTML = rebuildResult; // found in UWrebuild()
		} else {
			target.innerHTML = '';
		}
	}
</script>
<table cellpadding="3" cellspacing="0" border="0">
	<tr>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td align="left">
			<p>
			{$delete_chance}
			</p>
			<p>
			{$backupDesc}
			</p>
			<p>
			{$customized_mods_Desc}
			</p>
			<p>
			{$copiedDesc}
			</p>
			<p>
			{$skippedDesc}
			</p>
			<p>
			{$skipped_queries_Desc}
			</p>
			<p>
			{$rebuildResult}
			</p>
		</td>
	</tr>
	<tr><td>
		<p>
		{$old_schema}
		</p>
	</td></tr>
	<tr><td>
		{$old_schema_opt}
	</td></tr>
</table>
<div id="upgradeDiv" style="display:none">
    <table cellspacing="0" cellpadding="0" border="0">
        <tr><td>
           <p><img src='modules/UpgradeWizard/processing.gif'> <br></p>
        </td></tr>
     </table>
 </div>
 <script>
function checkSchemaDropStatus() {
	if(document.getElementById('select_schema_drop') != null){
		var schemaSelect = document.getElementById('select_schema_drop');
		var schemaDropMethod = document.getElementById('schema_drop');
		if(schemaSelect.options[schemaSelect.selectedIndex].value == 'manual') {
			schemaDropMethod.value = 'manual';
		} else {
			schemaDropMethod.value = 'sugar';
		}
   }
}
 checkSchemaDropStatus();
 </script>
eoq;

//set the upgrade progress status.
set_upgrade_progress('commit','done','commit','done');
$showBack = false;
$showCancel = false;
$showRecheck = false;
$showNext =($stop) ? false : true;

$GLOBALS['top_message'] = "<b>{$mod_strings['LBL_UW_COMMIT_DESC']}</b>";
$stepBack = $_REQUEST['step'] - 1;
//Skip ahead to the end page as no layouts need to be merged.
$skipLayouts = true;
foreach($_SESSION['sugarMergeRunResults'] as $mergeModule => $mergeModuleFileList){
    if(!empty($mergeModuleFileList)){
        $skipLayouts = false;
    }
}
$stepNext = $skipLayouts ? $_REQUEST['step'] + 2 : $_REQUEST['step'] + 1;
$stepCancel = -1;
$stepRecheck = $_REQUEST['step'];

$_SESSION['step'][$steps['files'][$_REQUEST['step']]] =($stop) ? 'failed' : 'success';

// clear out the theme cache
// clear out the theme cache
if(!class_exists('SugarThemeRegistry')){
    require_once('include/SugarTheme/SugarTheme.php');
}
SugarThemeRegistry::buildRegistry();
SugarThemeRegistry::clearAllCaches();

// re-minify the JS source files
$_REQUEST['root_directory'] = getcwd();
$_REQUEST['js_rebuild_concat'] = 'rebuild';
require_once('jssource/minify.php');
