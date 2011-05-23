<?php
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
global $current_user,$admin_group_header;
//users and security.
$admin_option_defs=array();
$admin_option_defs['Users']['user_management']= array('Users','LBL_MANAGE_USERS_TITLE','LBL_MANAGE_USERS','./index.php?module=Users&action=index');
//BEGIN SUGARCRM flav!=sales ONLY
$admin_option_defs['Users']['roles_management']= array('Roles','LBL_MANAGE_ROLES_TITLE','LBL_MANAGE_ROLES','./index.php?module=ACLRoles&action=index');
//END SUGARCRM flav!=sales ONLY
//BEGIN SUGARCRM flav=pro ONLY
$admin_option_defs['Users']['teams_management']= array('Teams','LBL_MANAGE_TEAMS_TITLE','LBL_MANAGE_TEAMS','./index.php?module=Teams&action=index');
//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['password_management']= array('Password','LBL_MANAGE_PASSWORD_TITLE','LBL_MANAGE_PASSWORD','./index.php?module=Administration&action=PasswordManager');
//END SUGARCRM flav!=sales ONLY
require_once 'include/SugarOAuthServer.php';
if(SugarOAuthServer::enabled()) {
    $admin_option_defs['Administration']['oauth']= array('Password','LBL_OAUTH_TITLE','LBL_OAUTH','./index.php?module=OAuthKeys&action=index');
}
$admin_group_header[]= array('LBL_USERS_TITLE','',false,$admin_option_defs, 'LBL_USERS_DESC');

//BEGIN SUGARCRM flav=dce ONLY
//DCE.
$admin_option_defs=array();
$admin_option_defs['dce']['license_management']= array('License','LBL_MANAGE_LICENSE_TITLE','LBL_MANAGE_LICENSE','./index.php?module=Administration&action=LicenseSettings');
$admin_option_defs['dce']['dce_settings']= array('dce_Settings','LBL_DCE_SETTINGS_TITLE','LBL_DCE_SETTINGS','./index.php?module=Configurator&action=DCESettings');
$admin_group_header[]= array('LBL_DCE_TITLE','',false,$admin_option_defs, 'LBL_DCE_DESC');
//END SUGARCRM flav=dce ONLY

//BEGIN SUGARCRM flav!=dce && flav!=sales ONLY
//Sugar Connect
$admin_option_defs=array();
$license_key = 'no_key';

//BEGIN SUGARCRM flav=pro || dep=od ONLY
$admin_option_defs['Administration']['license_management']= array('License','LBL_MANAGE_LICENSE_TITLE','LBL_MANAGE_LICENSE','./index.php?module=Administration&action=LicenseSettings');
$focus = new Administration();
$focus->retrieveSettings();
$license_key = $focus->settings['license_key'];
//END SUGARCRM flav=pro || dep=od ONLY
$admin_option_defs['Administration']['support']= array('Support','LBL_SUPPORT_TITLE','LBL_SUPPORT','./index.php?module=Administration&action=SupportPortal&view=support_portal');
//$admin_option_defs['documentation']= array('OnlineDocumentation','LBL_DOCUMENTATION_TITLE','LBL_DOCUMENTATION','./index.php?module=Administration&action=SupportPortal&view=documentation&help_module=Administration&edition='.$sugar_flavor.'&key='.$server_unique_key.'&language='.$current_language);


$admin_option_defs['Administration']['update'] = array('sugarupdate','LBL_SUGAR_UPDATE_TITLE','LBL_SUGAR_UPDATE','./index.php?module=Administration&action=Updater');
//BEGIN SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['documentation']= array('OnlineDocumentation','LBL_DOCUMENTATION_TITLE','LBL_DOCUMENTATION',
        'javascript:void window.open("index.php?module=Administration&action=SupportPortal&view=documentation&help_module=Administration&edition='.$sugar_flavor.'&key='.$server_unique_key.'&language='.$current_language.'", "helpwin","width=600,height=600,status=0,resizable=1,scrollbars=1,toolbar=0,location=0")');
