<?php

class Bug44030Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $unified_search_modules_file;
    
    public function setUp() 
    {
    	$this->useOutputBuffering = false;
	    global $beanList, $beanFiles, $dictionary;
	    	
	    //Add entries to simulate custom module
	    $beanList['Bug44030_TestPerson'] = 'Bug44030_TestPerson';
	    $beanFiles['Bug44030_TestPerson'] = 'modules/Bug44030_TestPerson/Bug44030_TestPerson.php';
	    
	    VardefManager::loadVardef('Contacts', 'Contact');
	    $dictionary['Bug44030_TestPerson'] = $dictionary['Contact'];
	    
	    //Copy over custom SearchFields.php file
        if(!file_exists('custom/modules/Bug44030_TestPerson/metadata')) {
       		mkdir_recursive('custom/modules/Bug44030_TestPerson/metadata');
    	}
    
    if( $fh = @fopen('custom/modules/Bug44030_TestPerson/metadata/SearchFields.php', 'w+') )
    {
$string = <<<EOQ
<?php
\$searchFields['Bug44030_TestPerson']['email'] = array(
'query_type' => 'default',
'operator' => 'subquery',
'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
'db_field' => array('id',),
'vname' =>'LBL_ANY_EMAIL',
);
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }	    
	    
	    
	    //Remove the cached unified_search_modules.php file
	    $this->unified_search_modules_file = $GLOBALS['sugar_config']['cache_dir'] . 'modules/unified_search_modules.php';
    	if(file_exists($this->unified_search_modules_file))
		{
			copy($this->unified_search_modules_file, $this->unified_search_modules_file.'.bak');
			unlink($this->unified_search_modules_file);
		}		
    }
    
    public function tearDown() 
    {
	    global $beanList, $beanFiles, $dictionary;
	    
		if(file_exists($this->unified_search_modules_file . '.bak'))
		{
			copy($this->unified_search_modules_file . '.bak', $this->unified_search_modules_file);
			unlink($this->unified_search_modules_file . '.bak');
		}	
		
		if(file_exists('custom/modules/Bug44030_TestPerson/metadata/SearchFields.php'))
		{
			unlink('custom/modules/Bug44030_TestPerson/metadata/SearchFields.php');
			rmdir_recursive('custom/modules/Bug44030_TestPerson');
		}
		unset($beanFiles['Bug44030_TestPerson']);
		unset($beanList['Bug44030_TestPerson']);
		unset($dictionary['Bug44030_TestPerson']);
    }
	
	public function testUnifiesSearchAdvancedBuildCache()
	{
		require_once('modules/Home/UnifiedSearchAdvanced.php');
		$usa = new UnifiedSearchAdvanced();
		$usa->buildCache();
		
		//Assert we could build the file without problems
		$this->assertTrue(file_exists($this->unified_search_modules_file), "Assert {$this->unified_search_modules_file} file was created");
	
	    require_once($this->unified_search_modules_file);
	    $this->assertTrue(isset($unified_search_modules['Bug44030_TestPerson']), "Assert that we have the custom module set in unified_search_modules.php file");
	    $this->assertTrue(isset($unified_search_modules['Bug44030_TestPerson']['fields']['email']), "Assert that the email field was set for the custom module");
	}

}

?>