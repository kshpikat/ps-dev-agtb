<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


/**
 * NoBlankFieldUpdateOnFirstSyncTest.php
 *
 * This unit test was written to test an Outlook Plugin Hotfix.  It is attempting to mimic
 * what would happen if a new Contact record was created in Sugar.  Then a record with the same
 * first and last name and a matching email was created in Outlook.  With the Outlook settings
 * set so that the Sugar server wins on conflicts, what was happening was that the new (blank) values
 * from the Outlook plugin were overriding the SugarCRM record values. Under the new test what should
 * happen is that blank values from the Outlook side do NOT wipe out the SugarCRM values on first sync.
 * 
 * @author Collin Lee
 */

require_once('include/nusoap/nusoap.php');
require_once('tests/service/SOAPTestCase.php');

class NoBlankFieldUpdateOnFirstSyncTest extends SOAPTestCase
{
	public $_soapClient = null;
    var $_sessionId;
    var $_resultId;
    var $_resultId2;
    var $c = null;
    var $c2 = null;

	public function setUp()
    {
        global $current_user;
        $this->_soapURL = $GLOBALS['sugar_config']['site_url'].'/soap.php';

        //Clean up any possible contacts not deleted
        $GLOBALS['db']->query("DELETE FROM contacts WHERE first_name = 'NoBlankFieldUpdate' AND last_name = 'OnFirstSyncTest'");

        $current_user = SugarTestUserUtilities::createAnonymousUser();
        $contact = SugarTestContactUtilities::createContact();
        $contact->first_name = 'NoBlankFieldUpdate';
        $contact->last_name = 'OnFirstSyncTest';
        $contact->phone_mobile = '867-5309';
        $contact->email1 = 'noblankfieldupdateonfirstsync@example.com';
        $contact->title = 'Jenny - I Got Your Number';
        $contact->disable_custom_fields = true;
        $contact->save();
		$this->c = $contact;

        $GLOBALS['db']->query("DELETE FROM contacts WHERE first_name = 'Collin' AND last_name = 'Lee'");

        //Manually create a contact entry
        $contact2 = new Contact();
        $contact2->title = 'Jenny - I Got Your Number';
        $contact2->first_name = 'Collin';
        $contact2->last_name = 'Lee';
        $contact2->phone_mobile = '867-5309';
        $contact2->disable_custom_fields = true;
        $contact2->email1 = '';
        $contact2->email2 = '';
        //BEGIN SUGARCRM flav=pro ONLY
        $contact2->team_id = '1';
        $contact2->team_set_id = '1';
        //END SUGARCRM flav=pro ONLY
        $contact2->save();
		$this->c2 = $contact2;
        //DELETE contact_users entries that may have remained
        $GLOBALS['db']->query("DELETE FROM contacts_users WHERE user_id = '{$current_user->id}'");
        parent::setUp();
        $this->useOutputBuffering = false;
    }

    public function tearDown()
    {
        global $current_user;
        SugarTestContactUtilities::removeAllCreatedContacts();
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id in ('{$this->_resultId}', '{$this->_resultId2}')");
        $GLOBALS['db']->query("DELETE FROM contacts_users WHERE user_id = '{$current_user->id}'");
        unset($this->c);
        unset($this->c2);
        parent::tearDown();
    }


    public function testNoBlankFieldUpdateOnFirstSyncTest()
    {
        global $current_user;
        $this->_login();
        $contacts_list=array(
                              'session'=>$this->_sessionId, 'module_name' => 'Contacts',
				              'name_value_lists' => array(
                                        array(
                                            array('name'=>'assigned_user_id' , 'value'=>"{$current_user->id}"),
                                            array('name'=>'first_name' , 'value'=>"{$this->c->first_name}"),
                                            array('name'=>'last_name' , 'value'=>"{$this->c->last_name}"),
                                            array('name'=>'email1' , 'value'=>'noblankfieldupdateonfirstsync@example.com'),
                                            array('name'=>'phone_mobile', 'value'=>''),
                                            array('name'=>'contacts_users_id', 'value'=>"{$current_user->id}"),
                                            array('name'=>'title', 'value'=>''),
                                        )
                              )
                        );

        $result = $this->_soapClient->call('set_entries',$contacts_list);
        $this->_resultId = $result['ids'][0];
        $this->assertEquals($this->c->id, $result['ids'][0], 'Found duplicate');

        $existingContact = new Contact();
        $existingContact->retrieve($this->c->id);

        $this->assertEquals('867-5309', $existingContact->phone_mobile, 'Assert that we have not changed the phone_mobile field from first sync');
        $this->assertEquals('Jenny - I Got Your Number', $existingContact->title, 'Assert that we have not changed the title field from first sync');

        $result = $GLOBALS['db']->getOne("SELECT count(id) AS total FROM contacts WHERE first_name = '{$existingContact->first_name}' AND last_name = '{$existingContact->last_name}'");
        $this->assertEquals(1, $result['total'], 'Assert we only have one Contact with the first and last name');

        //Now sync a second time
        $this->_login();
        $contacts_list=array(
                              'session'=>$this->_sessionId, 'module_name' => 'Contacts',
				              'name_value_lists' => array(
                                        array(
                                            array('name'=>'assigned_user_id' , 'value'=>"{$current_user->id}"),
                                            array('name'=>'first_name' , 'value'=>"{$this->c->first_name}"),
                                            array('name'=>'last_name' , 'value'=>"{$this->c->last_name}"),
                                            array('name'=>'email1' , 'value'=>'noblankfieldupdateonfirstsync@example.com'),
                                            array('name'=>'phone_mobile', 'value'=>'1-800-SUGARCRM'),
                                            array('name'=>'contacts_users_id', 'value'=>"{$current_user->id}"),
                                            array('name'=>'title', 'value'=>''),
                                        )
                              )
                        );

        $result = $this->_soapClient->call('set_entries',$contacts_list);
        $this->_resultId = $result['ids'][0];
        $this->assertEquals($this->c->id, $result['ids'][0], 'Found duplicate');
        
        $existingContact = new Contact();
        $existingContact->retrieve($this->c->id);

        $this->assertEquals('1-800-SUGARCRM', $existingContact->phone_mobile, 'Assert that we have changed the phone_mobile field from second sync');
        $this->assertEquals('', $existingContact->title, 'Assert that we have changed the title field to be (blank) from second sync');
        $result = $GLOBALS['db']->getOne("SELECT count(id) AS total FROM contacts WHERE first_name = '{$existingContact->first_name}' AND last_name = '{$existingContact->last_name}'");
        $this->assertEquals(1, $result['total'], 'Assert we only have one Contact with the first and last name');
    }
    

