<?php
//FILE SUGARCRM flav=pro ONLY
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

require_once('include/api/RestService.php');
require_once('modules/ForecastWorksheets/clients/base/api/ForecastWorksheetsApi.php');
require_once('modules/ForecastWorksheets/clients/base/api/ForecastWorksheetsFilterApi.php');

/***
 * Used to test Forecast Module endpoints from ForecastModuleApi.php
 *
 * @group forecastapi
 * @group forecasts
 */
class ForecastsChartApiTest extends Sugar_PHPUnit_Framework_TestCase
{

    protected static $user;

    /**
     * @var TimePeriod;
     */
    protected static $timeperiod;

    /**
     * @var commit_stage;
     */
    protected static $commit_stage;

    /**
     * @var chartApi
     */
    protected $chartApi;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('app_list_strings');
        SugarTestHelper::setUp('current_user');

        SugarTestForecastUtilities::setUpForecastConfig();
        self::$user = SugarTestForecastUtilities::createForecastUser(array("opportunities" => array("total" => 1, "include_in_forecast" => 1)));

        self::$timeperiod = SugarTestForecastUtilities::getCreatedTimePeriod();    

        self::$commit_stage = 'include';
    }
    
    public function setUp()
    {
        $this->markTestIncomplete("Results are corrupt.  SFA needs to fix.");        
        $this->_user = self::$user['user'];
        $this->chartApi = new ForecastWorksheetsFilterApi();
    }

    public static function tearDownAfterClass()
    {
        SugarTestForecastUtilities::tearDownForecastConfig();
    	SugarTestForecastUtilities::cleanUpCreatedForecastUsers();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        SugarTestOpportunityUtilities::removeAllCreatedOpportunities();
        SugarTestQuotaUtilities::removeAllCreatedQuotas();
        SugarTestTimePeriodUtilities::removeAllCreatedTimePeriods();
        parent::tearDownAfterClass();
        // this strange as we only want to call this when the class expires;
        parent::tearDown();
    }

    public function tearDown()
    {
        $this->chartApi = null;
    }

    /**
     * Utility Method to get the ServiceMock with a valid user in it
     *
     * @param User $user
     * @return ForecastChartApiServiceMock
     */
    protected function _getServiceMock(User $user)
    {
        $serviceApi = new ForecastChartApiServiceMock();
        $serviceApi->user = $user;

        return $serviceApi;
    }

    /**
     * @group forecastapi
     * @group forecasts
     * @group forecastschart
     */
    public function testQuotaIsReturned()
    {
        $args = array(
            'timeperiod_id' => self::$timeperiod->id,
            'user_id' => self::$user['user']->id,
            'display_manager' => false,
            'group_by' => 'sales_stage',
            'dataset' => 'likely',
            'module' => 'ForecastWorksheets'
        );

        $chart = $this->chartApi->forecastWorksheetsChartGet($this->_getServiceMock(self::$user['user']), $args);

        $this->assertEquals(self::$user["quota"]->amount, $chart['values'][0]['goalmarkervalue'][0]);
    }

    /**
     * @dataProvider providerDataSetValueReturned
     * @group forecastapi
     * @group forecasts
     * @group forecastschart
     */
    public function testDataSetValueReturned($key, $dataset)
    {
        $this->markTestIncomplete('Failing. Need to be fixed by SFA team');
        $args = array(
            'timeperiod_id' => self::$timeperiod->id,
            'user_id' => self::$user['user']->id,
            'display_manager' => false,
            'group_by' => 'sales_stage',
            'dataset' => $dataset,
            'module' => 'ForecastWorksheets'
        );

        $chart = $this->chartApi->forecastWorksheetsChartGet($this->_getServiceMock(self::$user['user']), $args);

        $found = false;

        foreach($chart["values"] as $value)
        {
            if($value["goalmarkervalue"][1] != 0.00)
            {
                $this->assertEquals(self::$user["opportunities"][0]->$key, $value["goalmarkervalue"][1]);
                $found = true;
            }
        }
        $this->assertEquals(true, $found, "The chart value was now found in the dataset.");
    }

    /**
     * @return array
     */
    public function providerDataSetValueReturned()
    {
        return array(
            array("best_case", "best"),
            array("amount", "likely"),
            array("worst_case", "worst")
        );
    }
    /**
     * @group forecastapi
     * @group forecasts
     * @group forecastschart
     */
    public function testGoalMarkerLabelSetCorrectly()
    {
        $args = array(
            'timeperiod_id' => self::$timeperiod->id,
            'user_id' => self::$user['user']->id,
            'display_manager' => false,
            'group_by' => 'sales_stage',
            'dataset' => 'likely',
            'module' => 'ForecastWorksheets'
        );

        $chart = $this->chartApi->forecastWorksheetsChartGet($this->_getServiceMock(self::$user['user']), $args);

        $this->assertEquals("Likely Case", $chart['properties'][0]['goal_marker_label'][1]);
    }

    /**
     * @dataProvider providerGroupByReturnTheProperLabelName
     * @group forecastapi
     * @group forecasts
     * @group forecastschart
     */
    public function testGroupByReturnTheProperLabelName($actual, $group_by)
    {
        $this->markTestIncomplete('Failing. Need to be fixed by SFA team');
        $args = array(
            'timeperiod_id' => self::$timeperiod->id,
            'user_id' => self::$user['user']->id,
            'display_manager' => false,
            'group_by' => $group_by,
            'dataset' => 'likely',
            'module' => 'ForecastWorksheets'
        );

        $chart = $this->chartApi->forecastWorksheetsChartGet($this->_getServiceMock(self::$user['user']), $args);

        $this->assertEquals($actual, $chart['properties'][0]['label_name']);
    }

    /**
     * @return array
     */
    public function providerGroupByReturnTheProperLabelName()
    {
        global $current_language;

        $mod_strings = return_module_language($current_language, 'Opportunities');

        return array(
            array(get_label('LBL_SALES_STAGE', $mod_strings), 'sales_stage'),
            array(get_label('LBL_FORECAST', $mod_strings), 'forecast'),
            array(get_label('LBL_PROBABILITY', $mod_strings), 'probability')
        );
    }

    /**
     * @bug 54921
     * @group forecastapi
     * @group forecasts
     * @group forecastschart
     */
    public function testUsersWithNoDataChartContainsUsers()
    {
        $user1 = SugarTestForecastUtilities::createForecastUser(array("createOpportunities" => false,"createQuota" => false,"createForecast" => false,));
        $user2 = SugarTestForecastUtilities::createForecastUser(array("createOpportunities" => false,"createQuota" => false,"createForecast" => false,'user' => array('manager', 'reports_to' => $user1['user']->id)));

        $args = array(
            'timeperiod_id' => self::$timeperiod->id,
            'user_id' => $user1['user']->id,
            'display_manager' => true,
            'group_by' => 'sales_stage',
            'dataset' => 'likely',
            'module' => 'ForecastWorksheets'
        );

        $chart = $this->chartApi->forecastWorksheetsChartGet($this->_getServiceMock($user1['user']), $args);

        $this->assertEquals(3, count($chart['values']));
    }

    /**
     * @bug 55246
     * @group forecastapi
     * @group forecasts
     * @group forecastschart
     */
    public function testNoGroupByReturnsGroupedByForecast()
    {
        $this->markTestIncomplete('Failing. Need to be fixed by SFA team');
        $args = array(
            'timeperiod_id' => self::$timeperiod->id,
            'user_id' => self::$user['user']->id,
            'display_manager' => false,
            'dataset' => 'likely',
            'module' => 'ForecastWorksheets'
        );

        $chart = $this->chartApi->forecastWorksheetsChartGet($this->_getServiceMock(self::$user["user"]), $args);

        $this->assertEquals(ucfirst(self::$commit_stage), $chart['label'][0]);
    }

}

class ForecastChartApiServiceMock extends RestService
{
    public function execute()
    {
    }

    protected function handleException(Exception $exception)
    {
    }
}
