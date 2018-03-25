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

use PHPUnit\Framework\TestCase;

class BugCRYS697ParserLabelTest extends TestCase
{
    private $lang = 'en_us';
    private $testModule = 'Accounts';

    protected function setUp()
    {
        SugarTestHelper::setUp('files');
        SugarTestHelper::saveFile($this->getFileName());
        if (file_exists($this->getFileName())) {
            unlink($this->getFileName());
        }
        parent::setUp();
    }

    protected function tearDown()
    {
        SugarTestHelper::tearDown();
        parent::tearDown();
    }

    private function getFileName()
    {
        return "custom/modules/{$this->testModule}/Ext/Language/{$this->lang}.lang.ext.php";
    }

    public function testSavingEmptyLabels()
    {
        ParserLabel::addLabels($this->lang, array(), $this->testModule);
        $this->assertFalse(file_exists($this->getFileName()));
    }
}
