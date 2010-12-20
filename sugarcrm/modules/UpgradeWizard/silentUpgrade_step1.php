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
///////////////////////////////////////////////////////////////////////////////
////	UTILITIES THAT MUST BE LOCAL :(
function prepSystemForUpgradeSilent() {
	global $subdirs;
	global $cwd;
	global $sugar_config;

	// make sure dirs exist
	foreach($subdirs as $subdir) {
		if(!is_dir(clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/{$subdir}"))) {
	    	mkdir_recursive(clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/{$subdir}"));
		}
	}
}

//local function for clearing cache
function clearCacheSU($thedir, $extension) {
	if ($current = @opendir($thedir)) {
		while (false !== ($children = readdir($current))) {
			if ($children != "." && $children != "..") {
				if (is_dir($thedir . "/" . $children)) {
					clearCacheSU($thedir . "/" . $children, $extension);
				}
				elseif (is_file($thedir . "/" . $children) && substr_count($children, $extension)) {
					unlink($thedir . "/" . $children);
				}
			}
		}
	}
 }
 //Bug 24890, 24892. default_permissions not written to config.php. Following function checks and if
 //no found then adds default_permissions to the config file.
 function checkConfigForPermissions(){
     if(file_exists(getcwd().'/config.php')){
         require(getcwd().'/config.php');
     }
     global $sugar_config;
     if(!isset($sugar_config['default_permissions'])){
             $sugar_config['default_permissions'] = array (
                     'dir_mode' => 02770,
                     'file_mode' => 0660,
                     'user' => '',
                     'group' => '',
             );
         ksort($sugar_config);
         if(is_writable('config.php') && write_array_to_file("sugar_config", $sugar_config,'config.php')) {
        	//writing to the file
 		}
     }
}
function checkLoggerSettings(){
	if(file_exists(getcwd().'/config.php')){
         require(getcwd().'/config.php');
     }
    global $sugar_config;
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
		 ksort($sugar_config);
         if(is_writable('config.php') && write_array_to_file("sugar_config", $sugar_config,'config.php')) {
        	//writing to the file
 		}
	 }
}
 
function checkResourceSettings(){
	if(file_exists(getcwd().'/config.php')){
         require(getcwd().'/config.php');
     }
    global $sugar_config;
	if(!isset($sugar_config['resource_management'])){
	  $sugar_config['resource_management'] =
		  array (
		    'special_query_limit' => 50000,
		    'special_query_modules' =>
		    array (
		      0 => 'Reports',
		      1 => 'Export',
		      2 => 'Import',
		      3 => 'Administration',
		      4 => 'Sync',
		    ),
		    'default_limit' => 1000,
		  );
		 ksort($sugar_config);
         if(is_writable('config.php') && write_array_to_file("sugar_config", $sugar_config,'config.php')) {
        	//writing to the file
 		}
	}
}


//rebuild all relationships...
function rebuildRelations($pre_path = ''){
	$_REQUEST['silent'] = true;
	include($pre_path.'modules/Administration/RebuildRelationship.php');
	 $_REQUEST['upgradeWizard'] = true;
	 include($pre_path.'modules/ACL/install_actions.php');
}

function createMissingRels(){
	$relForObjects = array('leads'=>'Leads','campaigns'=>'Campaigns','prospects'=>'Prospects');
	foreach($relForObjects as $relObjName=>$relModName){
		//assigned_user
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_assigned_user'";
		$result= $GLOBALS['db']->query($query, true);
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
		if($GLOBALS['db']->checkError()){
			//log this
		}
		if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
						VALUES ('{$guid}', '{$relObjName}_assigned_user','Users','users','id','{$relModName}','{$relObjName}','assigned_user_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}
		}
		//modified_user
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_modified_user'";
		$result= $GLOBALS['db']->query($query, true);
		if($GLOBALS['db']->checkError()){
			//log this
		}
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
		if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
						VALUES ('{$guid}', '{$relObjName}_modified_user','Users','users','id','{$relModName}','{$relObjName}','modified_user_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}
		}
		//created_by
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_created_by'";
		$result= $GLOBALS['db']->query($query, true);
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
    	if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
						VALUES ('{$guid}', '{$relObjName}_created_by','Users','users','id','{$relModName}','{$relObjName}','created_by',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}
    	}
		$guid = create_guid();
		$query = "SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_team'";
		$result= $GLOBALS['db']->query($query, true);
		$a = null;
		$a = $GLOBALS['db']->fetchByAssoc($result);
		if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
							VALUES ('{$guid}', '{$relObjName}_team','Teams','teams','id','{$relModName}','{$relObjName}','team_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$GLOBALS['db']->query($qRel);
			if($GLOBALS['db']->checkError()){
				//log this
			}

		}
	}
	//Also add tracker perf relationship
	$guid = create_guid();
	$query = "SELECT id FROM relationships WHERE relationship_name = 'tracker_monitor_id'";
	$result= $GLOBALS['db']->query($query, true);
	if($GLOBALS['db']->checkError()){
		//log this
	}
	$a = null;
	$a = $GLOBALS['db']->fetchByAssoc($result);
	if($GLOBALS['db']->checkError()){
		//log this
	}
	if(!isset($a['id']) && empty($a['id']) ){
		$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
					VALUES ('{$guid}', 'tracker_monitor_id','TrackerPerfs','tracker_perf','monitor_id','Trackers','tracker','monitor_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
		$GLOBALS['db']->query($qRel);
		if($GLOBALS['db']->checkError()){
			//log this
		}
	}
}


/**
 * This function will merge password default settings into config file
 * @param   $sugar_config
 * @param   $sugar_version
 * @return  bool true if successful
 */
function merge_passwordsetting($sugar_config, $sugar_version) {	
   
     $passwordsetting_defaults = array (
        'passwordsetting' => array (
            'minpwdlength' => '',
            'maxpwdlength' => '',
            'oneupper' => '',
            'onelower' => '',
            'onenumber' => '',
            'onespecial' => '',
            'SystemGeneratedPasswordON' => '',
            'generatepasswordtmpl' => '',
            'lostpasswordtmpl' => '',
            'customregex' => '',
            'regexcomment' => '',
            'forgotpasswordON' => false,
            'linkexpiration' => '1',
            'linkexpirationtime' => '30',
            'linkexpirationtype' => '1',
            'userexpiration' => '0',
            'userexpirationtime' => '',
            'userexpirationtype' => '1',
            'userexpirationlogin' => '',
            'systexpiration' => '0',
            'systexpirationtime' => '',
            'systexpirationtype' => '0',
            'systexpirationlogin' => '',
            'lockoutexpiration' => '0',
            'lockoutexpirationtime' => '',
            'lockoutexpirationtype' => '1',
            'lockoutexpirationlogin' => '',
        ),
    );
        
    $sugar_config = sugarArrayMerge($passwordsetting_defaults, $sugar_config );

    // need to override version with default no matter what
    $sugar_config['sugar_version'] = $sugar_version;

    ksort( $sugar_config );

    if( write_array_to_file( "sugar_config", $sugar_config, "config.php" ) ){
        return true;
    }
    else {
        return false;
    }
}

function addDefaultModuleRoles($defaultRoles = array()) {
	foreach($defaultRoles as $roleName=>$role){
        foreach($role as $category=>$actions){
            foreach($actions as $name=>$access_override){
                    $query = "SELECT * FROM acl_actions WHERE name='$name' AND category = '$category' AND acltype='$roleName' AND deleted=0 ";
					$result = $GLOBALS['db']->query($query);
					//only add if an action with that name and category don't exist
					$row=$GLOBALS['db']->fetchByAssoc($result);
					if ($row == null) {
	                	$guid = create_guid();
	                	$currdate = gmdate($GLOBALS['timedate']->get_db_date_time_format());
	                	$query= "INSERT INTO acl_actions (id,date_entered,date_modified,modified_user_id,name,category,acltype,aclaccess,deleted ) VALUES ('$guid','$currdate','$currdate','1','$name','$category','$roleName','$access_override','0')";
						$GLOBALS['db']->query($query);
						if($GLOBALS['db']->checkError()){
							//log this
						}
	                }
            }
        }
	}
}

function verifyArguments($argv,$usage_dce,$usage_regular){
    $upgradeType = '';
    $cwd = getcwd(); // default to current, assumed to be in a valid SugarCRM root dir.
    if(isset($argv[3])) {
        if(is_dir($argv[3])) {
            $cwd = $argv[3];
            chdir($cwd);
        } else {
            echo "*******************************************************************************\n";
            echo "*** ERROR: 3rd parameter must be a valid directory.  Tried to cd to [ {$argv[3]} ].\n";
            exit(1);
        }
    }

    //check if this is an instance
    if(is_file("{$cwd}/ini_setup.php")){
        // this is an instance
        $upgradeType = constant('DCE_INSTANCE');
        //now that this is dce instance we want to make sure that there are
        // 7 arguments
        if(count($argv) < 7) {
            echo "*******************************************************************************\n";
            echo "*** ERROR: Missing required parameters.  Received ".count($argv)." argument(s), require 7.\n";
            echo $usage_dce;
            echo "FAILURE\n";
            exit(1);
        }
        // this is an instance
        if(!is_dir($argv[1])) { // valid directory . template path?
            echo "*******************************************************************************\n";
            echo "*** ERROR: First argument must be a full path to the template. Got [ {$argv[1]} ].\n";
            echo $usage_dce;
            echo "FAILURE\n";
            exit(1);
        }
    }
    else if(is_file("{$cwd}/include/entryPoint.php")) {
        //this should be a regular sugar install
        $upgradeType = constant('SUGARCRM_INSTALL');
        //check if this is a valid zip file
        if(!is_file($argv[1])) { // valid zip?
            echo "*******************************************************************************\n";
            echo "*** ERROR: First argument must be a full path to the patch file. Got [ {$argv[1]} ].\n";
            echo $usage_regular;
            echo "FAILURE\n";
            exit(1);
        }
        if(count($argv) < 5) {
            echo "*******************************************************************************\n";
            echo "*** ERROR: Missing required parameters.  Received ".count($argv)." argument(s), require 5.\n";
            echo $usage_regular;
            echo "FAILURE\n";
            exit(1);
        }
    }
    else {
        //this should be a regular sugar install
        echo "*******************************************************************************\n";
        echo "*** ERROR: Tried to execute in a non-SugarCRM root directory.\n";
        exit(1);      
    }

    if(isset($argv[7]) && file_exists($argv[7].'SugarTemplateUtilties.php')){
        require_once($argv[7].'SugarTemplateUtilties.php');
    }
    
    return $upgradeType;
}

function upgradeDCEFiles($argv,$instanceUpgradePath){
	//copy and update following files from upgrade package
	$upgradeTheseFiles = array('cron.php','download.php','index.php','install.php','soap.php','sugar_version.php','vcal_server.php');
	foreach($upgradeTheseFiles as $file){
		$srcFile = clean_path("{$instanceUpgradePath}/$file");
		$destFile = clean_path("{$argv[3]}/$file");
		if(file_exists($srcFile)){
			if(!is_dir(dirname($destFile))) {
				mkdir_recursive(dirname($destFile)); // make sure the directory exists
			}
			copy_recursive($srcFile,$destFile);
			$_GET['TEMPLATE_PATH'] = $destFile;
			$_GET['CONVERT_FILE_ONLY'] = true;
			if(!class_exists('TemplateConverter')){
				include($argv[7].'templateConverter.php');
			}else{
				TemplateConverter::convertFile($_GET['TEMPLATE_PATH']);
			}


		}
	}
}



function threeWayMerge(){
	//using threeway merge apis
}

////	END UTILITIES THAT MUST BE LOCAL :(
///////////////////////////////////////////////////////////////////////////////


// only run from command line
if(isset($_SERVER['HTTP_USER_AGENT'])) {
	fwrite(STDERR,'This utility may only be run from the command line or command prompt.');
	exit(1);
}
//Clean_string cleans out any file  passed in as a parameter
$_SERVER['PHP_SELF'] = 'silentUpgrade.php';


///////////////////////////////////////////////////////////////////////////////
////	USAGE
$usage_dce =<<<eoq1
Usage: php.exe -f silentUpgrade.php [upgradeZipFile] [logFile] [pathToSugarInstance]

On Command Prompt Change directory to where silentUpgrade.php resides. Then type path to
php.exe followed by -f silentUpgrade.php and the arguments.

Example:
    [path-to-PHP/]php.exe -f silentUpgrade.php [path-to-upgrade-package/]SugarEnt-Upgrade-4.5.1-to-5.0.0b.zip [path-to-log-file/]silentupgrade.log  [path-to-sugar-instance/]Sugar451e
                             [Old Template path] [skipdbupgrade] [exitOrContinue]

Arguments:
    New Template Path or Upgrade Package : Upgrade package name. Template2 (upgrade to)location.
    silentupgrade.log                    : Silent Upgarde log file.
    Sugar451e/DCE                        : Sugar or DCE Instance instance being upgraded.
    Old Template path                    : Template1 (upgrade from) Instance is being upgraded.
    skipDBupgrade                        : If set to Yes then silentupgrade will only upgrade files. Default is No.
    exitOnConflicts                      : If set to No and conflicts are found then Upgrade continues. Default Yes.
    pathToDCEClient                      : This is path to to DCEClient directory

eoq1;

$usage_regular =<<<eoq2
Usage: php.exe -f silentUpgrade.php [upgradeZipFile] [logFile] [pathToSugarInstance] [admin-user]

On Command Prompt Change directory to where silentUpgrade.php resides. Then type path to
php.exe followed by -f silentUpgrade.php and the arguments.

Example:
    [path-to-PHP/]php.exe -f silentUpgrade.php [path-to-upgrade-package/]SugarEnt-Upgrade-5.2.0-to-5.5.0.zip [path-to-log-file/]silentupgrade.log  [path-to-sugar-instance/] admin

Arguments:
    upgradeZipFile                       : Upgrade package file.
    logFile                              : Silent Upgarde log file.
    pathToSugarInstance                  : Sugar Instance instance being upgraded.
    admin-user                           : admin user performing the upgrade
eoq2;
////	END USAGE
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////	STANDARD REQUIRED SUGAR INCLUDES AND PRESETS
if(!defined('sugarEntry')) define('sugarEntry', true);

$_SESSION = array();
$_SESSION['schema_change'] = 'sugar'; // we force-run all SQL
$_SESSION['silent_upgrade'] = true;
$_SESSION['step'] = 'silent'; // flag to NOT try redirect to 4.5.x upgrade wizard

$_REQUEST = array();
$_REQUEST['addTaskReminder'] = 'remind';


define('SUGARCRM_INSTALL', 'SugarCRM_Install');
define('DCE_INSTANCE', 'DCE_Instance');

global $cwd;
$cwd = getcwd(); // default to current, assumed to be in a valid SugarCRM root dir.

$upgradeType = verifyArguments($argv,$usage_dce,$usage_regular);

///////////////////////////////////////////////////////////////////////////////
//////  Verify that all the arguments are appropriately placed////////////////

///////////////////////////////////////////////////////////////////////////////
////	PREP LOCALLY USED PASSED-IN VARS & CONSTANTS
//$GLOBALS['log']	= LoggerManager::getLogger('SugarCRM');
//require_once('/var/www/html/eddy/sugarnode/SugarTemplateUtilities.php');

$path			= $argv[2]; // custom log file, if blank will use ./upgradeWizard.log
//$db				= &DBManagerFactory::getInstance();  //<---------


//$UWstrings		= return_module_language('en_us', 'UpgradeWizard');
//$adminStrings	= return_module_language('en_us', 'Administration');
//$mod_strings	= array_merge($adminStrings, $UWstrings);
$subdirs		= array('full', 'langpack', 'module', 'patch', 'theme', 'temp');

//$_REQUEST['zip_from_dir'] = $zip_from_dir;

define('SUGARCRM_PRE_INSTALL_FILE', 'scripts/pre_install.php');
define('SUGARCRM_POST_INSTALL_FILE', 'scripts/post_install.php');
define('SUGARCRM_PRE_UNINSTALL_FILE', 'scripts/pre_uninstall.php');
define('SUGARCRM_POST_UNINSTALL_FILE', 'scripts/post_uninstall.php');



echo "\n";
echo "********************************************************************\n";
echo "***************This Upgrade process may take sometime***************\n";
echo "********************************************************************\n";
echo "\n";

global $sugar_config;
$isDCEInstance = false;
$errors = array();


if($upgradeType != constant('DCE_INSTANCE')) {

	ini_set('error_reporting',1);
	require_once('include/entryPoint.php');
	require_once('include/SugarLogger/SugarLogger.php');
	require_once('include/utils/zip_utils.php');
	
	
	
	require('config.php');
	//require_once('modules/UpgradeWizard/uw_utils.php'); // must upgrade UW first
	if(isset($argv[3])) {
		if(is_dir($argv[3])) {
			$cwd = $argv[3];
			chdir($cwd);
		}
	}

	require_once("{$cwd}/sugar_version.php"); // provides $sugar_version & $sugar_flavor

    $GLOBALS['log']	= LoggerManager::getLogger('SugarCRM');
	$patchName		= basename($argv[1]);
	$zip_from_dir	= substr($patchName, 0, strlen($patchName) - 4); // patch folder name (minus ".zip")
	$path			= $argv[2]; // custom log file, if blank will use ./upgradeWizard.log

	if($sugar_version < '5.1.0'){
		$db				= &DBManager :: getInstance();
	}
	else{
		$db				= &DBManagerFactory::getInstance();
	}
	$UWstrings		= return_module_language('en_us', 'UpgradeWizard');
	$adminStrings	= return_module_language('en_us', 'Administration');
	$mod_strings	= array_merge($adminStrings, $UWstrings);
	$subdirs		= array('full', 'langpack', 'module', 'patch', 'theme', 'temp');
	global $unzip_dir;
    $license_accepted = false;
    if(isset($argv[5]) && (strtolower($argv[5])=='yes' || strtolower($argv[5])=='y')){
    	$license_accepted = true;
	 }
	//////////////////////////////////////////////////////////////////////////////
	//Adding admin user to the silent upgrade

	$current_user = new User();
	if(isset($argv[4])) {
	   //if being used for internal upgrades avoid admin user verification
	   $user_name = $argv[4];
	   $q = "select id from users where user_name = '" . $user_name . "' and is_admin=1";
	   $result = $GLOBALS['db']->query($q, false);
	   $logged_user = $GLOBALS['db']->fetchByAssoc($result);
	   if(isset($logged_user['id']) && $logged_user['id'] != null){
		//do nothing
	    $current_user->retrieve($logged_user['id']);
	   }
	   else{
	   	echo "FAILURE: Not an admin user in users table. Please provide an admin user\n";
		exit(1);
	   }
	}
	else {
		echo "*******************************************************************************\n";
		echo "*** ERROR: 4th parameter must be a valid admin user.\n";
		echo $usage;
		echo "FAILURE\n";
		exit(1);
	}


		/////retrieve admin user
	global $sugar_config;
	$configOptions = $sugar_config['dbconfig'];


///////////////////////////////////////////////////////////////////////////////
////	UPGRADE PREP
prepSystemForUpgradeSilent();


$unzip_dir = clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp");
$install_file = clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/patch/".basename($argv[1]));

$_SESSION['unzip_dir'] = $unzip_dir;
$_SESSION['install_file'] = $install_file;
$_SESSION['zip_from_dir'] = $zip_from_dir;
if(is_dir($unzip_dir.'/scripts'))
{
	rmdir_recursive($unzip_dir.'/scripts');
}
if(is_file($unzip_dir.'/manifest.php'))
{
	rmdir_recursive($unzip_dir.'/manifest.php');
}
mkdir_recursive($unzip_dir);
if(!is_dir($unzip_dir)) {
	fwrite(STDERR,"\n{$unzip_dir} is not an available directory\nFAILURE\n");
	exit(1);
}

unzip($argv[1], $unzip_dir);
// mimic standard UW by copy patch zip to appropriate dir
copy($argv[1], $install_file);
////	END UPGRADE PREP
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	UPGRADE UPGRADEWIZARD

$zipBasePath = clean_path("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/{$zip_from_dir}");
$uwFiles = findAllFiles(clean_path("{$zipBasePath}/modules/UpgradeWizard"), array());
$destFiles = array();

foreach($uwFiles as $uwFile) {
	$destFile = clean_path(str_replace($zipBasePath, $cwd, $uwFile));
	copy($uwFile, $destFile);
}
require_once('modules/UpgradeWizard/uw_utils.php'); // must upgrade UW first
removeSilentUpgradeVarsCache(); // Clear the silent upgrade vars - Note: Any calls to these functions within this file are removed here
logThis("*** SILENT UPGRADE INITIATED.", $path);
logThis("*** UpgradeWizard Upgraded  ", $path);

if(function_exists('set_upgrade_vars')){
	set_upgrade_vars();
}

if($configOptions['db_type'] == 'mysql'){
	//Change the db wait_timeout for this session
	$que ="select @@wait_timeout";
	$result = $db->query($que);
	$tb = $db->fetchByAssoc($result);
	logThis('Wait Timeout before change ***** '.$tb['@@wait_timeout'] , $path);
	$query ="set wait_timeout=28800";
	$db->query($query);
	$result = $db->query($que);
	$ta = $db->fetchByAssoc($result);
	logThis('Wait Timeout after change ***** '.$ta['@@wait_timeout'] , $path);
}

////	END UPGRADE UPGRADEWIZARD
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	MAKE SURE PATCH IS COMPATIBLE
if(is_file("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/manifest.php")) {
	// provides $manifest array
	include("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/manifest.php");
	if(!isset($manifest)) {
		fwrite(STDERR,"\nThe patch did not contain a proper manifest.php file.  Cannot continue.\n\n");
	    exit(1);
	} else {
		copy("{$cwd}/{$sugar_config['upload_dir']}upgrades/temp/manifest.php", "{$cwd}/{$sugar_config['upload_dir']}upgrades/patch/{$zip_from_dir}-manifest.php");

		$error = validate_manifest($manifest);
		if(!empty($error)) {
			$error = strip_tags(br2nl($error));
			fwrite(STDERR,"\n{$error}\n\nFAILURE\n");
			exit(1);
		}
	}
} else {
	fwrite(STDERR,"\nThe patch did not contain a proper manifest.php file.  Cannot continue.\n\n");
	exit(1);
}

$ce_to_pro_ent = isset($manifest['name']) && ($manifest['name'] == 'SugarCE to SugarPro' || $manifest['name'] == 'SugarCE to SugarEnt');
$_SESSION['upgrade_from_flavor'] = $manifest['name'];	

global $sugar_config;
global $sugar_version;
global $sugar_flavor;

////	END MAKE SURE PATCH IS COMPATIBLE
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////	RUN SILENT UPGRADE
ob_start();
set_time_limit(0);
if(file_exists('ModuleInstall/PackageManager/PackageManagerDisplay.php')) {
	require_once('ModuleInstall/PackageManager/PackageManagerDisplay.php');
}

	$parserFiles = array();

if(file_exists(clean_path("{$zipBasePath}/include/SugarFields"))) {
	$parserFiles = findAllFiles(clean_path("{$zipBasePath}/include/SugarFields"), $parserFiles);
}
 //$cwd = clean_path(getcwd());
foreach($parserFiles as $file) {
	$srcFile = clean_path($file);
	//$targetFile = clean_path(getcwd() . '/' . $srcFile);
    if (strpos($srcFile,".svn") !== false) {
	  //do nothing
    }
    else{
    $targetFile = str_replace(clean_path($zipBasePath), $cwd, $srcFile);

	if(!is_dir(dirname($targetFile))) {
		mkdir_recursive(dirname($targetFile)); // make sure the directory exists
	}

	if(!file_exists($targetFile))
	 {
		//logThis('Copying file to destination: ' . $targetFile, $path);
		if(!copy($srcFile, $targetFile)) {
			logThis('*** ERROR: could not copy file: ' . $targetFile, $path);
		} else {
			$copiedFiles[] = $targetFile;
		}
	} else {
		//logThis('Skipping file: ' . $targetFile, $path);
		//$skippedFiles[] = $targetFile;
	}
   }
 }
	//copy minimum required files including sugar_file_utils.php
	if(file_exists("{$zipBasePath}/include/utils/sugar_file_utils.php")){
		$destFile = clean_path(str_replace($zipBasePath, $cwd, "{$zipBasePath}/include/utils/sugar_file_utils.php"));
		copy("{$zipBasePath}/include/utils/sugar_file_utils.php", $destFile);
	}
	if(file_exists('include/utils/sugar_file_utils.php')){
    	require_once('include/utils/sugar_file_utils.php');
    }

/*
$errors = preflightCheck();
if((count($errors) == 1)) { // only diffs
	logThis('file preflight check passed successfully.', $path);
}
else{
	fwrite(STDERR,"\nThe user doesn't have sufficient permissions to write to database'.\n\n");
	exit(1);
}
*/
//If version less than 500 then look for modules to be upgraded
if(function_exists('set_upgrade_vars')){
	set_upgrade_vars();
}
//Initialize the session variables. If upgrade_progress.php is already created
//look for session vars there and restore them
if(function_exists('initialize_session_vars')){
	initialize_session_vars();
}

if(!didThisStepRunBefore('preflight')){
	set_upgrade_progress('preflight','in_progress');
	//Quickcreatedefs on the basis of editviewdefs
    if(substr($sugar_version,0,1) >= 5){
    	updateQuickCreateDefs();
	}
	set_upgrade_progress('preflight','done');
}
////////////////COMMIT PROCESS BEGINS///////////////////////////////////////////////////////////////
////	MAKE BACKUPS OF TARGET FILES

if(!didThisStepRunBefore('commit')){
	set_upgrade_progress('commit','in_progress','commit','in_progress');
	if(!didThisStepRunBefore('commit','commitMakeBackupFiles')){
		set_upgrade_progress('commit','in_progress','commitMakeBackupFiles','in_progress');
		$errors = commitMakeBackupFiles($rest_dir, $install_file, $unzip_dir, $zip_from_dir, array());
		set_upgrade_progress('commit','in_progress','commitMakeBackupFiles','done');
	}

	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE PREINSTALL SCRIPTS
	if(empty($errors)) {
		$file = "{$unzip_dir}/".constant('SUGARCRM_PRE_INSTALL_FILE');

		if(is_file($file)) {
			include($file);
			if(!didThisStepRunBefore('commit','pre_install')){
				set_upgrade_progress('commit','in_progress','pre_install','in_progress');
				pre_install();
				set_upgrade_progress('commit','in_progress','pre_install','done');
			}
		}
	}

	//Clean smarty from cache
	if(is_dir($GLOBALS['sugar_config']['cache_dir'].'smarty')){
		$allModFiles = array();
		$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'smarty',$allModFiles);
	   foreach($allModFiles as $file){
	       	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
	       	if(file_exists($file)){
				unlink($file);
	       	}
	   }
	}

		//Also add the three-way merge here. The idea is after the 451 html files have
		//been converted run the 3-way merge. If 500 then just run the 3-way merge
		if(file_exists('modules/UpgradeWizard/SugarMerge/SugarMerge.php')){
		    set_upgrade_progress('end','in_progress','threewaymerge','in_progress');
		    require_once('modules/UpgradeWizard/SugarMerge/SugarMerge.php');
		    $merger = new SugarMerge($zipBasePath);
		    $merger->mergeAll();
		    set_upgrade_progress('end','in_progress','threewaymerge','done');
		}
	///////////////////////////////////////////////////////////////////////////////
	////	COPY NEW FILES INTO TARGET INSTANCE

     if(!didThisStepRunBefore('commit','commitCopyNewFiles')){
			set_upgrade_progress('commit','in_progress','commitCopyNewFiles','in_progress');
			$split = commitCopyNewFiles($unzip_dir, $zip_from_dir);
	 		$copiedFiles = $split['copiedFiles'];
	 		$skippedFiles = $split['skippedFiles'];
			set_upgrade_progress('commit','in_progress','commitCopyNewFiles','done');
	 }
	require_once(clean_path($unzip_dir.'/scripts/upgrade_utils.php')); 
	$new_sugar_version = getUpgradeVersion();
    $origVersion = substr(preg_replace("/[^0-9]/", "", $sugar_version),0,3);
    $destVersion = substr(preg_replace("/[^0-9]/", "", $new_sugar_version),0,3);
    $siv_varset_1 = setSilentUpgradeVar('origVersion', $origVersion);
    $siv_varset_2 = setSilentUpgradeVar('destVersion', $destVersion);
    $siv_write    = writeSilentUpgradeVars();
    if(!$siv_varset_1 || !$siv_varset_2 || !$siv_write){
        logThis("Error with silent upgrade variables: origVersion write success is ({$siv_varset_1}) ".
        		"-- destVersion write success is ({$siv_varset_2}) -- ".
        		"writeSilentUpgradeVars success is ({$siv_write}) -- ".
        		"path to cache dir is ({$GLOBALS['sugar_config']['cache_dir']})", $path);
    }
     require_once('modules/DynamicFields/templates/Fields/TemplateText.php');
	///////////////////////////////////////////////////////////////////////////////
    ///    RELOAD NEW DEFINITIONS
    global $ACLActions, $beanList, $beanFiles;
    include('modules/ACLActions/actiondefs.php');
    include('include/modules.php'); 
	/////////////////////////////////////////////

    if (!function_exists("inDeveloperMode")) { 
        //this function was introduced from tokyo in the file include/utils.php, so when upgrading from 5.1x and 5.2x we should declare the this function 
        function inDeveloperMode()
        {
            return isset($GLOBALS['sugar_config']['developerMode']) && $GLOBALS['sugar_config']['developerMode'];
        }
    }
	///////////////////////////////////////////////////////////////////////////////
	////	HANDLE POSTINSTALL SCRIPTS
	if(empty($errors)) {
		logThis('Starting post_install()...', $path);

		$trackerManager = TrackerManager::getInstance();   
        $trackerManager->pause();
        $trackerManager->unsetMonitors();

		if(!didThisStepRunBefore('commit','post_install')){
			$file = "$unzip_dir/" . constant('SUGARCRM_POST_INSTALL_FILE');
			if(is_file($file)) {
				//set_upgrade_progress('commit','in_progress','post_install','in_progress');
				$progArray['post_install']='in_progress';
				post_install_progress($progArray,'set');
				    global $moduleList;
					include($file);
					post_install();
				// cn: only run conversion if admin selects "Sugar runs SQL"
				if(!empty($_SESSION['allTables']) && $_SESSION['schema_change'] == 'sugar')
					executeConvertTablesSql($db->dbType, $_SESSION['allTables']);
				//set process to done
				$progArray['post_install']='done';
				//set_upgrade_progress('commit','in_progress','post_install','done');
				post_install_progress($progArray,'set');
			}
		}
	    //clean vardefs
		logThis('Performing UWrebuild()...', $path);
		ob_start();
			@UWrebuild();
		ob_end_clean();
		logThis('UWrebuild() done.', $path);

		logThis('begin check default permissions .', $path);
	    	checkConfigForPermissions();
	    logThis('end check default permissions .', $path);

	    logThis('begin check logger settings .', $path);
	    	checkLoggerSettings();
	    logThis('begin check logger settings .', $path);

	    logThis('begin check resource settings .', $path);
			checkResourceSettings();
		logThis('begin check resource settings .', $path);

        
		require("sugar_version.php");
		require('config.php');
		global $sugar_config;
		
		if($ce_to_pro_ent){
			if(isset($sugar_config['sugarbeet']))
			{
			    //$sugar_config['sugarbeet'] is only set in COMM
			    unset($sugar_config['sugarbeet']); 
			}
		    if(isset($sugar_config['disable_team_access_check']))
			{
			    //$sugar_config['disable_team_access_check'] is a runtime configration, 
			    //no need to write to config.php
			    unset($sugar_config['disable_team_access_check']); 
			}
			if(!merge_passwordsetting($sugar_config, $sugar_version)) {
				logThis('*** ERROR: could not write config.php! - upgrade will fail!', $path);
				$errors[] = 'Could not write config.php!';
			}
			
		}

		logThis('Set default_theme to Sugar', $path);
		$sugar_config['default_theme'] = 'Sugar';
		
		if( !write_array_to_file( "sugar_config", $sugar_config, "config.php" ) ) {
            logThis('*** ERROR: could not write config.php! - upgrade will fail!', $path);
            $errors[] = 'Could not write config.php!';
        }
        
        logThis('Set default_max_tabs to 7', $path);
		$sugar_config['default_max_tabs'] = '7';
		
		if( !write_array_to_file( "sugar_config", $sugar_config, "config.php" ) ) {
            logThis('*** ERROR: could not write config.php! - upgrade will fail!', $path);
            $errors[] = 'Could not write config.php!';
        }
        
		logThis('Upgrade the sugar_version', $path);
		$sugar_config['sugar_version'] = $sugar_version;
		if($destVersion == $origVersion)
			require('config.php');
        if( !write_array_to_file( "sugar_config", $sugar_config, "config.php" ) ) {
            logThis('*** ERROR: could not write config.php! - upgrade will fail!', $path);
            $errors[] = 'Could not write config.php!';
        }
		
		logThis('post_install() done.', $path);
	}

	///////////////////////////////////////////////////////////////////////////////
	////	REGISTER UPGRADE
	if(empty($errors)) {
		logThis('Registering upgrade with UpgradeHistory', $path);
		if(!didThisStepRunBefore('commit','upgradeHistory')){
			set_upgrade_progress('commit','in_progress','upgradeHistory','in_progress');
			$file_action = "copied";
			// if error was encountered, script should have died before now
			$new_upgrade = new UpgradeHistory();
			$new_upgrade->filename = $install_file;
			$new_upgrade->md5sum = md5_file($install_file);
			$new_upgrade->name = $zip_from_dir;
			$new_upgrade->description = $manifest['description'];
			$new_upgrade->type = 'patch';
			$new_upgrade->version = $sugar_version;
			$new_upgrade->status = "installed";
			$new_upgrade->manifest = (!empty($_SESSION['install_manifest']) ? $_SESSION['install_manifest'] : '');

			if($new_upgrade->description == null){
				$new_upgrade->description = "Silent Upgrade was used to upgrade the instance";
			}
			else{
				$new_upgrade->description = $new_upgrade->description." Silent Upgrade was used to upgrade the instance.";
			}
		   $new_upgrade->save();
		   set_upgrade_progress('commit','in_progress','upgradeHistory','done');
		   set_upgrade_progress('commit','done','commit','done');
		}
	  }

	//Clean modules from cache
		if(is_dir($GLOBALS['sugar_config']['cache_dir'].'smarty')){
			$allModFiles = array();
			$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'smarty',$allModFiles);
		   foreach($allModFiles as $file){
		       	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
		       	if(file_exists($file)){
					unlink($file);
		       	}
		   }
		}
   //delete cache/modules before rebuilding the relations
   	//Clean modules from cache
		if(is_dir($GLOBALS['sugar_config']['cache_dir'].'modules')){
			$allModFiles = array();
			$allModFiles = findAllFiles($GLOBALS['sugar_config']['cache_dir'].'modules',$allModFiles);
		   foreach($allModFiles as $file){
		       	//$file_md5_ref = str_replace(clean_path(getcwd()),'',$file);
		       	if(file_exists($file)){
					unlink($file);
		       	}
		   }
		}
		
		//delete cache/themes
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
	ob_start();
	if(!isset($_REQUEST['silent'])){
		$_REQUEST['silent'] = true;
	}
	else if(isset($_REQUEST['silent']) && $_REQUEST['silent'] != true){
		$_REQUEST['silent'] = true;
	}
	 
	logThis('Start rebuild relationships.', $path);
	 	@rebuildRelations();
	logThis('End rebuild relationships.', $path);
	 //logThis('Checking for leads_assigned_user relationship and if not found then create.', $path);
		@createMissingRels();
	 //logThis('Checked for leads_assigned_user relationship.', $path);
	ob_end_clean();	
	//// run fix on dropdown lists that may have been incorrectly named
    //fix_dropdown_list();
}

