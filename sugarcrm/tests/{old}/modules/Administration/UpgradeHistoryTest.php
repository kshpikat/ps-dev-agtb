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

use PHPUnit\Framework\TestCase;

class UpgradeHistoryTest extends TestCase
{
    /**
     * @ticket 44075
     */
    public function testTrackerVisibilityBug44075()
    {
        $uh = new UpgradeHistory();
        $this->assertFalse($uh->tracker_visibility);
    }
}