//END SUGARCRM flav!=sales ONLY
if(!empty($license->settings['license_latest_versions'])){
	$encodedVersions = $license->settings['license_latest_versions'];
	$versions = unserialize(base64_decode( $encodedVersions));
	include('sugar_version.php');
	if(!empty($versions)){
		foreach($versions as $version){
			if($version['version'] > $sugar_version )
			{
				$admin_option_defs['Administration']['update'][] ='red';
				if(!isset($admin_option_defs['Administration']['update']['additional_label']))$admin_option_defs['Administration']['update']['additional_label']= '('.$version['version'].')';

			}
		}
	}
}



$admin_group_header[]= array('LBL_SUGAR_NETWORK_TITLE','',false,$admin_option_defs, 'LBL_SUGAR_NETWORK_DESC');

//END SUGARCRM flav!=dce && flav!=sales ONLY

//BEGIN SUGARCRM flav=int ONLY
global $sugar_config;
if($sugar_config['dbconfig']['db_type'] == 'mysql') {

	$admin_option_defs=array();
	$admin_option_defs['Administration']['er_diagram']= array('Upgrade','LBL_ER_DIAGRAM_SCHEMA','LBL_ER_DIAGRAM','./index.php?module=UpgradeWizard&action=erDiagramUI');
	$admin_option_defs['Administration']['dbScan']= array('Upgrade','LBL_DB_SCAN','LBL_DB_SCAN_PROCESS','./index.php?module=UpgradeWizard&action=dbScanUI');
	$admin_option_defs['Administration']['checkSchema']= array('Upgrade','LBL_CHECK_YOUR_SCHEMA','LBL_CHECK_YOUR_SCHEMA_PROCESS','./index.php?module=Administration&action=checkYourSchemaChange');
	$admin_option_defs['Administration']['dbStats']= array('Upgrade','LBL_DATABASE_STATS','LBL_DATABASE_STATS_PROCESS','./index.php?module=UpgradeWizard&action=dbStats');
	$admin_group_header[]= array('LBL_DATABASE_AND_SCHEMA_TITLE','',false,$admin_option_defs, 'LBL_DATABASE_AND_SCHEMA_DESC');
}
//END SUGARCRM flav=int ONLY

//system.
$admin_option_defs=array();
$admin_option_defs['Administration']['configphp_settings']= array('Administration','LBL_CONFIGURE_SETTINGS_TITLE','LBL_CONFIGURE_SETTINGS','./index.php?module=Configurator&action=EditView');
//BEGIN SUGARCRM flav!=sales ONLY
if(!defined('TEMPLATE_URL')){
	$admin_option_defs['Administration']['upgrade_wizard']= array('Upgrade','LBL_UPGRADE_WIZARD_TITLE','LBL_UPGRADE_WIZARD','./index.php?module=UpgradeWizard&action=index');
}
//END SUGARCRM flav!=sales ONLY

$admin_option_defs['Administration']['locale']= array('Currencies','LBL_MANAGE_LOCALE','LBL_LOCALE','./index.php?module=Administration&action=Locale&view=default');
//BEGIN SUGARCRM flav!=dce && flav!=sales ONLY
$admin_option_defs['Administration']['backup_management']= array('Backups','LBL_BACKUPS_TITLE','LBL_BACKUPS','./index.php?module=Administration&action=Backups');
//END SUGARCRM flav!=dce && flav!=sales ONLY


//BEGIN SUGARCRM flav!=dce ONLY
$admin_option_defs['Administration']['currencies_management']= array('Currencies','LBL_MANAGE_CURRENCIES','LBL_CURRENCY','./index.php?module=Currencies&action=index');
//END SUGARCRM flav!=dce ONLY
//BEGIN SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['repair']= array('Repair','LBL_UPGRADE_TITLE','LBL_UPGRADE','./index.php?module=Administration&action=Upgrade');
//END SUGARCRM flav!=sales ONLY

