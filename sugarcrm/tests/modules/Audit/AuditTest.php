<?php
//File SUGARCRM flav=pro ONLY
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

require_once 'tests/SugarTestDatabaseMock.php';
require_once 'modules/Audit/Audit.php';

class AuditTest extends Sugar_PHPUnit_Framework_TestCase
{

    protected $bean =null;

    public static $db;

    public static function setUpBeforeClass()
    {
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('beanList');

        self::$db = new SugarTestDatabaseMock();
        self::$db->setUp();
        SugarTestHelper::setUp('current_user');
    }

    public static function tearDownAfterClass()
    {
        self::$db->tearDown();
        SugarTestHelper::tearDown();
    }

    public function setUp()
    {
        parent::setUp();
        SugarTestHelper::setUp('app_strings');
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('moduleList');

        $this->bean = BeanFactory::getBean('Leads');
        $this->bean->name = 'Test';
        $this->bean->id = '1';
    }

    public function tearDown()
    {
        unset($this->bean);
        SugarTestHelper::tearDown();
        parent::tearDown();
    }

    public function testGetAuditLog()
    {
        global $timedate;
        $auditTable = $this->bean->get_audit_table_name();
        $dateCreated = date('Y-m-d H:i:s');
        self::$db->queries['auditQuery'] =  array(
                                    'match' => '/' . $auditTable . '/',
                                    'rows' => array(
                                                    array(
                                                        'field_name' => 'name',
                                                        'date_created' => $dateCreated,
                                                        'before_value_string' => 'Test',
                                                        'after_value_string' => 'Awesome',
                                                        'before_value_text' => '',
                                                        'after_value_text' => '',
                                                        ),
                                                    ),
                                    );
        $audit = BeanFactory::getBean('Audit');
        $data = $audit->getAuditLog($this->bean);
        $dateCreated = $timedate->fromDbType($dateCreated, "datetime");
        $expectedDateCreated = $timedate->asIso($dateCreated);
        $expected = array(
                0 => array(
                    'field_name' => 'name',
                    'date_created' => $expectedDateCreated,
                    'after' => 'Awesome',
                    'before' => 'Test',
                ),
            );

        $this->assertEquals($expected, $data, "Expected Result was incorrect");
    }

    public function testGetAuditLogTranslation()
    {
        global $timedate;
        $auditTable = $this->bean->get_audit_table_name();
        $dateCreated = date('Y-m-d H:i:s');
        self::$db->queries['auditQuery'] =  array(
            'match' => '/' . $auditTable . '/',
            'rows' => array(
                array(
                    'field_name' => 'assigned_user_id',
                    'date_created' => $dateCreated,
                    'before_value_string' => '012345678',
                    'after_value_string' => '876543210',
                    'before_value_text' => '',
                    'after_value_text' => '',
                ),
            ),
        );
        self::$db->queries['translateQuery'] = array(
            'match' => '/012345678/',
            'rows' => array(
                array(
                    'user_name' => 'Jim'
                ),
            ),
        );
        self::$db->queries['translateQuery2'] = array(
            'match' => '/876543210/',
            'rows' => array(
                array(
                    'user_name' => 'Sally'
                ),
            ),
        );
        $audit = BeanFactory::getBean('Audit');
        $data = $audit->getAuditLog($this->bean);
        $dateCreated = $timedate->fromDbType($dateCreated, "datetime");
        $expectedDateCreated = $timedate->asIso($dateCreated);
        $expected = array(
            0 => array(
                'field_name' => 'assigned_user_id',
                'date_created' => $expectedDateCreated,
                'after' => 'Sally',
                'before' => 'Jim',
            ),
        );

        $this->assertEquals($expected, $data, "Expected Result was incorrect");
    }
}
