<?php
//FILE SUGARCRM flav=pro ONLY
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2011 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/



require_once 'include/SugarSearchEngine/Elastic/SugarSearchEngineElastic.php';

class SugarSearchEngineElasticTest extends Sugar_PHPUnit_Framework_TestCase
{

    public $bean;

    public function setUp()
    {
        // create a Bean..doesn't need to be saved
        $this->bean = BeanFactory::newBean('Accounts');
        $this->bean->id = create_guid();
        $this->bean->name = 'Test';
        $this->bean->assigned_user_id = create_guid();
    }

    public function providerQueryStringData()
    {
        return array(
            array('abc', true),
            array('abc def', true),
            array("abc[10 TO 20]", false),
            array('{10 TO 20}abc', false),
            array('"abc"', false),
            array('abc~', false),
            array('accounts:abc', true),
            array('abc*', false),
            );
    }

    /**
     * @dataProvider providerQueryStringData
     */
    public function testCanAppendWildcard($queryString, $canAppend)
    {
        $queryString = html_entity_decode($queryString);

        $stub = new SugarSearchEngineElasticTestStub();
        $result = $stub->canAppendWildcard($queryString);

        $this->assertEquals($canAppend, $result, 'Expect different value from canAppendWildcard()');
    }

    public function testCreateIndexDocument() {
        $stub = new SugarSearchEngineElasticTestStub();
        $document = $stub->createIndexDocument($this->bean);
        $data = $document->getData();
        $this->assertEquals(str_replace('-','', strval($this->bean->assigned_user_id)), $data['doc_owner']);
        
    }


    public function mappingSearchableTypeProvider()
    {
        return array(
            array('name', true),
            array('varchar', true),
            array('phone', true),
            array('enum', false),
            array('iframe', false),
            array('bool', false),
            array('invalid', false),
        );
    }

    /**
     * @dataProvider mappingSearchableTypeProvider
     */
    public function testSearchableType($type, $searchable)
    {
        $ret = SugarSearchEngineFactory::getInstance('Elastic')->isTypeFtsEnabled($type);
        $this->assertEquals($searchable, $ret, 'field type incorrect searchable definition');
    }

}


class SugarSearchEngineElasticTestStub extends SugarSearchEngineElastic
{
    // to test protected function
    public function canAppendWildcard($queryString)
    {
        return parent::canAppendWildcard($queryString);
    }
}