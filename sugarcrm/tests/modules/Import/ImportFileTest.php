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
 
require_once('modules/Import/sources/ImportFile.php');

class ImportFileTest extends Sugar_PHPUnit_Framework_TestCase
{
	public function setUp()
	{
    	$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
    }
    
    public function tearDown()
    {
        SugarTestImportUtilities::removeAllCreatedFiles();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    /**
 	 * @ticket 23380
 	 */
	public function testFileImportNoEnclosers()
    {
    	$file = SugarTestImportUtilities::createFile(2,1);
    	$importFile = new ImportFile($file,',','', TRUE, FALSE);
        $row = $importFile->getNextRow();
        $this->assertEquals($row, array('foo00'));
        $row = $importFile->getNextRow();
        $this->assertEquals($row,array('foo10'));
    }
    
    public function testLoadNonExistantFile()
    {
        $importFile = new ImportFile($GLOBALS['sugar_config']['import_dir'].'/thisfileisntthere'.date("YmdHis").'.csv',',','"');
        $this->assertFalse($importFile->fileExists());
    }
    
    public function testLoadGoodFile()
    {
        $file = SugarTestImportUtilities::createFile(2,1);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        $this->assertTrue($importFile->fileExists());
    }
    
    /**
     * @ticket 39494
     */
    public function testLoadFileWithByteOrderMark()
    {
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/Bug39494ImportFile.txt';
        copy('tests/modules/Import/Bug39494ImportFile.txt', $sample_file);
        $importFile = new ImportFile($sample_file,"\t",'',false);
        $this->assertTrue($importFile->fileExists());
        $row = $importFile->getNextRow();
        $this->assertEquals($row,array('name','city'));
        $row = $importFile->getNextRow();
        $this->assertEquals($row,array('tester1','wuhan'));
        unlink($sample_file);
    }
    
    public function testGetNextRow()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $row = $importFile->getNextRow();
        $this->assertEquals(array("foo00","foo01"),$row);
        $row = $importFile->getNextRow();
        $this->assertEquals(array("foo10","foo11"),$row);
        $row = $importFile->getNextRow();
        $this->assertEquals(array("foo20","foo21"),$row);
    }
    
    /**
 	 * @ticket 41361
 	 */
    public function testGetNextRowWithEOL()
    {
        $file = SugarTestImportUtilities::createFileWithEOL(1, 1);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        $row = $importFile->getNextRow();
        // both \r\n and \n should be properly replaced with PHP_EOL
        $this->assertEquals(array("start0".PHP_EOL."0".PHP_EOL."end"), $row);
    }
    
    public function testLoadEmptyFile()
    {
        $emptyFile = $GLOBALS['sugar_config']['import_dir'].'/empty'.date("YmdHis").'.csv';
        file_put_contents($emptyFile,'');
        
        $importFile = new ImportFile($emptyFile,',','"',false);
        
        $this->assertFalse($importFile->getNextRow());
        
        $importFile = new ImportFile($emptyFile,',','',false);
        
        $this->assertFalse($importFile->getNextRow());
        
        @unlink($emptyFile);
    }
    
    public function testDeleteFileOnDestroy()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"',true, FALSE);
        
        unset($importFile);
        
