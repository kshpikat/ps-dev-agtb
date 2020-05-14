<?php
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

class S_585_2_HealthCheckScannerCasesTestMock extends HealthCheckScannerCasesTestMock
{
    public $not = true;

    public function getUpgradeHistory()
    {
        return new class {
            public function getInstalledPackagesByType(string $type): array
            {
                $history = new UpgradeHistory();
                $history->filename = 'upload/upgrades/module/DemoPackage.zip';
                return [$history];
            }
        };
    }

    public function getPackageManager()
    {
        return new class {
            public function getinstalledPackages(): array
            {
                return [];
            }
        };
    }
}