    public function testNoEmailsFindsDuplicates()
    {
        global $current_user;
        $this->_login();
        $contacts_list=array(
                              'session'=>$this->_sessionId, 'module_name' => 'Contacts',
				              'name_value_lists' => array(
                                        array(
                                            array('name'=>'assigned_user_id' , 'value'=>"{$current_user->id}"),
                                            array('name'=>'first_name' , 'value'=>"{$this->c2->first_name}"),
                                            array('name'=>'last_name' , 'value'=>"{$this->c2->last_name}"),
                                            array('name'=>'email1' , 'value'=>''),
                                            array('name'=>'email2', 'value'=>''),
                                            array('name'=>'phone_mobile', 'value'=>''),
                                            array('name'=>'contacts_users_id', 'value'=>"{$current_user->id}"),
                                            array('name'=>'title', 'value'=>''),
                                        )
                              )
                        );

        $result = $this->_soapClient->call('set_entries',$contacts_list);
        $this->_resultId2 = $result['ids'][0];
        $this->assertEquals($this->c2->id, $result['ids'][0], 'Found duplicate when both records have no email');

        $existingContact = new Contact();
        $existingContact->retrieve($this->c2->id);

        $this->assertEquals('867-5309', $existingContact->phone_mobile, 'Assert that we have not changed the phone_mobile field from first sync');
        $this->assertEquals('Jenny - I Got Your Number', $existingContact->title, 'Assert that we have not changed the title field from first sync');

        $result = $GLOBALS['db']->getOne("SELECT count(id) AS total FROM contacts WHERE first_name = '{$existingContact->first_name}' AND last_name = '{$existingContact->last_name}'");
        $this->assertEquals(1, $result['total'], 'Assert we only have one Contact with the first and last name');

        //Now sync a second time
        $this->_login();
        $contacts_list=array(
                              'session'=>$this->_sessionId, 'module_name' => 'Contacts',
				              'name_value_lists' => array(
                                        array(
                                            array('name'=>'assigned_user_id' , 'value'=>"{$current_user->id}"),
                                            array('name'=>'first_name' , 'value'=>"{$this->c2->first_name}"),
                                            array('name'=>'last_name' , 'value'=>"{$this->c2->last_name}"),
                                            array('name'=>'email1' , 'value'=>''),
                                            array('name'=>'email2', 'value'=>''),
                                            array('name'=>'phone_mobile', 'value'=>'1-800-SUGARCRM'),
                                            array('name'=>'contacts_users_id', 'value'=>"{$current_user->id}"),
                                            array('name'=>'title', 'value'=>''),
                                        )
                              )
                        );

        $result = $this->_soapClient->call('set_entries',$contacts_list);

        $existingContact = new Contact();
        $existingContact->retrieve($this->c2->id);

        $this->assertEquals('1-800-SUGARCRM', $existingContact->phone_mobile, 'Assert that we have changed the phone_mobile field from second sync');
        $this->assertEquals('', $existingContact->title, 'Assert that we have changed the title field to be (blank) from second sync');
        $result = $GLOBALS['db']->getOne("SELECT count(id) AS total FROM contacts WHERE first_name = '{$existingContact->first_name}' AND last_name = '{$existingContact->last_name}'");
        $this->assertEquals(1, $result['total'], 'Assert we only have one Contact with the first and last name');
    }

}

?>