<?php
error_reporting(-1);
require 'vendor/autoload.php';
require_once 'functions/func_db.php';

$brands = json_decode(file_get_contents('json/brands11-08-22.json'));
$colls = json_decode(file_get_contents('json/collections11-08-22.json'));
$products = json_decode(file_get_contents('json/catalog11-08-22.json'));
setProducts($brands, $colls, $products);
//echo $c = count($products);
