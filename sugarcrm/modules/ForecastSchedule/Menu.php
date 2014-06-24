<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point'); 
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/*********************************************************************************
 * $Id: Menu.php 14461 2006-07-07 17:32:59Z ajay $
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

global $mod_strings;
global $current_user;

$module_menu[] = array("index.php?module=Forecasts&action=ListView", $mod_strings['LNK_FORECAST_LIST'],"Forecasts");
$module_menu[] = array("index.php?module=Forecasts&action=index&submodule=Worksheet", $mod_strings['LNK_UPD_FORECAST'],"ForecastWorksheet");
$module_menu[] = array("index.php?module=Quotas&action=index", $mod_strings['LNK_QUOTA'],"ForecastWorksheet");
?>
