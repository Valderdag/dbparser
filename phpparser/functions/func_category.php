<?php
require_once 'vendor/autoload.php';

function get_html($url, \GuzzleHttp\Client $client): string
{
    $jar = \GuzzleHttp\Cookie\CookieJar();
    $resp = $client->get($url, [
        'headers' => [
            'authority' => 'domain.com',
            'method' => 'GET',
            'path' => '/categories/',
            'scheme' => 'https',
            'accept' => ['text/html','application/xhtml+xml','application/xml','q=0.9','image/avif','image/webp','image/apng','*/*;q=0.8', 'application/signed-exchange;v=b3;q=0.9'],
            'accept-encoding' => ['gzip', 'deflate', 'br'],
            'accept-language' => ['ru','ru-RU',';q=0.9','en-US',';q=0.8','en;q=0.7'],
            'cache-control' => 'no-cache',
            'cookie' => $jar,
            'pragma' => 'no-cache',
            'sec-ch-ua' => [".Not/A)Brand",';v="99"', "Google Chrome",';v="103"', "Chromium",';v="103"'],
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => 'Windows',
            'sec-fetch-dest' => 'document',
            'sec-fetch-mode' => 'navigate',
            'sec-fetch-site' => 'none',
            'sec-fetch-user' => '?1',
            'upgrade-insecure-requests' => 1,
            'user-agent' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36']
        ]
    ]);
    return $resp->getBody()->getContents();
}

function get_colls(\DiDom\Document $document, \GuzzleHttp\Client $client): array
{
    static $coll_cnt = 1;
    $colls_data = [];
    $document->first('head script')->remove();
    $colls = $document->find('.arrivals-block__main-list.arrivals-grid1 .arrivals-block__item ');
    foreach ($colls as $row){
        sleep(rand(1, 4));
        echo "Collection {$coll_cnt} ...\n";
        $url = 'https://domain.com'.$row->first('a')->attr('href');
        $colls_data[$coll_cnt] = get_coll($document, $client, $url, $coll_cnt);
        print($url) . PHP_EOL;
        $coll_cnt++;
    }
    return $colls_data;
}

function get_coll(\DiDom\Document $document, \GuzzleHttp\Client $client, $url, $coll_cnt): array
{
    $coll = [];
    $file = get_html($url, $client);
    $document->loadHtml($file);
    if ($document->has('.collection-detail h1')){
        $coll['title'] = trim(preg_replace("/\s+/"," ", $document->first('.collection-detail h1')->text()));
    }
    if (isset($coll['title']) && !empty($coll['title'])){
        $coll['name'] = trim(substr($coll['title'], 30));
    }
    if ($document->has('meta[property=product:brand]')){
        $coll['brand'] = trim($document->first('meta[property=product:brand]')->attr('content'));
    }
    if (isset($coll['brand']) && !empty($coll['brand'])){
        $brand_dir = trim(str_replace('&', '_', str_replace(' ', '-', mb_strtolower($coll['brand']))));
    }
    $coll_dir = str_replace(' ', '-', mb_strtolower($coll['name']));
    $img_path = 'https://oboi-palitra.ru'.$document->first('.left-mob img')->attr('src');
    if( $document->has('.product-item-detail-tab-content p')):
        $coll_desc = $document->find('.product-item-detail-tab-content p');
        foreach ($coll_desc as $row){
            $coll['desc'][] = trim(preg_replace("/\s+/"," ",$row->text()));
        }
        endif;
    if($document->has('..product-item-detail-tab-content ul li')):
        $coll_desc = $document->find('.product-item-detail-tab-content ul li');
        foreach ($coll_desc as $row){
            $coll['desc'][] = trim(preg_replace("/\s+/"," ",$row->text()));
        }
        endif;

  $cool['tip'] = trim(substr($document->first('.collectip .val')->text(), 1).':'.$document->first('.collectip .collectfavor')->text());
  $cool['method'] = trim(substr($document->first('.collectipproizvod .val')->text(), 1).':'.$document->first('.collectipproizvod .collectfavor')->text());
//SAVE IMAGES COLLECTION
  if (!is_dir("images/$brand_dir/$coll_dir/PAGE__COLL")){
    mkdir("images/$brand_dir/$coll_dir/PAGE__COLL");
}
$image_name = "coll_".$coll_dir.'.'.trim(get_ext($img_path));
$file_path = "images/$brand_dir/$coll_dir/PAGE__COLL/$image_name";
file_put_contents($file_path, file_get_contents($img_path));
$coll['image'] = $file_path;
return $coll;
}

function get_ext($file_name)
{
    $data = explode('.', $file_name);
    $data = explode('?', end($data));
    return $data[0];
}
