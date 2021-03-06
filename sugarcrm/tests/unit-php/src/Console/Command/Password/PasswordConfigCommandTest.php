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

namespace Sugarcrm\SugarcrmTestsUnit\Console\Command\Password;

use Sugarcrm\Sugarcrm\Security\Password\Backend\Native;
use Sugarcrm\Sugarcrm\Security\Password\Backend\Sha2;
use Sugarcrm\Sugarcrm\Security\Password\Hash;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @coversDefaultClass \Sugarcrm\Sugarcrm\Console\Command\Password\PasswordConfigCommand
 */
class PasswordConfigCommandTest extends AbstractPasswordCommandTestCase
{
    /**
     * @covers ::configure
     * @covers ::execute
     * @covers ::showPasswordSettings
     * @covers ::showHashingInfo
     * @covers ::getProtectedValue
     * @dataProvider providerTestExecute
     */
    public function testExecute(array $config, Hash $hash, $output, $exit)
    {
        $cmd = $this->getMockBuilder('Sugarcrm\Sugarcrm\Console\Command\Password\PasswordConfigCommand')
            ->setMethods(['getHashInstance', 'getConfig'])
            ->getMock();

        $cmd->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($config));

        $cmd->expects($this->once())
            ->method('getHashInstance')
            ->will($this->returnValue($hash));

        $tester = new CommandTester($cmd);
        $tester->execute([]);

        $output = self::$fixturePath . $output;
        $this->assertStringEqualsFile($output, $tester->getDisplay(true));
        $this->assertSame($exit, $tester->getStatusCode());
    }

    public function providerTestExecute()
    {
        $hash1 = new Hash(new Native());

        $sha2 = new Sha2();
        $sha2->setAlgo('CRYPT_SHA512');

        $hash2 = new Hash($sha2);
        $hash2->setRehash(false);
        $hash2->setAllowLegacy(true);

        return [

            // OOTB configuration
            [
                [
                    'minpwdlength' => 6,
                    'maxpwdlength' => '',
                    'oneupper' => true,
                    'onelower' => true,
                    'onenumber' => true,
                    'onespecial' => '',
                    'customregex' => '',
                ],
                $hash1,
                'PasswordConfigCommand_0.txt',
                0,
            ],

            // Missing configuration
            [
                [],
                $hash1,
                'PasswordConfigCommand_1.txt',
                0,
            ],

            // SHA2 backend using SHA-512
            [
                [
                    'minpwdlength' => 4,
                    'maxpwdlength' => '8',
                    'oneupper' => false,
                    'onelower' => true,
                    'onenumber' => false,
                    'onespecial' => true,
                    'customregex' => 'foobar',
                ],
                $hash2,
                'PasswordConfigCommand_2.txt',
                0,
            ],
        ];
    }
}
