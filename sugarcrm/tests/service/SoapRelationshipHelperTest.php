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

/**
 * SoapRelationshipHelperTest.php
 * This test may be used to write tests against the SoapRelationshipHelper.php file and the utility functions found there.
 *
 * @author Collin Lee
 */
require_once('soap/SoapRelationshipHelper.php');
class SoapRelationshipHelperTest extends Sugar_PHPUnit_Framework_TestCase
{

    var $noSoapErrorArray = array('number'=>0, 'name'=>'No Error', 'description'=>'No Error');
    var $callsAndMeetingsSelectFields = array('id', 'date_modified', 'deleted', 'name', 'rt.deleted synced');
    var $tasksSelectFields = array('id', 'date_modified', 'deleted', 'name');
    var $contactsSelectFields =  array('id', 'date_modified', 'deleted', 'first_name', 'last_name', 'rt.deleted synced', "(SELECT email_addresses.email_address FROM contacts LEFT JOIN  email_addr_bean_rel on contacts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module='Contacts' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address=1 LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id Where contacts.ID = m1.ID) email1","(SELECT email_addresses.email_address FROM contacts LEFT JOIN  email_addr_bean_rel on contacts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module='Contacts' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address!=1 LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id Where contacts.ID = m1.ID Limit 1) email2");
    var $meeting;
    var $call;
    var $contact;
    var $task;
    var $nowTime;
    var $tenMinutesLaterTime;
    var $testData;

    public function setUp()
    {
        global $timedate, $current_user;
        $timedate = TimeDate::getInstance();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        $current_user = SugarTestUserUtilities::createAnonymousUser();
        $this->nowTime = $timedate->asDb($timedate->getNow()->get("-10 minutes"));
        $this->tenMinutesLaterTime = $timedate->asDb($timedate->getNow()->get("+10 minutes"));
        $current_user->is_admin = 1;
        $current_user->save();
        $this->meeting = SugarTestMeetingUtilities::createMeeting();
        $this->meeting->team_id = $current_user->team_id;
        $this->meeting->team_set_id = $current_user->team_set_id;
        $this->meeting->team_id = $current_user->team_id;
        $this->meeting->team_set_id = $current_user->team_set_id;
        $this->meeting->assigned_user_id = $current_user->id;
        $this->meeting->save();
        $this->meeting->load_relationship('users');
        $this->meeting->users->add($current_user);
        $this->call = SugarTestCallUtilities::createCall();
        $this->call->team_id = $current_user->team_id;
        $this->call->team_set_id = $current_user->team_set_id;
        $this->call->assigned_user_id = $current_user->id;
        $this->call->save();
        $this->call->load_relationships('users');
        $this->call->users->add($current_user);
        $this->contact = SugarTestContactUtilities::createContact();
        $this->contact->email1 = 'mark.zuckerberg@facebook.com';
        $this->contact->contacts_users_id = $current_user->id;
        $this->contact->load_relationship('user_sync');
        $this->contact->user_sync->add($current_user);
        $this->contact->sync_contact = 1;
        $this->contact->save();
        $this->task = SugarTestTaskUtilities::createTask();
        $this->task->assigned_user_id = $current_user->id;
        $this->task->team_id = $current_user->id;
        $this->task->team_set_id = $current_user->id;
        $this->task->save();

        //$this->useOutputBuffering = false;
        /**
         * This provider returns an Array of Array data.  Each Array contains the following data
         * 0 => String - Left side module name
         * 1 => String - Right side module name
         * 2 => String - Relationship Query
         * 3 => boolean to return deleted records or not (this is actually ignored by the function)
         * 4 => integer offset to start with
         * 5 => integer value for the maximum number of results
         * 6 => array of fields to select and return
         * 7 => load_relationships - Relationship name to use
         * 8 => array of expected results
         * 9 => integer of expected total count
         * 10 => array of expected soap error
         * @return array The provider array
         */
        $this->testData = array(
            array('Users', 'Meetings', "( (m1.date_modified > '{$this->nowTime}' AND m1.date_modified <= '{$this->tenMinutesLaterTime}' AND m1.deleted = 0) OR (m1.date_modified > '{$this->nowTime}' AND m1.date_modified <= '{$this->tenMinutesLaterTime}' AND m1.deleted = 1) AND m1.id IN ('{$this->meeting->id}')) OR (m1.id NOT IN ('{$this->meeting->id}') AND m1.deleted = 0) AND m2.id = '{$current_user->id}'", 0, 0 , 3000, $this->callsAndMeetingsSelectFields, 'meetings_users', array('id'=>$this->meeting->id), 1, $this->noSoapErrorArray),
            array('Users', 'Calls', "( m1.deleted = 0) AND m2.id = '{$current_user->id}'",0,0,3000,$this->callsAndMeetingsSelectFields, 'calls_users', array('id'=>$this->call->id), 1, $this->noSoapErrorArray),
            array('Users', 'Contacts', "( (m1.date_modified > '{$this->nowTime}' AND m1.date_modified <= '{$this->tenMinutesLaterTime}' AND {0}.deleted = 0) OR ({0}.date_modified > '{$this->nowTime}' AND {0}.date_modified <= '{$this->tenMinutesLaterTime}' AND {0}.deleted = 1) AND m1.id IN ('31a219bd-b9c1-2c3e-aa5d-4f2778ab0347','c794bc39-e4fb-f515-f1d5-4f285ca88965','d51a0555-8f84-9e62-0fbc-4f2787b5d839','a1219ae6-5a6b-0d1b-c49f-4f2854bc2912')) OR (m1.id NOT IN ('31a219bd-b9c1-2c3e-aa5d-4f2778ab0347','c794bc39-e4fb-f515-f1d5-4f285ca88965','d51a0555-8f84-9e62-0fbc-4f2787b5d839','a1219ae6-5a6b-0d1b-c49f-4f2854bc2912') AND {0}.deleted = 0) AND m2.id = '1'", 0, 0 , 3000, $this->contactsSelectFields, 'contacts_users', array('id'=>$this->contact->id, 'email1'=>'mark.zuckerberg@facebook.com'), 1, $this->noSoapErrorArray),
            array('Users', 'Tasks', " ( (m1.date_modified > '{$this->nowTime}' AND m1.date_modified <= '{$this->tenMinutesLaterTime}' AND {0}.deleted = 0) OR ({0}.date_modified > '{$this->nowTime}' AND {0}.date_modified <= '{$this->tenMinutesLaterTime}' AND {0}.deleted = 1) AND m1.id IN ('{$this->task->id}')) OR (m1.id NOT IN ('{$this->task->id}') AND {0}.deleted = 0) AND m2.id = '1'", 0, 0, 3000, $this->tasksSelectFields, 'tasks_assigned_user', array('id'=>$this->task->id), 1, $this->noSoapErrorArray),
        );
    }

