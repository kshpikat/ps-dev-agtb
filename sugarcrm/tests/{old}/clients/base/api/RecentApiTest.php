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

use Sugarcrm\Sugarcrm\DataPrivacy\Erasure\FieldList;
use PHPUnit\Framework\TestCase;

/**
 * @group ApiTests
 */
class RecentApiTest extends TestCase
{
    /**
     * @var RecentApi
     */
    private $api;

    protected function setUp()
    {
        parent::setUp();
        SugarTestHelper::setUp('current_user');
        SugarTestHelper::setUp('timedate');

        $this->api = new RecentApi();
    }

    protected function tearDown()
    {
        global $current_user;
        global $db;

        SugarTestAccountUtilities::removeAllCreatedAccounts();
        $db->query('DELETE FROM tracker WHERE user_id = ' . $db->quoted($current_user->id));

        SugarTestHelper::tearDown();
        parent::tearDown();
    }

    public function testFilterModules()
    {
        // Employees module is currently handled in a special way, so test it explicitly
        $modules = array('Accounts', 'Employees', 'NonExistingModule');
        $api = new RecentApi();
        $filtered = SugarTestReflection::callProtectedMethod($api, 'filterModules', array($modules));

        $this->assertContains('Accounts', $filtered);
        $this->assertContains('Employees', $filtered);
        $this->assertNotContains('NonExistingModule', $filtered);
    }

    public function testGetRecentlyViewed()
    {
        global $timedate;

        $account = SugarTestAccountUtilities::createAccount();

        $service = SugarTestRestUtilities::getRestServiceMock();
        $this->api->api = $service;

        $date = '2014-01-01 00:00:00';

        $this->trackAction($account, $date);
        $response = $this->api->getRecentlyViewed($service, array(
            'module_list' => $account->module_name,
        ));

        $this->assertCount(1, $response['records'], 'API response should contain exactly one record');
        $record = array_shift($response['records']);
        $this->assertEquals($account->module_name, $record['_module']);
        $this->assertEquals($account->id, $record['id']);

        $lastViewed = $record['_last_viewed_date'];
        $dateTime = $timedate->fromIso($lastViewed);
        $lastViewed = $dateTime->asDb();
        $this->assertEquals($date, $lastViewed);
    }

    /**
     * @test
     */
    public function erasedFields()
    {
        $contact = SugarTestContactUtilities::createContact();
        $contact->erase(FieldList::fromArray(['field_list']), false);

        $service = SugarTestRestUtilities::getRestServiceMock();
        $this->api->api = $service;

        $this->trackAction($contact, '2014-01-01 00:00:00');

        $response = $this->api->getRecentlyViewed($service, array(
            'module_list' => $contact->module_name,
            'erased_fields' => true,
        ));

        $this->assertArraySubset([
            'records' => [
                [
                    '_erased_fields' => ['field_list'],
                ],
            ],
        ], $response);
    }

    private function trackAction(SugarBean $bean, $date)
    {
        global $timedate;

        $dateTime = $timedate->fromDb($date);
        $timedate->setNow($dateTime);

        $this->api->trackAction($bean);
    }
}
