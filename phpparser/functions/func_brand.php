<?php
require_once 'vendor/autoload.php';

function get_html($url, \GuzzleHttp\Client $client): string
{
    \GuzzleHttp\RequestOptions::HEADERS;
    $resp = $client->get($url, [
       'headers' => [
            'authority' => 'domain.com',
            'method' => 'GET',
            'path' => '/catalog/brands/',
            'scheme' => 'https',
            'accept' => ['text/html','application/xhtml+xml','application/xml','q=0.9','image/avif','image/webp','image/apng','*/*;q=0.8', 'application/signed-exchange;v=b3;q=0.9'],
            'accept-encoding' => ['gzip', 'deflate', 'br'],
            'accept-language' => ['ru','ru-RU',';q=0.9','en-US',';q=0.8','en;q=0.7'],
            'cache-control' => 'no-cache',
            'cookie' => ['BITRIX_SM_FAVORITES_UID=57c05ad0dddd9dc88e30fa859382d6bd; waSessionId=9aa3e8dd-1278-eb81-89e4-58966966de4e; waUserId_251155369-nona_chistovik_-251155369-FAk-18229139804=85f0c3bb-ad33-957d-a5a7-1d5960047452; _ym_uid=1658578808791731516; BITRIX_SM_SALE_UID=b2019cd59f39ca67e90f193617fd6292; _fbp=fb.1.1658578850663.93381342; PHPSESSID=jhg1pqa1khnrg2c61rgr1s5tg2; _ym_visorc=w; _ym_isad=1; BITRIX_CONVERSION_CONTEXT_s1=%7B%22ID%22%3A2%2C%22EXPIRE%22%3A1659214740%2C%22UNIQUE%22%3A%5B%22conversion_visit_day%22%5D%7D; BITRIX_SM_GUEST_ID=1068562; BITRIX_SM_LAST_VISIT=30.07.2022+21%3A41%3A18; _ym_d=1659206479'],
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

/*function get_products(\DiDom\Document $document, \GuzzleHttp\Client $client): array
{
    static $product_cnt = 1;
    $products_data = [];
    $document->first('head script')->remove();
    $products = $document->find('div.item-card__card-wrapper');
    foreach ($products as $product) {
        sleep(rand(1, 4));
        echo "product {$product_cnt} ...\n";
        $url = "https://oboi-palitra.ru" . $product->first('a.item-card__card-full-link')->attr('href');
        $products_data[$product_cnt] = get_product($document, $client, $url, $product_cnt);
        print($url) . PHP_EOL;
        $product_cnt++;
    }
    return $products_data;
}

function get_product(\DiDom\Document $document, \GuzzleHttp\Client $client, $url, $product_cnt): array
{
    $file = get_html($url, $client);
    $document->loadHtml($file);
    $product['brand'] = $document->first('.detail-desk meta[itemprop=brand]')->attr('content');
    $product['collection'] = $document->first('div.container .model-caption')->text();
    $product['vendor_code'] = trim($document->first('div.container .article-caption h1')->text());
    $product['name'] = $document->first('.detail-desk meta[itemprop=name]')->attr('content');
    $product['description'] = $document->first('.detail-desk meta[itemprop=description]')->attr('content');
    $product['productID'] = $document->first('.detail-desk meta[itemprop=productID]')->attr('content');
    if ($document->has('.detail-desk meta[property=product:price:amount]')):
        $product['price'] = $document->first('.detail-desk meta[property=product:price:amount]')->attr('content');
    endif;
    $product['currency'] = $document->first('.detail-desk meta[property=product:price:currency]')->attr('content');

    //Images
    $dir_br = str_replace(' ', '-', mb_strtolower($product['brand']));
    $dir_coll = str_replace(' ', '-', mb_strtolower($product['collection']));
    $a = $document->first('.product-top')->attr('style');
    $b = explode(')', $a);
    $c = explode('(', $b[1]);
    $e = explode("'", $c[1]);
    $image_path = 'https://domain.com'.trim($e[1]);
    $image_name = $product['vendor_code'] . "." . trim(get_ext($image_path));
    if (!is_dir("images/$dir_br/$dir_coll")) {
        mkdir("images/$dir_br/$dir_coll", 0777, true);
    }
    $file_path = "images/$dir_br/$dir_coll/$image_name";
    file_put_contents($file_path, file_get_contents($image_path));
    $product['image'] = $file_path;
    //Options
    if ($document->has('ul.settings li span.label') && $document->has('ul.settings li span.val')):
        $names = $document->find('ul.settings li span.label');
    $values = $document->find('ul.settings li span.val');
    foreach ($names as $k => $name):
        $product['options'][] = $name->text().trim(preg_replace("/\s+/"," ", $values[$k]->text()));
    endforeach;
endif;
return $product;
}
*/
function get_ext($file_name)
{
    $data = explode('.', $file_name);
    $data = explode('?', end($data));
    return $data[0];
}