//BEGIN SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['scheduler'] = array('Schedulers','LBL_SUGAR_SCHEDULER_TITLE','LBL_SUGAR_SCHEDULER','./index.php?module=Schedulers&action=index');
//END SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['diagnostic']= array('Diagnostic','LBL_DIAGNOSTIC_TITLE','LBL_DIAGNOSTIC_DESC','./index.php?module=Administration&action=Diagnostic');

//BEGIN SUGARCRM flav!=sales ONLY
// Theme Enable/Disable
$admin_option_defs['Administration']['theme_settings']=array('icon_AdminThemes','LBL_THEME_SETTINGS','LBL_THEME_SETTINGS_DESC','./index.php?module=Administration&action=ThemeSettings');
//END SUGARCRM flav!=sales ONLY
//BEGIN SUGARCRM flav=pro ONLY
$admin_option_defs['Administration']['tracker_settings']=array('Trackers','LBL_TRACKER_SETTINGS','LBL_TRACKER_SETTINGS_DESC','./index.php?module=Trackers&action=TrackerSettings');
//END SUGARCRM flav=pro ONLY

$admin_option_defs['Administration']['feed_settings']=array('icon_SugarFeed','LBL_SUGARFEED_SETTINGS','LBL_SUGARFEED_SETTINGS_DESC','./index.php?module=SugarFeed&action=AdminSettings');

// Connector Integration
$admin_option_defs['Administration']['connector_settings']=array('icon_Connectors','LBL_CONNECTOR_SETTINGS','LBL_CONNECTOR_SETTINGS_DESC','./index.php?module=Connectors&action=ConnectorSettings');

//BEGIN SUGARCRM flav=pro ONLY
$admin_option_defs['Administration']['sugarpdf']= array('icon_AdminPDF','LBL_SUGARPDF_SETTINGS','LBL_SUGARPDF_SETTINGS_DESC','./index.php?module=Configurator&action=SugarpdfSettings');
//END SUGARCRM flav=pro ONLY

//BEGIN SUGARCRM flav=pro ONLY
// Enable/Disable wireless modules
$admin_option_defs['Administration']['enable_wireless_modules']=array('icon_AdminMobile','LBL_WIRELESS_MODULES_ENABLE','LBL_WIRELESS_MODULES_ENABLE_DESC','./index.php?module=Administration&action=EnableWirelessModules');
//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav!=dce ONLY
//$admin_option_defs['module_loader'] = array('ModuleLoader','LBL_MODULE_LOADER_TITLE','LBL_MODULE_LOADER','./index.php?module=Administration&action=UpgradeWizard&view=module');
//END SUGARCRM flav!=dce ONLY
//BEGIN SUGARCRM flav=ent && flav!=dce ONLY
$admin_option_defs['Administration']['offline_client']= array('OfflineClient','LBL_MANAGE_OFFLINE_CLIENT','LBL_OFFLINE_CLIENT','./index.php?module=Administration&action=ViewOfflineClients');
//END SUGARCRM flav=ent && flav!=dce ONLY



