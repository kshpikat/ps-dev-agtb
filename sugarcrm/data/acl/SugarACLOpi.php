<?php
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
require_once('data/SugarACLStrategy.php');

class SugarACLOpi extends SugarACLStrategy
{
    protected static $syncingViews = array(
        'edit',
        'delete',
    );

    protected static $platformSourceMap = array(
        'base' => 'Sugar',
        'portal' => 'Sugar',
        'mobile' => 'Sugar',
        'opi' => 'Outlook',
        'lpi' => 'LotusNotes'
    );

    /**
     * Check recurring source to determine edit
     * @param string $module
     * @param string $view
     * @param array $context
     * @return bool|void
     */
    public function checkAccess($module, $view, $context)
    {
        $bean = self::loadBean($module, $context);

        // if there is no bean we have nothing to check
        if ($bean === false) {
            return true;
        }

        // if the recurring source is Sugar allow modifications
        if (in_array($view, self::$syncingViews)
            && !empty($bean->recurring_source)
            && !empty($bean->fetched_row['recurring_source'])
            && $bean->recurring_source == 'Sugar'
            && $bean->recurring_source == $bean->fetched_row['recurring_source']) {
            return true;
        }

        $view = SugarACLStrategy::fixUpActionName($view);

        if (in_array($view, self::$syncingViews)
            && isset($_SESSION['platform'])
            && isset(self::$platformSourceMap[$_SESSION['platform']])
            && !empty($bean->recurring_source) && !empty($bean->fetched_row['recurring_source'])
            && $bean->fetched_row['recurring_source'] != self::$platformSourceMap[$_SESSION['platform']]
            && $bean->recurring_source != self::$platformSourceMap[$_SESSION['platform']]) {
            return false;
        }

        return true;
    }

    /**
     * Load bean from context
     * @static
     * @param string $module
     * @param array $context
     * @return SugarBean
     */
    protected static function loadBean($module, $context = array())
    {
        if (isset($context['bean']) && $context['bean'] instanceof SugarBean
            && $context['bean']->module_dir == $module) {
            $bean = $context['bean'];
        } else {
            $bean = false;
        }
        return $bean;
    }

}
