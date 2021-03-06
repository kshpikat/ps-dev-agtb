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
$config = array();
$config['excludeFileTypes'] = array('.xls'=>1, '.png'=>1, '.gif'=> 1, '.jpg'=>1, '.swf'=>1, 'README'=>1, '.eot' => 1, '.ttf' => 1, '.svg' => 1, '.svgz' => 1, '.woff' => 1, 'phar'=>1);
$config['excludeFiles'] = array('sugarportal/jscalendar/lang/calendar-hr.js'=>1);
$config['skipBuilds'] = array();
$config['skipDirs'] = array('.AppleDouble' => 1, 'rome' => 1, 'translations' => 1);
$config['registry'] = array('flav' => array());
$config['sugarVariables'] = array(
    '@_SUGAR_VERSION' => '',
    '@_SUGAR_MAR_VERSION' => '',
    '@_SUGAR_FLAV' => '',
    '@_SUGAR_BUILD_NUMBER' => '999',
    '@_SUGAR_BUILD_TIME' => date('Y-m-d g:ia'),
    '@_SUGAR_COPYRIGHT_YEAR' => date('Y'),
);
$config['mergeDirs'] = array('translations'=>'sugarcrm');
$config['replace'] = array();

//Controls whether or not to include the original line numbering (i.e. commented lines appear as newlines)
$config['retainCommentSpacing'] = false;

foreach (new GlobIterator('config/builds/config.*.php') as $path) {
    include $path;
}

foreach($config['skipBuilds'] as $flav=>$x){
	define($flav, $flav);
}

foreach($config['builds'] as $flav=>$info){
		if(!defined($flav))define($flav, $flav);
}