set_upgrade_progress('end','in_progress','end','in_progress');
/////////////////////////Old Logger settings///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

if(function_exists('deleteCache')){
	set_upgrade_progress('end','in_progress','deleteCache','in_progress');
	@deleteCache();
	set_upgrade_progress('end','in_progress','deleteCache','done');
}

///////////////////////////////////////////////////////////////////////////////
////	HANDLE REMINDERS
if(empty($errors)) {
	commitHandleReminders($skippedFiles, $path);
}

if(file_exists(clean_path(getcwd()).'/original451files')){
	rmdir_recursive(clean_path(getcwd()).'/original451files');
}

require_once('modules/Administration/Administration.php');
$admin = new Administration();
$admin->saveSetting('system','adminwizard',1);

logThis('Upgrading user preferences start .', $path);
if(function_exists('upgradeUserPreferences')){
   upgradeUserPreferences();
}
logThis('Upgrading user preferences finish .', $path);

//BEGIN SUGARCRM flav=pro ONLY
if($ce_to_pro_ent){
	if(function_exists('upgradeDashletsForSalesAndMarketing')){
		logThis('Upgrading tracker dashlets for sales and marketing start .', $path);
		upgradeDashletsForSalesAndMarketing();
		logThis('Upgrading tracker dashlets for sales and marketing start .', $path);
	}
}

