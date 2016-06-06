<?php
// FILE SUGARCRM flav=ent ONLY
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

/**
 * Converts array of disabled modules to array of enabled modules.
 */
class SugarUpgradeUpdateTBAConfig extends UpgradeScript
{
    /**
     * Sorting order.
     * @var int
     */
    public $order = 9999;

    /**
     * Script updates config files.
     * @var int
     */
    public $type = self::UPGRADE_CUSTOM;

    public function run()
    {
        if (version_compare($this->from_version, '7.8', '>=')
                && version_compare($this->to_version, '7.8.0.0.RC.4', '>=')) {
            $tbaConfigurator = new TeamBasedACLConfigurator();
            $config = $tbaConfigurator->getConfig();
            if (!empty($config['disabled_modules']) && is_array($config['disabled_modules'])) {
                $actionsList = $tbaConfigurator->getListOfPublicTBAModules();
                $enabledModules = array_values(array_diff($actionsList, $config['disabled_modules']));
                $tbaConfigurator->setForModulesList($enabledModules, true);
                $tbaConfigurator->setForModulesList($config['disabled_modules'], false);
            }
        }
        return;
    }
}