//BEGIN SUGARCRM flav=sales ONLY
$admin_option_defs=array();
$admin_option_defs['Administration']['configphp_settings']= array('Administration','LBL_CONFIGURE_SETTINGS_TITLE','LBL_CONFIGURE_SETTINGS','./index.php?module=Configurator&action=EditView');
$admin_option_defs['Administration']['license_management']= array('License','LBL_MANAGE_LICENSE_TITLE','LBL_MANAGE_LICENSE','./index.php?module=Administration&action=LicenseSettings');
$admin_option_defs['Administration']['mass_Email_config']= array('EmailMan','LBL_MASS_EMAIL_CONFIG_TITLE','LBL_MASS_EMAIL_CONFIG_DESC','./index.php?module=EmailMan&action=config');
$admin_option_defs['Administration']['diagnostic']= array('Diagnostic','LBL_DIAGNOSTIC_TITLE','LBL_DIAGNOSTIC_DESC','./index.php?module=Administration&action=Diagnostic');
$admin_option_defs['Administration']['currencies_management']= array('Currencies','LBL_MANAGE_CURRENCIES','LBL_CURRENCY','./index.php?module=Currencies&action=index');
$admin_option_defs['Administration']['repair']= array('Repair','LBL_UPGRADE_TITLE','LBL_UPGRADE','./index.php?module=Administration&action=repairSelectModule');
$admin_option_defs['Administration']['locale']= array('Currencies','LBL_MANAGE_LOCALE','LBL_LOCALE','./index.php?module=Administration&action=Locale&view=default');
$admin_option_defs['Administration']['feed_settings']=array('icon_SugarFeed','LBL_SUGARFEED_SETTINGS','LBL_SUGARFEED_SETTINGS_DESC','./index.php?module=SugarFeed&action=AdminSettings');
$admin_option_defs['Administration']['connector_settings']=array('icon_Connectors','LBL_CONNECTOR_SETTINGS','LBL_CONNECTOR_SETTINGS_DESC','./index.php?module=Connectors&action=ConnectorSettings');
//END SUGARCRM flav=sales ONLY

$admin_option_defs['Administration']['global_search']=array('icon_SearchForm','LBL_GLOBAL_SEARCH_SETTINGS','LBL_GLOBAL_SEARCH_SETTINGS_DESC','./index.php?module=Administration&action=GlobalSearchSettings');
$admin_option_defs['Administration']['languages']= array('Currencies','LBL_MANAGE_LANGUAGES','LBL_LANGUAGES','./index.php?module=Administration&action=Languages&view=default');

$admin_group_header[]= array('LBL_ADMINISTRATION_HOME_TITLE','',false,$admin_option_defs, 'LBL_ADMINISTRATION_HOME_DESC');


//BEGIN SUGARCRM flav!=sales ONLY
//email manager.
$admin_option_defs=array();
$admin_option_defs['Emails']['mass_Email_config']= array('EmailMan','LBL_MASS_EMAIL_CONFIG_TITLE','LBL_MASS_EMAIL_CONFIG_DESC','./index.php?module=EmailMan&action=config');
//BEGIN SUGARCRM flav!=dce && flav!=sales ONLY
$admin_option_defs['Campaigns']['campaignconfig']= array('Campaigns','LBL_CAMPAIGN_CONFIG_TITLE','LBL_CAMPAIGN_CONFIG_DESC','./index.php?module=EmailMan&action=campaignconfig');
//END SUGARCRM flav!=dce  && flav!=sales ONLY
$admin_option_defs['Emails']['mailboxes']= array('InboundEmail','LBL_MANAGE_MAILBOX','LBL_MAILBOX_DESC','./index.php?module=InboundEmail&action=index');
//BEGIN SUGARCRM flav!=dce && flav!=sales ONLY
$admin_option_defs['Campaigns']['mass_Email']= array('EmailMan','LBL_MASS_EMAIL_MANAGER_TITLE','LBL_MASS_EMAIL_MANAGER_DESC','./index.php?module=EmailMan&action=index');
//END SUGARCRM flav!=dce && flav!=sales ONLY
$admin_group_header[]= array('LBL_EMAIL_TITLE','',false,$admin_option_defs, 'LBL_EMAIL_DESC');
//END SUGARCRM flav!=sales ONLY

