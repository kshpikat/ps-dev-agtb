<?php

class PMSEConcurrencyValidatorTest extends PHPUnit_Framework_TestCase 
{
    private $validator;
    
    /**
     * Sets up the test data, for example, 
     *     opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        
    }

    /**
     * Removes the initial test configurations for each test, for example:
     *     close a network connection. 
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }
    
    /**
     * Test if a flow is being concurrently requested by the direct handler class
     */
    public function testValidateRequestIfConcurrent()
    {
        $this->validator = $this->getMockBuilder("PMSEConcurrencyValidator")
                ->disableOriginalConstructor()
                ->setMethods(NULL)
                ->getMock();
        
        $loggerMock = $this->getMockBuilder("PSMELogger")
                ->disableOriginalConstructor()
                ->setMethods(array('info', 'debug'))
                ->getMock();
        
        $_SESSION['locked_flows'] = array('abc123');
        
        $requestMock = $this->getMockBuilder("PMSERequest")
                ->disableOriginalConstructor()
                ->setMethods(array('getArguments'))
                ->getMock();
        
        $requestMock->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array('idFlow' => 'abc123')));
        
        $this->validator->setLogger($loggerMock);
        $result = $this->validator->validateRequest($requestMock);
        $this->assertEquals(false, $result->isValid());
    }
    
    /**
     * Test if no concurrent flows are being requested by the direct handler class
     */
    public function testValidateRequestIfNotConcurrent()
    {
        $this->validator = $this->getMockBuilder("PMSEConcurrencyValidator")
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();
        
        $loggerMock = $this->getMockBuilder("PSMELogger")
                ->disableOriginalConstructor()
                ->setMethods(array('info', 'debug'))
                ->getMock();
        
        $_SESSION['locked_flows'] = array();
        
        $requestMock = $this->getMockBuilder("PMSERequest")
                ->disableOriginalConstructor()
                ->setMethods(array('getArguments'))
                ->getMock();
        
        $requestMock->expects($this->once())
                ->method('getArguments')
                ->will($this->returnValue(array('idFlow' => 'abc123')));
        
        $this->validator->setLogger($loggerMock);
        $result = $this->validator->validateRequest($requestMock);
        $this->assertEquals(true, $result->isValid());
    }
}
