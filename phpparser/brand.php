<?php
error_reporting(-1);
require_once 'vendor/autoload.php';
require_once 'functions/func_brand.php';
require_once 'functions/helpers.php';

$url = "https://domain.com/brands/";
$client = new \GuzzleHttp\Client();
$document = new \DiDom\Document();
$file = get_html($url, $client);
$document->loadHtml($file);
static $brand_cnt = 1;
$brand_data = [];
$brands = $document->find('.container .base-text');
foreach ($brands as $row){
  $logo_path = 'https://domain.com'.$row->first('.our-brands__slider-logo img')->attr('src');
  $brand['name'] = trim($row->first('h2')->text());
  $brand['desc'] = trim($row->first('.our-brands__item-description p')->text());
  echo "Parsing Brand {$brand_cnt}========={$brand['name']}.....\n";
  //SAVE IMAGES BRAND
  $logo_name = 'logo_'.str_replace(' ', '-', mb_strtolower($brand['name'])).'.'.get_ext($logo_path);
  $images = $row->find('.img-wrapper img');
  $dir_name = str_replace(' ', '-', mb_strtolower($brand['name']));
  static $img = 1;
  foreach ($images as $item){

    $image_path = 'https://domain.com'. $item->src;
    $image_name = 'slide_'.str_replace(' ', '-', mb_strtolower($brand['name'])).$img.'.'.get_ext($image_path);
    echo "image {$img}========={$image_name}.....\n";
    if (!is_dir("images/$dir_name/PAGE__BRAND")){
      mkdir ("images/$dir_name/PAGE__BRAND", 0777, true);
    }
    $file_img = "images/$dir_name/PAGE__BRAND/$image_name";
    file_put_contents($file_img, file_get_contents($image_path));
    $brand['images'] = $file_img;
    $img++;

  }
  //SAVE LOGO BRAND
  $file_logo = "images/$dir_name/PAGE__BRAND/$logo_name";
  file_put_contents($file_logo, file_get_contents($logo_path));
  $brand['logo'] = $file_logo;
  //SAVE DATA BRAND
  $brand_data[$brand_cnt] = $brand;
  $json_name = "brands".date('d-m-y').".json";
  file_put_contents($json_name, json_encode($brand_data, FILE_APPEND | LOCK_EX | JSON_UNESCAPED_UNICODE));
  //COUNTER BRAND
  $brand_cnt++;
  $br_cnt = count($brand_data);

}
echo "\n=================================\n";
echo "Complited! Brands received {$br_cnt}";