    public function tearDown()
    {
        global $current_user;
        $GLOBALS['db']->query("DELETE FROM meetings_users WHERE user_id = '{$current_user->id}'");
        $GLOBALS['db']->query("DELETE FROM calls_users WHERE user_id = '{$current_user->id}'");
        $GLOBALS['db']->query("DELETE FROM contacts_users WHERE user_id = '{$current_user->id}'");
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
        SugarTestCallUtilities::removeAllCreatedCalls();
        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestTaskUtilities::removeAllCreatedTasks();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['beanFiles']);
        unset($GLOBALS['beanList']);
    }


    /**
     * testRetrieveModifiedRelationships
     * This test checks to make sure we can correctly retrieve related Meetings and Calls (see bugs 50092 & 50093)
     */
    public function testRetrieveModifiedRelationships()
    {
        /*
        if($GLOBALS['db']->dbType != 'mysql' ) {
            $this->markTestIncomplete("Currently these queries don't work on non-mysql DBs, skip until query is fixed.");
        }
        */
        foreach($this->testData as $data)
        {
            //retrieve_modified_relationships($module_name, $related_module, $relationship_query, $show_deleted, $offset, $max_results, $select_fields = array(), $relationship_name = '')

            $result = retrieve_modified_relationships($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]);
            $this->assertEquals($data[8]['id'], $result['result'][0]['id'], 'Ids do not match');
            $this->assertEquals($data[9], $result['total_count'], 'Totals do not match');
            $this->assertEquals($data[10], $result['error'], 'No SOAP Error');
        }
    }
}
