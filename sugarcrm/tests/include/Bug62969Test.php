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


require_once 'include/utils.php';

/**
 * @ticket 62969
 */
class Bug62969Test extends Sugar_PHPUnit_Framework_TestCase
{
    protected $_customDir = 'custom/include/language';
    protected $_customFile = 'en_us.lang.php';
    public function setUp()
    {
        SugarTestHelper::setUp('current_user');
        SugarTestHelper::setUp('beanFiles');
        SugarTestHelper::setUp('beanList');

        // create a custom language file
        $customLangFileContent = <<<EOQ
<?php
\$app_list_strings['parent_type_display']=array (
  'Accounts' => 'Account',
  'Contacts' => 'Contact',
  'Tasks' => 'Task',
  'Opportunities' => 'Opportunity',
  'Products' => 'Product',
  'Quotes' => 'Quote',
  'Bugs' => 'Bug Tracker',
  'Cases' => 'Case',
  'Leads' => 'Lead',
  'Project' => 'Project',
  'ProjectTask' => 'Project Task',
  //'Prospects' => 'Target',
);
EOQ;
        if (!file_exists($this->_customDir)) {
            mkdir($this->_customDir);
        }
        file_put_contents($this->_customDir . '/' . $this->_customFile, $customLangFileContent);

        // add to loader map
        SugarAutoLoader::addToMap($this->_customDir . '/' . $this->_customFile, true);

        // clear cache so it can be reloaded later
        $cache_key = 'app_list_strings.'.$GLOBALS['current_language'];
       	sugar_cache_clear($cache_key);
    }

    public function tearDown()
    {
        // remove the custom language file
        if (file_exists($this->_customDir . '/' . $this->_customFile)) {
            unlink($this->_customDir . '/' . $this->_customFile);
        }

        // delete from loader map
        SugarAutoLoader::delFromMap($this->_customDir . '/' . $this->_customFile, true);

        // clear cache so it can be reloaded later
        $cache_key = 'app_list_strings.'.$GLOBALS['current_language'];
       	sugar_cache_clear($cache_key);

        // reload app_list_strings
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);

        SugarTestHelper::tearDown();
    }

    /*
     * to test that the custom array is used for parent_type_display
     */
    public function testBug62969()
    {
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $this->assertArrayNotHasKey('Prospects', $GLOBALS['app_list_strings']['parent_type_display'], 'Should not have Prospects');
    }
}