fix_report_relationships($path);
//END SUGARCRM flav=pro ONLY

require_once('modules/Administration/upgrade_custom_relationships.php');
upgrade_custom_relationships();

if(isset($_SESSION['upgrade_from_flavor'])){ 

        //check to see if there are any new files that need to be added to systems tab
        //retrieve old modules list
        logThis('check to see if new modules exist',$path);
        $oldModuleList = array();
        $newModuleList = array();
        include($argv[3].'/include/modules.php');
        $oldModuleList = $moduleList;
        include('include/modules.php');
        $newModuleList = $moduleList;

        //include tab controller 
        require_once('modules/MySettings/TabController.php');
        $newTB = new TabController();
        
        //make sure new modules list has a key we can reference directly
        $newModuleList = $newTB->get_key_array($newModuleList);
        $oldModuleList = $newTB->get_key_array($oldModuleList);
        
        //iterate through list and remove commonalities to get new modules
        foreach ($newModuleList as $remove_mod){
            if(in_array($remove_mod, $oldModuleList)){
                unset($newModuleList[$remove_mod]);
            }   
        }

        $must_have_modules= array(
			  'Activities'=>'Activities',
        	  'Calendar'=>'Calendar',
        	  'Reports' => 'Reports',
			  'Quotes' => 'Quotes',
			  'Products' => 'Products',
			  'Forecasts' => 'Forecasts',
			  'Contracts' => 'Contracts',
			  'KBDocuments' => 'KBDocuments'
        );
        $newModuleList = array_merge($newModuleList,$must_have_modules);
        
        //new modules list now has left over modules which are new to this install, so lets add them to the system tabs
        logThis('new modules to add are '.var_export($newModuleList,true),$path);
        
        //grab the existing system tabs
        $tabs = $newTB->get_system_tabs();

        //add the new tabs to the array
        foreach($newModuleList as $nm ){
          $tabs[$nm] = $nm;
        }

        //Set the default order
        $default_order = array(
        	'Home'=>'Home',
        	'Accounts'=>'Accounts',
        	'Contacts'=>'Contacts',
        	'Opportunities'=>'Opportunities',
        	'Activities'=>'Activities',
        	'Reports'=>'Reports',
        	'Documents'=>'Documents'
        );
        $tabs = array_merge($default_order, $tabs);        
        
        //now assign the modules to system tabs 
        $newTB->set_system_tabs($tabs);
        logThis('module tabs updated',$path);
        

}

