<?php
error_reporting(-1);
require 'vendor/autoload.php';
require_once 'functions/debug.php';
require_once 'helpers/helpers.php';
class_alias('\RedBeanPHP\R', '\R');


$products = json_decode(file_get_contents('json/catalog31-07-22.json'));
foreach ($products as $product){
    $image = str_replace('images/', 'catalog/', $product->image);
    echo $image;die;
}


