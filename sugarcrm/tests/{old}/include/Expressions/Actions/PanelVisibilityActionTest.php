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

class PanelVisibilityActionTest extends TestCase
{
    /**
     * @var PanelVisibilityAction
     */
    private $action;

    protected function setUp() : void
    {
        $this->action = new PanelVisibilityAction(array('target' => 'a', 'value' => 'b'));
    }

    public function testGetDefinition()
    {
        $this->assertEquals(
            $this->action->getDefinition(),
            array('action' => 'SetPanelVisibility', 'params' => array('target' => 'a', 'value' => 'b'))
        );
    }

    public function testGetJavascriptFire()
    {
        $this->assertEquals($this->action->getJavascriptFire(), "new SUGAR.forms.SetPanelVisibilityAction('a','b')");
    }
}
