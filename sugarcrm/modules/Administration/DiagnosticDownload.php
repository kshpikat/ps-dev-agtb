<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
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
 * by SugarCRM are Copyright (C) 2004-2005 SugarCRM, Inc.; All Rights Reserved.
 * $Id: DiagnosticDownload.php 45203 2009-03-17 17:46:55Z maubert $
 ********************************************************************************/

global $current_user;


if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");

http://localhost:8888/Mango/build/rome/builds/ent/sugarcrm/cache/diagnostic/d9b809a4-c2e3-f93e-eb95-4d9ffe76db13/diagnostic20110408-233534.zip
if(!isset($_REQUEST['guid']) || !isset($_REQUEST['time']))
{
	die('Did not receive a filename to download');
}
$time = str_replace(array('.', '/', '\\'), '', $_REQUEST['time']);
$guid = str_replace(array('.', '/', '\\'), '', $_REQUEST['guid']);
$path = getcwd()."/{$GLOBALS['sugar_config']['cache_dir']}diagnostic/{$guid}/diagnostic{$time}.zip";
$filesize = filesize($path);

header('Content-type: application/zip');
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Disposition: attachment; filename=$guid.zip");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");

readfile($path);


?>
