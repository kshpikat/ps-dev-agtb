<?php 
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
 
require_once('include/nusoap/nusoap.php');


/**
 * @group bug39234
 */
class Bug39234Test extends Sugar_PHPUnit_Framework_TestCase
{
	public $_user = null;
	public $_soapClient = null;
	public $_session = null;
	public $_sessionId = '';
    public $_contactId = '';
    var $c1 = null;
    var $c2 = null;
	var $a1 = null;
	
    /**
     * Create test user
     *
     */
	public function setUp() 
    {
    	$this->_soapClient = new nusoapclient($GLOBALS['sugar_config']['site_url'].'/soap.php',false,false,false,false,false,600,600);
        $this->_setupTestUser();
        
		$beanList = array();
		$beanFiles = array();
		require('include/modules.php');
		$GLOBALS['beanList'] = $beanList;
		$GLOBALS['beanFiles'] = $beanFiles;
        
        $unid = uniqid();
		$time = date('Y-m-d H:i:s');

		$contact = new Contact();
		$contact->id = 'c_'.$unid;
        $contact->first_name = 'testfirst';
        $contact->last_name = 'testlast';
        $contact->email1 = 'fred@rogers.com';
        $contact->new_with_id = true;
        $contact->disable_custom_fields = true;
        $contact->save();
		$this->c1 = $contact;
		
		$account = new Account();
		$account->id = 'a_'.$unid;
        $account->name = 'acctfirst';
        $account->assigned_user_id = 'SugarUser';
        $account->new_with_id = true;
        $account->disable_custom_fields = true;
        $account->save();
        $this->a1 = $account;
        
       $this->c1->load_relationship('accounts');
       $this->c1->accounts->add($this->a1->id);
       
       $contact2 = new Contact();
		$contact2->id = 'c2_'.$unid;
       $contact2->first_name = 'testfirst';
        $contact2->last_name = 'testlast';
        $contact2->email1 = 'fred@rogers.com';
        $contact2->new_with_id = true;
        $contact2->disable_custom_fields = true;
        $contact2->save();
		$this->c2 = $contact2;
    }

    /**
     * Remove anything that was used during this test
     *
     */
    public function tearDown() {
    	global $soap_version_test_accountId, $soap_version_test_opportunityId, $soap_version_test_contactId;
        $this->_tearDownTestUser();
        $this->_user = null;
        $this->_sessionId = '';
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->c1->id}'");
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->c2->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE contact_id= '{$this->c1->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE contact_id= '{$this->c2->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts WHERE id= '{$this->a1->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts WHERE name = 'joe pizza'");
        unset($this->c1);
        unset($this->c2);
        unset($this->a1);
        unset($soap_version_test_accountId);
        unset($soap_version_test_opportunityId);
        unset($soap_version_test_contactId);

		unset($GLOBALS['beanList']);
		unset($GLOBALS['beanFiles']);
    }	
    
    public function testSetEntries() {
    	$this->_login();
		$result = $this->_soapClient->call('set_entries',array('session'=>$this->_sessionId,'module_name' => 'Contacts','name_value_lists' => array(array(array('name'=>'last_name' , 'value'=>$this->c1->last_name), array('name'=>'email1' , 'value'=>$this->c1->email1), array('name'=>'first_name' , 'value'=>$this->c1->first_name), array('name'=>'account_name' , 'value'=>$this->a1->name)))));
		$this->assertTrue(isset($result['ids']));
		$this->assertEquals($result['ids'][0],$this->c1->id);
    } // fn
    
     public function testSetEntries2() {
    	$this->_login();
		$result = $this->_soapClient->call('set_entries',array('session'=>$this->_sessionId,'module_name' => 'Contacts','name_value_lists' => array(array(array('name'=>'last_name' , 'value'=>$this->c2->last_name), array('name'=>'email1' , 'value'=>$this->c2->email1), array('name'=>'first_name' , 'value'=>$this->c2->first_name), array('name'=>'account_name' , 'value'=>'joe pizza')))));
		$this->assertTrue(isset($result['ids']));
		$this->assertNotEquals($result['ids'][0],$this->c1->id);
    } // fn
    
	/**********************************
     * HELPER PUBLIC FUNCTIONS
     **********************************/
    
    /**
     * Attempt to login to the soap server
     *
     * @return $set_entry_result - this should contain an id and error.  The id corresponds
     * to the session_id.
     */
    public function _login(){
		global $current_user;  	
    	$result = $this->_soapClient->call('login',
            array('user_auth' => 
                array('user_name' => $current_user->user_name,
                    'password' => $current_user->user_hash, 
                    'version' => '.01'), 
                'application_name' => 'SoapTest')
            );
        $this->_sessionId = $result['id'];
		return $result;
    }
    
 /**
     * Create a test user
     *
     */
	public function _setupTestUser() {
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $this->_user->status = 'Active';
        $this->_user->is_admin = 1;
        $this->_user->save();
        $GLOBALS['current_user'] = $this->_user;
    }
        
    /**
     * Remove user created for test
     *
     */
	public function _tearDownTestUser() {
       SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
       unset($GLOBALS['current_user']);
    }
	
}
?>