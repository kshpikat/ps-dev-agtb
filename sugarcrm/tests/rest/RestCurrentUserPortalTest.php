<?php
//FILE SUGARCRM flav=ent ONLY
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

require_once('tests/rest/RestTestPortalBase.php');

class RestCurrentUserPortalTest extends RestTestPortalBase {
    public function setUp()
    {
        global $db;

        parent::setUp();

        // Disable the other portal users
        $this->oldPortal = array();
        $ret = $db->query("SELECT id FROM users WHERE portal_only = '1' AND deleted = '0'");
        while ( $row = $db->fetchByAssoc($ret) ) {
            $this->oldPortal[] = $row['id'];
        }
        $db->query("UPDATE users SET deleted = '1' WHERE portal_only = '1'");

        $this->_user->portal_only = '1';
        $this->_user->save();
        $GLOBALS ['system_config']->saveSetting('supportPortal', 'RegCreatedBy', $this->_user->id);
        // A little bit destructive, but necessary.
        $db->query("DELETE FROM contacts WHERE portal_name = 'unittestportal'");

        $this->accounts = array();
        $this->contacts = array();

        $account = BeanFactory::newBean('Accounts');
        $account->name = "UNIT TEST PortalMe";
        $account->billing_address_postalcode = "90210";
        $account->save();
        $this->accounts[] = $account;

        $this->contact->first_name = "UNITTEST";
        $this->contact->last_name = "PORTALME";
        $this->contact->title = "UNITTEST";

        $this->contact->load_relationship('accounts');
        $this->contact->accounts->add(array($this->accounts[0]));
        
        $this->contact->save();
        
        $GLOBALS['db']->commit();
    }



    public function tearDown()
    {
        global $db;
        // Re-enable the old portal users
        $portalIds = "('".implode("','",$this->oldPortal)."')";
        $db->query("UPDATE users SET deleted = '0' WHERE id IN {$portalIds}");

        $accountIds = array();
        foreach ( $this->accounts as $account ) {
            $accountIds[] = $account->id;
        }
        $accountIds = "('".implode("','",$accountIds)."')";
        $contactIds = array();
        foreach ( $this->contacts as $contact ) {
            $contactIds[] = $contact->id;
        }
        $contactIds = "('".implode("','",$contactIds)."')";

        $GLOBALS['db']->query("DELETE FROM accounts WHERE id IN {$accountIds}");
        if ($GLOBALS['db']->tableExists('accounts_cstm')) {
            $GLOBALS['db']->query("DELETE FROM accounts_cstm WHERE id_c IN {$accountIds}");
        }
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id IN {$contactIds}");
        if ($GLOBALS['db']->tableExists('contacts_cstm')) {
            $GLOBALS['db']->query("DELETE FROM contacts_cstm WHERE id_c IN {$contactIds}");
        }
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE contact_id IN {$contactIds}");

        $GLOBALS ['system_config']->saveSetting('supportPortal', 'RegCreatedBy', '');
        $GLOBALS ['system_config']->saveSetting('portal', 'on', 0);
        $GLOBALS['db']->commit();

        parent::tearDown();
    }

    /**
     * @group rest
     */
    public function testRetrieve() {
        $restReply = $this->_restCall("me");
        $this->assertNotEmpty($restReply['reply']['current_user']['id']);
        $this->assertEquals($this->portalGuy->id,$restReply['reply']['current_user']['id']);
        $this->assertEquals($this->_user->id,$restReply['reply']['current_user']['user_id']);
        $this->assertEquals('support_portal',$restReply['reply']['current_user']['type']);
    }

    /**
     * @group rest
     */
    public function testUpdate() {
        $restReply = $this->_restCall("me", json_encode(array('first_name' => 'UNIT TEST - AFTER')), "PUT");
        $this->assertNotEquals(stripos($restReply['reply']['current_user']['full_name'], 'UNIT TEST - AFTER'), false);
    }

    /**
     * @group rest
     */
    public function testPasswordUpdate() {
        $this->_restLogin();
        // Change password twice to be sure working as expected
        $reply = $this->_restCall("me/password",
            json_encode(array('new_password' => 'fubar', 'old_password' => 'unittest')),
            'PUT');
        
        $this->assertEquals($reply['reply']['valid'], true, "Part One");
        $reply = $this->_restCall("me/password",
            json_encode(array('new_password' => 'newernew', 'old_password' => 'fubar')),
            'PUT');
        $this->assertEquals($reply['reply']['valid'], true, "Part Deux");
        // Now use an incorrect old_password .. this should return valid:false
        $reply = $this->_restCall("me/password",
            json_encode(array('new_password' => 'hello', 'old_password' => 'nope')),
            'PUT');
        $this->assertEquals($reply['reply']['valid'], false, "Part Three - With a Vengence");
    }

    /**
     * @group rest
     */
    public function testPasswordVerification() {
        $reply = $this->_restCall("me/password",
            json_encode(array('password_to_verify' => 'unittest')),
            'POST');
        $this->assertEquals($reply['reply']['valid'], true);
        $reply = $this->_restCall("me/password",
            json_encode(array('password_to_verify' => 'noway')),
            'POST');
        $this->assertEquals($reply['reply']['valid'], false);
    }

}
