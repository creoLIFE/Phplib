<?php
/**
 * Created by PhpStorm.
 * User: miroslawratman
 * Date: 17/12/14
 * Time: 10:27
 */

ERROR_REPORTING(E_ALL | E_STRICT);

require_once(__DIR__ . '/../src/loader.php');

$pClass = new \Phplib\Helpers\Params(
    array(
        'url' => 'http://www.google.pl',
        'email' => 'test@email.com',
        'product' => 'Product 1',
        'offer' => 'offer',
        'unsafe' => "<script>alert(1)</script>Product 1"
    )
);

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('url'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('product', 'string'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('product', 'letters'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('unsafe'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('offer', '/[a-z]*/'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('email', 'email'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('some_non_existing_param', '/[a-z]*/'));

echo "---------------------------------------------------------------------------------\n";
var_dump($pClass->getParam('some', 'string', 'alternative response'));

//Throwing HelpersException
echo "---------------------------------------------------------------------------------\n";
$pClass = new \Phplib\Helpers\Params(
    array(
        'product' => 'Product 1',
    ),
    array(
        'product'
    )
);
var_dump($pClass->getParam('product', 'int'));

