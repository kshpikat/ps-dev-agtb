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

require_once 'include/OutboundEmail/OutboundEmail.php';

use Sugarcrm\Sugarcrm\Util\Uuid;

/**
 * @coversDefaultClass OutboundEmail
 */
class OutboundEmailTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        SugarTestHelper::setUp('current_user');
        OutboundEmailConfigurationTestHelper::setUp();
    }

    protected function tearDown()
    {
        OutboundEmailConfigurationTestHelper::tearDown();
        SugarTestHelper::tearDown();
        parent::tearDown();
    }

    /**
     * @covers ::save
     */
    public function testSave()
    {
        $bean = BeanFactory::newBean('OutboundEmail');
        $bean->id = Uuid::uuid1();
        $bean->new_with_id = true;
        $bean->name = 'test outbound account';
        $bean->mail_smtpserver = 'smtp.sugarcrm.com';
        $bean->mail_smtpuser = 'sugarcrm';
        $bean->mail_smtppass = 'foobar';
        $bean->email_address_id = Uuid::uuid1();
        $id = $bean->save();

        $this->assertSame($bean->id, $id, 'OutboundEmail save returned abnormally');

        $userId = $GLOBALS['db']->getOne("SELECT user_id FROM outbound_email WHERE id='{$bean->id}'");
        $this->assertSame($GLOBALS['current_user']->id, $userId, 'Should be owned by the current user');
    }

    /**
     * @covers ::saveSystem
     */
    public function testSaveSystem()
    {
        $bean = OutboundEmailConfigurationTestHelper::getSystemConfiguration();
        $bean->name = 'a new name';

        // Change the port to prove that the system-override configurations are updated.
        $bean->mail_smtpport = $bean->mail_smtpport + 5;

        // Change the email address.
        $address = SugarTestEmailAddressUtilities::createEmailAddress();
        $bean->email_address = $address->email_address;
        $bean->email_address_id = $address->id;

        $bean->saveSystem(true);
        $admin = Administration::getSettings('notify');

        // Saving dirties the bean so it actually references the system-override configuration. Reload the system
        // configuration before asserting.
        $system = BeanFactory::newBean('OutboundEmail');
        $system->getSystemMailerSettings(false);

        $this->assertSame(
            $system->name,
            $admin->settings['notify_fromname'],
            'The from name config should have been updated'
        );
        $this->assertSame(
            $system->email_address,
            $admin->settings['notify_fromaddress'],
            'The from email config should have been updated'
        );

        // Check the admin's system-override configuration. If the admin's configuration was updated, then all user's
        // configurations were updated.
        $override = $system->getUsersMailerForSystemOverride('1');
        $this->assertEquals($system->mail_smtpport, $override->mail_smtpport, 'The port should have been updated');
    }

    /**
     * @covers ::createUserSystemOverrideAccount
     * @covers ::populateFromUser
     */
    public function testCreateUserSystemOverrideAccount()
    {
        $userData = $GLOBALS['current_user']->getUsersNameAndEmail();
        $emailAddressId = $GLOBALS['current_user']->emailAddress->getGuid($userData['email']);
        $system = OutboundEmailConfigurationTestHelper::getSystemConfiguration();

        $bean = BeanFactory::newBean('OutboundEmail');
        $override = $bean->createUserSystemOverrideAccount($GLOBALS['current_user']->id, 'sraymer', 'hh%Ty7Ui6p');

        $this->assertNotEquals($system->id, $override->id, 'The IDs should not match');
        $this->assertSame($userData['name'], $override->name, 'The names should match');
        $this->assertSame('system-override', $override->type, 'The types should match');
        $this->assertSame($GLOBALS['current_user']->id, $override->user_id, 'The current user should be the owner');
        $this->assertSame($userData['email'], $override->email_address, 'The email addresses should match');
        $this->assertSame($emailAddressId, $override->email_address_id, 'The email address IDs should match');
        $this->assertSame($system->mail_sendtype, $override->mail_sendtype, 'The send types should match');
        $this->assertSame($system->mail_smtptype, $override->mail_smtptype, 'The SMTP types should match');
        $this->assertSame($system->mail_smtpserver, $override->mail_smtpserver, 'The servers should match');
        $this->assertEquals($system->mail_smtpport, $override->mail_smtpport, 'The ports should match');
        $this->assertSame('sraymer', $override->mail_smtpuser, 'The usernames should match');
        $this->assertNotEquals($system->mail_smtppass, $override->mail_smtppass, 'The passwords should not match');
        $this->assertEquals(
            $system->mail_smtpauth_req,
            $override->mail_smtpauth_req,
            'The auth requirements should match'
        );
        $this->assertSame($system->mail_smtpssl, $override->mail_smtpssl, 'The security settings should match');
        $this->assertEquals(0, $override->deleted, 'Should not be deleted');
    }

    /**
     * @covers ::mark_deleted
     */
    public function testMarkDeleted()
    {
        $bean = $this->createPartialMock('OutboundEmail', ['delete']);
        $bean->method('delete')->willReturn(true);
        $bean->id = Uuid::uuid1();

        $actual = $bean->mark_deleted(Uuid::uuid1());
        $this->assertFalse($actual, 'Should return false when trying to delete a different instance');

        $actual = $bean->mark_deleted($bean->id);
        $this->assertTrue($actual, 'Should return true when deleting the instance');
    }

    /**
     * @covers ::delete
     */
    public function testDelete()
    {
        $bean = BeanFactory::newBean('OutboundEmail');

        $actual = $bean->delete();
        $this->assertFalse($actual, 'Should return false when trying to delete an instance without an ID');

        // Now create a record that can be deleted.
        $bean->name = 'test outbound account';
        $bean->mail_smtpserver = 'smtp.sugarcrm.com';
        $bean->mail_smtpuser = 'sugarcrm';
        $bean->mail_smtppass = 'foobar';
        $bean->save();

        $actual = $bean->delete();
        $this->assertTrue($actual, 'Should return true when deleting the instance');

        $actual = $GLOBALS['db']->getOne("SELECT COUNT(id) FROM outbound_email WHERE id='{$bean->id}'");
        $this->assertEquals(0, $actual, 'Should have deleted the specified row');
    }
}
