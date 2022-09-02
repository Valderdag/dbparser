<?php
require_once 'vendor/autoload.php';
require_once 'functions/func_category.php';
require_once 'functions/helpers.php';

set_time_limit(0);
ini_set('memory_limit', -1);

    $client = new \GuzzleHttp\Client();
    $document = new \DiDom\Document();
    $url = "https://domain.com/category/";
    $nums_pages = '21';
    echo "Start parsing...\n";
    $file = get_html($url, $client);
    $document->loadHtml($file);
    $name_json = "collections".date('d-m-y');
    $colls_data = [];
    for ($i = 1; $i <= $nums_pages; $i++)
    {
        try{
            echo "PAGE PARSING {$i} of {$nums_pages}...\n";
            sleep(rand(2, 5));
            if ($i > 1){
                $file = get_html($url . "?PAGEN_2={$i}", $client);
                $document->loadHtml($file);
            }
            $colls_data = array_merge($colls_data, get_colls($document, $client));
            file_put_contents($name_json.".json", json_encode($colls_data, FILE_APPEND | LOCK_EX | JSON_UNESCAPED_UNICODE));
        }catch(Exception $exception){
            $exception->getMessage() . " " . $exception->getCode() . " " . $exception->getFile() . " " . $exception->getLine();
            continue;
        }
    }
    $cl_cnt = count($colls_data);
    echo "\n=================================\n";
    echo "Complited! Items received {$cl_cnt}";
?>
