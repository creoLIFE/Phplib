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
        $this->assertEquals($this->pArray['url'], $this->pClass->getParam('url'));
    }

    public function testGetUnsafeParam()
    {
        $this->pClass->setSafeMode(false);
        $this->assertEquals($this->pArray['unsafe'], $this->pClass->getParam('unsafe'));
    }

    public function testGetUnsafeParamProtected()
    {
        $this->assertContains('&lt;script&gt;', $this->pClass->getParam('unsafe'));
    }

    public function testGetParamRegex()
    {
        $this->assertEquals('Product 1', $this->pClass->getParam('product', 'string'));
    }

    public function testGetNull()
    {
        $this->assertNull($this->pClass->getParam('product', 'letters'));
    }
}