<?php
namespace Specification;

use PHPUnit_Framework_TestCase;

use Helpers\PHPUnitUtil;

class ServiceTest extends PHPUnit_Framework_TestCase
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
    
    public function testExecute()
    {
      
    }
}