//Also set the tracker settings if  flavor conversion ce->pro or ce->ent
if(isset($_SESSION['current_db_version']) && isset($_SESSION['target_db_version'])){
	if($_SESSION['current_db_version'] == $_SESSION['target_db_version']){
	    $_REQUEST['upgradeWizard'] = true;
	    ob_start();
			include('include/Smarty/internals/core.write_file.php');
		ob_end_clean();
	 	$db =& DBManagerFactory::getInstance();
		if($ce_to_pro_ent){
	        //Also set license information
	        $admin = new Administration();
			$category = 'license';
			$value = 0;
			$admin->saveSetting($category, 'users', $value);
			$key = array('num_lic_oc','key','expire_date');
			$value = '';
			foreach($key as $k){
				$admin->saveSetting($category, $k, $value);
			}
		}
	}
}

	$phpErrors = ob_get_contents();
	ob_end_clean();
	logThis("**** Potential PHP generated error messages: {$phpErrors}", $path);
	
	if(count($errors) > 0) {
		foreach($errors as $error) {
			logThis("****** SilentUpgrade ERROR: {$error}", $path);
		}
		echo "FAILED\n";
	} 


} else {
//ELSE if DCE_INSTANCE
echo "RUNNING DCE UPGRADE\n";
	
} //END of big if-else block for DCE_INSTANCE



?>
