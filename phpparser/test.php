<?php
error_reporting(-1);
require_once 'vendor/autoload.php';
require_once 'functions/func_category.php';
require_once 'functions/helpers.php';

$url = "https://domain.com/category/example/";
$client = new \GuzzleHttp\Client();
$document = new \DiDom\Document();
$file = get_html($url, $client);
$document->loadHtml($file);



$coll = [];
$colls = $document->find('.product-item-detail-tab-content ul li');
foreach($colls as $row){
    echo $coll['desc'] = trim($row->text());
}