//BEGIN SUGARCRM flav!=dce ONLY
//studio.
$admin_option_defs=array();
$admin_option_defs['studio']['studio']= array('Studio','LBL_STUDIO','LBL_STUDIO_DESC','./index.php?module=ModuleBuilder&action=index&type=studio');
//BEGIN SUGARCRM flav!=sales ONLY
if(isset($GLOBALS['beanFiles']['iFrame'])) {
	$admin_option_defs['Administration']['portal']= array('iFrames','LBL_IFRAME','DESC_IFRAME','./index.php?module=iFrames&action=index');
}
//END SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['rename_tabs']= array('RenameTabs','LBL_RENAME_TABS','LBL_CHANGE_NAME_TABS',"./index.php?action=wizard&module=Studio&wizard=StudioWizard&option=RenameTabs");
//BEGIN SUGARCRM flav!=sales ONLY
$admin_option_defs['Administration']['moduleBuilder']= array('ModuleBuilder','LBL_MODULEBUILDER','LBL_MODULEBUILDER_DESC','./index.php?module=ModuleBuilder&action=index&type=mb');
$admin_option_defs['Administration']['configure_tabs']= array('ConfigureTabs','LBL_CONFIGURE_TABS_AND_SUBPANELS','LBL_CONFIGURE_TABS_AND_SUBPANELS_DESC','./index.php?module=Administration&action=ConfigureTabs');
//END SUGARCRM flav!=sales ONLY

$admin_option_defs['Administration']['module_loader'] = array('ModuleLoader','LBL_MODULE_LOADER_TITLE','LBL_MODULE_LOADER','./index.php?module=Administration&action=UpgradeWizard&view=module');
//BEGIN SUGARCRM flav!=sales ONLY
//BEGIN SUGARCRM flav=pro ONLY
$admin_option_defs['Administration']['config_prod_bar']=array('icon_ShortcutBar','LBL_CONFIGURE_SHORTCUT_BAR','LBL_CONFIGURE_SHORTCUT_BAR_DESC','./index.php?module=Administration&action=ConfigureShortcutBar');
//END SUGARCRM flav=pro ONLY
$admin_option_defs['any']['configure_group_tabs']= array('ConfigureTabs','LBL_CONFIGURE_GROUP_TABS','LBL_CONFIGURE_GROUP_TABS_DESC','./index.php?action=wizard&module=Studio&wizard=StudioWizard&option=ConfigureGroupTabs');

$admin_option_defs['any']['dropdowneditor']= array('Dropdown','LBL_DROPDOWN_EDITOR','DESC_DROPDOWN_EDITOR','./index.php?module=ModuleBuilder&action=index&type=dropdowns');
//END SUGARCRM flav!=sales ONLY

//BEGIN SUGARCRM flav=ent ONLY
$admin_option_defs['Administration']['sugarportal']= array('SugarPortal','LBL_SUGARPORTAL','LBL_SUGARPORTAL_DESC','./index.php?module=ModuleBuilder&action=index&type=sugarportal');
//END SUGARCRM flav=ent ONLY

//$admin_option_defs['migrate_custom_fields']= array('MigrateFields','LBL_EXTERNAL_DEV_TITLE','LBL_EXTERNAL_DEV_DESC','./index.php?module=Administration&action=Development');

//BEGIN SUGARCRM flav=pro ONLY
$admin_option_defs['any']['workflow_management']= array('WorkFlow','LBL_MANAGE_WORKFLOW','LBL_WORKFLOW_DESC','./index.php?module=WorkFlow&action=ListView');
//END SUGARCRM flav=pro ONLY

$admin_group_header[]= array('LBL_STUDIO_TITLE','',false,$admin_option_defs, 'LBL_TOOLS_DESC');

//BEGIN SUGARCRM flav=pro ONLY
//product catalog.

$admin_option_defs=array();
$admin_option_defs['Products']['product_catalog']= array('Products','LBL_PRODUCTS_TITLE','LBL_PRODUCTS','./index.php?module=ProductTemplates&action=ListView');
$admin_option_defs['Products']['manufacturers']= array('Manufacturers','LBL_MANUFACTURERS_TITLE','LBL_MANUFACTURERS','./index.php?module=Manufacturers&action=index');
$admin_option_defs['Products']['product_categories']= array('Product_Categories','LBL_PRODUCT_CATEGORIES_TITLE','LBL_PRODUCT_CATEGORIES','./index.php?module=ProductCategories&action=index');
$admin_option_defs['Products']['shipping_providers']= array('Shippers','LBL_SHIPPERS_TITLE','LBL_SHIPPERS','./index.php?module=Shippers&action=index');
$admin_option_defs['Products']['product_types']= array('Product_Types','LBL_PRODUCT_TYPES_TITLE','LBL_PRODUCT_TYPES','./index.php?module=ProductTypes&action=index');

