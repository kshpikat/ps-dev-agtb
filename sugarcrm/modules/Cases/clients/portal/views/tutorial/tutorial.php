<?php
//FILE SUGARCRM flav=ent
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
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
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$viewdefs['Cases']['portal']['view']['tutorial'] = array(
    'records' => array(
        'version' => 1,
        'intro' => 'LBL_PORTAL_TOUR_RECORDS_INTRO',
        'content' => array(
            array(
                'text' => 'LBL_PORTAL_TOUR_RECORDS_PAGE',
            ),
            array(
                'name' => '.dataTables_filter',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_FILTER',
                'full' => true,
            ),
            array(
                'name' => '.dataTables_filter',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_FILTER_EXAMPLE',
                'full' => true,
            ),
            array(
                'name' => '.btn-primary[name="create_button"]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_CREATE',
                'full' => true,
            ),
            array(
                'name' => '.routeLink.[data-route="#Cases"]',
                'text' => 'LBL_PORTAL_TOUR_RECORDS_RETURN',
                'full' => true,
            ),
        )
    ),
);
