<?php
require_once 'vendor/autoload.php';
require_once 'functions/func_catalog.php';
require_once 'functions/helpers.php';

set_time_limit(0);
ini_set('memory_limit', -1);

if(!empty($argv[1]))
{
    //$jar = new \GuzzleHttp\Cookie\CookieJar();
    $client = new \GuzzleHttp\Client();
    $document = new \DiDom\Document();
    $url = "https://domain.com/catalog/";
    $nums_pages = $argv[1];
    echo "Start parsing...\n";
    $file = get_html($url, $client);
    $document->loadHtml($file);
    $name_json = "catalog".date('d-m-y');
    $products_data = [];
    for($i = 1; $i <= $nums_pages; $i++ )
    {
        try{
            echo "PAGE PARSING {$i} of {$nums_pages}...\n";
            sleep(rand(2, 5));
            if ($i > 1) {
                $file = get_html($url . "?PAGEN_1={$i}", $client);
                $document->loadHtml($file);
            }
            $products_data = array_merge($products_data, get_products($document, $client));
            file_put_contents("$name_json.json", json_encode($products_data, FILE_APPEND | LOCK_EX | JSON_UNESCAPED_UNICODE));
        }catch(Exception $exception){
            $exception->getMessage() . " " . $exception->getCode() . " " . $exception->getFile() . " " . $exception->getLine();
            continue;
        }
    }
    $pr_cnt = count($products_data);
    echo "\n=================================\n";
    echo "Complited! Items received {$pr_cnt}";
}
?>
