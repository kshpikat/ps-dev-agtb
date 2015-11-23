<?php
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

namespace Sugarcrm\SugarcrmTestsUnit\Dav\Cal\Hook;

use Sugarcrm\SugarcrmTestsUnit\TestReflection;
use Sugarcrm\Sugarcrm\Dav\Cal\Hook\Handler as LogicHookHandler;

require_once 'tests/SugarTestCalDavUtilites.php';

/**
 * @coversDefaultClass \Sugarcrm\Sugarcrm\Dav\Cal\Hook\Handler
 */

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Sugarcrm\Sugarcrm\Dav\Cal\Hook\Handler::getManager
     */
    public function testGetManager()
    {
        $handlerObject = new LogicHookHandler();
        $manager = TestReflection::callProtectedMethod($handlerObject, 'getManager');
        $this->assertInstanceOf('\Sugarcrm\Sugarcrm\JobQueue\Manager\Manager', $manager);
    }

    /**
     * @covers \Sugarcrm\Sugarcrm\Dav\Cal\Hook\Handler::getAdapterFactory
     */
    public function testGetAdapterFactory()
    {
        $handlerObject = new LogicHookHandler();
        $manager = TestReflection::callProtectedMethod($handlerObject, 'getAdapterFactory');
        $this->assertInstanceOf('\Sugarcrm\Sugarcrm\Dav\Cal\Adapter\Factory', $manager);
    }
}
