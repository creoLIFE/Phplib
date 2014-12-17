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
    private $pClass;

    public function __construct()
    {
        $this->pClass = new \Phplib\Helpers\Params(
            array(
                'url' => 'http://www.google.pl',
                'product' => 'Product 1'
            )
        );
    }

    public function testGetParam()
    {
        $this->assertEquals($this->pClass->getParam('product'), 'Product 1');
    }

    public function testGetParamRegex()
    {
        $this->assertEquals($this->pClass->getParam('product', '/[a-zA-Z0-9\s]+/'), 'Product 1');
    }
}