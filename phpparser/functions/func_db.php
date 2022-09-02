<?php
error_reporting(-1);
require_once '../vendor/autoload.php';
require_once '../helpers.php';

R::ext('xdispense', function($table_name){
    return R::getRedBean()->dispense($table_name);
});
\RedBeanPHP\R::setup('mysql:host=127.0.0.1; dbname=opencart4', 'root', 'root');
if(!\RedBeanPHP\R::testConnection()){
    echo "ERROR CONNECT";
}
$brds = json_decode( file_get_contents('file.json'));
$clct = json_decode( file_get_contents('file.json'));
$prdt = json_decode( file_get_contents('file.json'));