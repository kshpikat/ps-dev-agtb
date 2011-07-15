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

class SugarTestImportUtilitiesTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
     //   SugarTestImportUtilities::removeAllCreatedFiles();
    }

    public function testCanCreateFile()
    {
        $filename = SugarTestImportUtilities::createFile();

        $this->assertTrue(is_file($filename));
        $fp = fopen($filename,"r");
        $i = 0;
        $buffer = '';
        while (!feof($fp)) {
            $columns = $buffer;
            $buffer = fgetcsv($fp, 4096);
            if ( $buffer !== false )
                $i++;
        }
        fclose($fp);
        $this->assertEquals($i,2000);
        $this->assertEquals(count($columns),3);
    }

    public function testCanCreateFileAndSpecifyLines()
    {
        $filename = SugarTestImportUtilities::createFile(1);
        $this->assertTrue(is_file($filename));
        $fp = fopen($filename,"r");
        $i = 0;
        $buffer = '';
        while (!feof($fp)) {
            $buffer = fgetcsv($fp, 4096);
            if ( $buffer !== false ) {
                $i++;
                $columns = $buffer;
            }
        }
        fclose($fp);
        $this->assertEquals(1,$i);
        $this->assertEquals(3, count($columns));
    }

    public function testCanCreateFileAndSpecifyLinesAndColumns()
    {
        $filename = SugarTestImportUtilities::createFile(2,5);

        $this->assertTrue(is_file($filename));
        $fp = fopen($filename,"r");
        $i = 0;
        $buffer = '';
        while (!feof($fp)) {
            $columns = $buffer;
            $buffer = fgetcsv($fp, 4096);
            if ( $buffer !== false )
                $i++;
        }
        fclose($fp);
        $this->assertEquals($i,2);
        $this->assertEquals(count($columns),5);
    }

    public function testCanRemoveAllCreatedFiles()
    {
        $filesCreated = array();

        for ($i = 0; $i < 5; $i++)
            $filesCreated[] = SugarTestImportUtilities::createFile();
        $filesCreated[] = $filesCreated[4].'-0';

        SugarTestImportUtilities::removeAllCreatedFiles();

        foreach ( $filesCreated as $filename )
            $this->assertFalse(is_file($filename));
    }
}

