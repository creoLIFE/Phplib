<?php
/**
 * Created by PhpStorm.
 * User: miroslawratman
 * Date: 17/12/14
 * Time: 10:27
 */

ERROR_REPORTING(E_ALL | E_STRICT);

require_once(__DIR__ . '/../vendor/autoload.php');

$pClass = new \Phplib\Helpers\Params(
    array(
        'url' => 'http://www.google.pl',
        'product' => 'Product 1'
    )
);

var_dump($pClass->getParam('product', '/[a-zA-Z0-9\s]+/'));