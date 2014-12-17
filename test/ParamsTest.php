<?php
/**
 * Created by PhpStorm.
 * User: miroslawratman
 * Date: 17/12/14
 * Time: 10:27
 */

ERROR_REPORTING(E_ALL);

require_once(__DIR__ . '/../vendor/autoload.php');

class ParamsTest extends PHPUnit_Framework_TestCase
{
    private $pArray;
    private $pClass;

    public function __construct()
    {
        $this->pArray = array(
            'url' => 'http://www.google.pl',
            'product' => 'Product 1',
            'unsafe' => '<script>alert(1)</script>Product 1'
        );

        $this->pClass = new \Phplib\Helpers\Params($this->pArray);
    }

    public function testGetParam()
    {
        $this->assertEquals($this->pClass->getParam('url'), $this->pArray['url']);
    }

    public function testGetUnsafeParam()
    {
        $this->pClass->setSafeMode(false);
        $this->assertEquals($this->pClass->getParam('unsafe'), $this->pArray['unsafe']);
    }

    public function testGetUnsafeParamProtected()
    {
        $this->assertContains('&lt;script&gt;', $this->pClass->getParam('unsafe'));
    }

    public function testGetParamRegex()
    {
        $this->assertEquals($this->pClass->getParam('product', '/[a-zA-Z0-9\s]+/'), 'Product 1');
    }

    public function testGetFalse()
    {
        $this->assertFalse($this->pClass->getParam('product', '/[a-z]+/'), 'Product 1');
    }
}