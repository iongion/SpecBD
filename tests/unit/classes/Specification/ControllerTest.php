<?php
namespace Specification;

use PHPUnit_Framework_TestCase;

use Helpers\PHPUnitUtil;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    protected $root;

    public function setUp()
    {
        $root = getenv('PHPUNIT_PROJECT_ROOT');
        $this->root = $root;
    }

    public function tearDown()
    {
        $this->root = null;
    }
    
    public function testLoadFromClassesArray()
    {
      
    }
    
    public function testLoadFromNamespacesPrefix()
    {
      
    }
    
    public function testParseClass()
    {
      
    }
    
    public function testGetSpecifications()
    {
      
    }
    
    public function testGetServiceSpecificationMap()
    {
      
    }
    
    public function testParseSchema()
    {
      
    }
    
    public function testDecodeDataWithSchemaArray()
    {
      
    }
    
    public function testValidateDataWithSchemaFile()
    {
      
    }
    
    public function testValidateDataWithSchemaArray()
    {
      
    }
}