        $this->assertFalse(is_file($file));
    }
    
    public function testNotDeleteFileOnDestroy()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"',false);
        
        unset($importFile);
        
        $this->assertTrue(is_file($file));
    }
    
    public function testGetFieldCount()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"',TRUE, FALSE);
        
        $importFile->getNextRow();
        $this->assertEquals(2,$importFile->getFieldCount());
    }
    
    public function testMarkRowAsDuplicate()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $row = $importFile->getNextRow();
        $importFile->markRowAsDuplicate();
        
        $fp = sugar_fopen(ImportCacheFiles::getDuplicateFileName(),'r');
        $duperow = fgetcsv($fp);
        fclose($fp);
        
        $this->assertEquals($row,$duperow);
    }
    
    public function testWriteError()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $row = $importFile->getNextRow();
        $importFile->writeError('Some Error','field1','foo');
        
        $fp = sugar_fopen(ImportCacheFiles::getErrorFileName(),'r');
        $errorrow = fgetcsv($fp);
        fclose($fp);
        
        $this->assertEquals(array('Some Error','field1','foo',1),$errorrow);
        
        $fp = sugar_fopen(ImportCacheFiles::getErrorRecordsWithoutErrorFileName(),'r');
        $errorrecordrow = fgetcsv($fp);
        fclose($fp);
        
        $this->assertEquals($row,$errorrecordrow);
    }
    
    public function testWriteErrorRecord()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $row = $importFile->getNextRow();
        $importFile->writeErrorRecord();
        
        $fp = sugar_fopen(ImportCacheFiles::getErrorRecordsWithoutErrorFileName(),'r');
        $errorrecordrow = fgetcsv($fp);
        fclose($fp);
        
        $this->assertEquals($row,$errorrecordrow);
    }
    
    public function testWriteStatus()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $importFile->getNextRow();
        $importFile->writeError('Some Error','field1','foo');
        $importFile->getNextRow();
        $importFile->markRowAsDuplicate();
        $importFile->getNextRow();
        $importFile->markRowAsImported();
        $importFile->writeStatus();
        
        $fp = sugar_fopen(ImportCacheFiles::getStatusFileName(),'r');
        $statusrow = fgetcsv($fp);
        fclose($fp);
        
        $this->assertEquals(array(3,1,1,1,0,$file),$statusrow);
    }
    
    public function testWriteStatusWithTwoErrorsInOneRow()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $row = $importFile->getNextRow();
        $importFile->writeError('Some Error','field1','foo');
        $importFile->writeError('Some Error','field1','foo');
        $importFile->getNextRow();
        $importFile->markRowAsImported();
        $importFile->getNextRow();
        $importFile->markRowAsImported();
        $importFile->writeStatus();
        
        $fp = sugar_fopen(ImportCacheFiles::getStatusFileName(),'r');
        $statusrow = fgetcsv($fp);
        fclose($fp);
        
        $this->assertEquals(array(3,1,0,2,0,$file),$statusrow);
        
        $fp = sugar_fopen(ImportCacheFiles::getErrorRecordsWithoutErrorFileName(),'r');
        $errorrecordrow = fgetcsv($fp);

        $this->assertEquals($row,$errorrecordrow);

        $this->assertFalse(fgetcsv($fp),'Should be only 1 record in the csv file');
        fclose($fp);
        
    }
    
    public function testWriteStatusWithTwoUpdatedRecords()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"', TRUE, FALSE);
        
        $row = $importFile->getNextRow();
        $importFile->markRowAsImported(false);
        $importFile->getNextRow();
        $importFile->markRowAsImported();
        $importFile->getNextRow();
        $importFile->markRowAsImported();
        $importFile->writeStatus();
        
        $fp = sugar_fopen(ImportCacheFiles::getStatusFileName(),'r');
        $statusrow = fgetcsv($fp);
        fclose($fp);

        $this->assertEquals(array(3,0,0,2,1,$file),$statusrow);
    }
    
    public function testWriteRowToLastImport()
    {
        $file = SugarTestImportUtilities::createFile(3,2);
        $importFile = new ImportFile($file,',','"');
        $record = $importFile->writeRowToLastImport("Tests","Test","TestRunner");
        
        $query = "SELECT * 
                        FROM users_last_import
                        WHERE assigned_user_id = '{$GLOBALS['current_user']->id}'
                            AND import_module = 'Tests'
                            AND bean_type = 'Test'
                            AND bean_id = 'TestRunner'
                            AND id = '$record'
                            AND deleted=0";

		$result = $GLOBALS['db']->query($query);
        
        $this->assertNotNull($GLOBALS['db']->fetchByAssoc($result));
        
        $query = "DELETE FROM users_last_import
                        WHERE assigned_user_id = '{$GLOBALS['current_user']->id}'
                            AND import_module = 'Tests'
                            AND bean_type = 'Test'
                            AND bean_id = 'TestRunner'
                            AND id = '$record'
                            AND deleted=0";
        $GLOBALS['db']->query($query);
    }

    public function providerEncodingData()
    {
        return array(
            array('TestCharset.csv', 'UTF-8'),
            array('TestCharset2.csv', 'ISO-8859-1'),
            );
    }

    /**
     * @dataProvider providerEncodingData
     */
    public function testCharsetDetection($file, $encoding) {
        // create the test file
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/'.$file;
        copy('tests/modules/Import/'.$file, $sample_file);

        // auto detect charset
        $importFile = new ImportFile($sample_file, ",", '', false, false);
        $this->assertTrue($importFile->fileExists());
        $charset = $importFile->autoDetectCharacterSet();
        $this->assertEquals($encoding, $charset, 'detected char encoding is incorrect.');

        // cleanup
        unlink($sample_file);
    }

    public function providerRowCountData()
    {
        return array(
            array('TestCharset.csv', 2, false),
            array('TestCharset2.csv', 11, true),
            array('TestCharset2.csv', 12, false),
            );
    }

    /**
     * @dataProvider providerRowCountData
     */
    public function testRowCount($file, $count, $hasHeader) {
        // create the test file
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/'.$file;
        copy('tests/modules/Import/'.$file, $sample_file);

        $importFile = new ImportFile($sample_file, ",", '', false, false);
        $this->assertTrue($importFile->fileExists());
        $importFile->setHeaderRow($hasHeader);
        $c = $importFile->getTotalRecordCount();
        $this->assertEquals($count, $c, 'incorrect row count.');

        // cleanup
        unlink($sample_file);
    }

    public function providerFieldCountData()
    {
        return array(
            array('TestCharset.csv', 2),
            array('TestCharset2.csv', 5),
            );
    }

    /**
     * @dataProvider providerFieldCountData
     */
    public function testFieldCount($file, $count) {
        // create the test file
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/'.$file;
        copy('tests/modules/Import/'.$file, $sample_file);

        $importFile = new ImportFile($sample_file, ",", '"', false, false);
        $this->assertTrue($importFile->fileExists());
        $c = $importFile->getNextRow();
        $c = $importFile->getFieldCount();
        $this->assertEquals($count, $c, 'incorrect row count.');

        // cleanup
        unlink($sample_file);
    }

    public function providerLineCountData()
    {
        return array(
            array('TestCharset.csv', 2),
            array('TestCharset2.csv', 12),
            );
    }

    /**
     * @dataProvider providerLineCountData
     */
    public function testLineCount($file, $count) {
        // create the test file
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/'.$file;
        copy('tests/modules/Import/'.$file, $sample_file);

        $importFile = new ImportFile($sample_file, ",", '"', false, false);
        $this->assertTrue($importFile->fileExists());
        $c = $importFile->getNumberOfLinesInfile();
        $this->assertEquals($count, $c, 'incorrect row count.');

        // cleanup
        unlink($sample_file);
    }

    public function providerDateFormatData()
    {
        return array(
            array('TestCharset.csv', 'd/m/Y'),
            array('TestCharset2.csv', 'm/d/Y'),
            );
    }

    /**
     * @dataProvider providerDateFormatData
     */
    public function testDateFormat($file, $format) {
        // create the test file
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/'.$file;
        copy('tests/modules/Import/'.$file, $sample_file);

        $importFile = new ImportFile($sample_file, ",", '"', false, false);
        $this->assertTrue($importFile->fileExists());
        $ret = $importFile->autoDetectCSVProperties();
        $this->assertTrue($ret, 'Failed to auto detect properties.');
        $c = $importFile->getDateFormat();
        $this->assertEquals($format, $c, 'incorrect date format.');

        // cleanup
        unlink($sample_file);
    }

    public function providerTimeFormatData()
    {
        return array(
            array('TestCharset.csv', 'h:ia'),
            array('TestCharset2.csv', 'H:i'),
            );
    }

    /**
     * @dataProvider providerTimeFormatData
     */
    public function testTimeFormat($file, $format) {
        // create the test file
        $sample_file = $GLOBALS['sugar_config']['upload_dir'].'/'.$file;
        copy('tests/modules/Import/'.$file, $sample_file);

        $importFile = new ImportFile($sample_file, ",", '"', false, false);
        $this->assertTrue($importFile->fileExists());
        $ret = $importFile->autoDetectCSVProperties();
        $this->assertTrue($ret, 'Failed to auto detect properties.');
        $c = $importFile->getTimeFormat();
        $this->assertEquals($format, $c, 'incorrect time format.');

        // cleanup
        unlink($sample_file);
    }
}
