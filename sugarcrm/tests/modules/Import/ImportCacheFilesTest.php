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
 
require_once 'modules/Import/ImportCacheFiles.php';

class ImportCacheFilesTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
    }
    
    public function tearDown() 
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function testgetDuplicateFileName()
    {
        $filename = ImportCacheFiles::getDuplicateFileName();
        
        $this->assertEquals(
            "{$GLOBALS['sugar_config']['import_dir']}dupes_{$GLOBALS['current_user']->id}.csv", $filename);
    }
    
    public function testgetErrorFileName()
    {
        $filename = ImportCacheFiles::getErrorFileName();
        
        $this->assertEquals(
            "{$GLOBALS['sugar_config']['import_dir']}error_{$GLOBALS['current_user']->id}.csv", $filename);
    }
    
    public function testgetStatusFileName()
    {
        $filename = ImportCacheFiles::getStatusFileName();
        
        $this->assertEquals(
            "{$GLOBALS['sugar_config']['import_dir']}status_{$GLOBALS['current_user']->id}.csv", $filename);
    }
    
    public function testclearCacheFiles()
    {
        // make sure there is a file in there
        file_put_contents(ImportCacheFiles::getStatusFileName(),'foo');
        
        ImportCacheFiles::clearCacheFiles();
        
        $this->assertFalse(is_file(ImportCacheFiles::getStatusFileName()));
    }
}