$admin_option_defs['Quotes']['tax_rates']= array('TaxRates','LBL_TAXRATES_TITLE','LBL_TAXRATES','./index.php?module=TaxRates&action=index');

$admin_group_header[]= array('LBL_PRICE_LIST_TITLE','',false,$admin_option_defs, 'LBL_PRICE_LIST_DESC');

//END SUGARCRM flav=pro ONLY
//BEGIN SUGARCRM flav!=sales ONLY
//bug tracker.
$admin_option_defs=array();
$admin_option_defs['Bugs']['bug_tracker']= array('Releases','LBL_MANAGE_RELEASES','LBL_RELEASE','./index.php?module=Releases&action=index');
$admin_group_header[]= array('LBL_BUG_TITLE','',false,$admin_option_defs, 'LBL_BUG_DESC');
//END SUGARCRM flav!=sales ONLY
//BEGIN SUGARCRM flav=pro ONLY
//Forecasting
$admin_option_defs=array();
$admin_option_defs['Forecasts']['timeperiod_management']= array('TimePeriods','LBL_MANAGE_TIMEPERIODS_TITLE','LBL_MANAGE_TIMEPERIODS','./index.php?module=TimePeriods&action=ListView');
$admin_group_header[]= array('LBL_FORECAST_TITLE','',false,$admin_option_defs, 'LBL_FORECAST_DESC');

//Contracts
$admin_option_defs=array();
$admin_option_defs['Contracts']['contract_type_management']= array('Contracts','LBL_MANAGE_CONTRACTEMPLATES_TITLE','LBL_CONTRACT_TYPES','./index.php?module=ContractTypes&action=index');
$admin_group_header[]= array('LBL_CONTRACT_TITLE','',false,$admin_option_defs, 'LBL_CONTRACT_DESC');

//END SUGARCRM flav=pro ONLY

//END SUGARCRM flav!=dce ONLY

if(file_exists('custom/modules/Administration/Ext/Administration/administration.ext.php')){
	include('custom/modules/Administration/Ext/Administration/administration.ext.php');
}

//For users with MLA access we need to find which entries need to be shown.
//lets process the $admin_group_header and apply all the access control rules.
$access = get_admin_modules_for_user($current_user);
foreach ($admin_group_header as $key=>$values) {
	$module_index = array_keys($values[3]);  //get the actual links..
	foreach ($module_index as $mod_key=>$mod_val) {
		if (is_admin($current_user) ||
			in_array($mod_val, $access) ||
		    $mod_val=='studio'||
		    ($mod_val=='Forecasts' && in_array('ForecastSchedule', $access)) ||
		    ($mod_val =='any')
		   ) {
		   	    if(!is_admin($current_user)&& isset($values[3]['Administration'])){
                    unset($values[3]['Administration']);
                }
                if(displayStudioForCurrentUser() == false) {
                    unset($values[3]['studio']);
                }

		   	   //BEGIN SUGARCRM PRO ONLY
                if(displayWorkflowForCurrentUser() == false) {
                    unset($values[3]['any']['workflow_management']);
                }

                // Need this check because Quotes and Products share the header group
                if(!in_array('Quotes', $access)&& isset($values[3]['Quotes'])){
                    unset($values[3]['Quotes']);
                }
                if(!in_array('Products', $access)&& isset($values[3]['Products'])){
                    unset($values[3]['Products']);
                }

                // Need this check because Emails and Campaigns share the header group
                if(!in_array('Campaigns', $access)&& isset($values[3]['Campaigns'])){
                    unset($values[3]['Campaigns']);
                }

                //END SUGARCRM PRO ONLY
                //////////////////

        } else {
        	//hide the link
        	unset($admin_group_header[$key][3][$mod_val]);
        }

	}
}
?>
