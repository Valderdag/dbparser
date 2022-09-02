<?php
error_reporting(-1);
require 'vendor/autoload.php';
require_once 'functions/debug.php';
require_once 'helpers/helpers.php';
class_alias('\RedBeanPHP\R', '\R');
R::ext('xdispense', function ($table_name) {
    return R::getRedBean()->dispense($table_name);
});

R::setup("mysql:host=host; dbname=dbname", "db_user", "secret");
/*R::setup("mysql:host=127.0.0.1; dbname=oc4", "root", "root");*/
R::freeze(TRUE);

function setProducts($brands, $colls, $products = 0)
{
    //MANUFACTURER
    foreach ($brands as $brand) {
        R::exec('
INSERT IGNORE INTO oc_manufacturer (name, image, sort_order) VALUES (?,?,?)', [$brand->name, $brand->images, 0]);
        $br_id = R::getInsertID();
        R::exec('
INSERT IGNORE INTO oc_manufacturer_to_store (manufacturer_id, store_id) VALUES (?,?)', [$br_id, 0]);
    }
    //CATEGORIES
    foreach ($brands as $brand) {
        $image = str_replace('images/', 'catalog/', str_replace('&', '_', $brand->images));
        R::exec('INSERT INTO oc_category (image) VALUES (?)', [$image]);
        $br_id = R::getInsertID();
        R::exec('INSERT INTO oc_category_description (category_id, language_id, name, description, meta_title, meta_description, meta_keyword) VALUES (?,?,?,?,?,?,?)', [$br_id, 1, $brand->name, $brand->desc, "Купить виниловые обои {$brand->name}", $brand->desc, $brand->desc]);
        R::exec('INSERT INTO oc_category_to_store (category_id, store_id) VALUES (?, ?) ', [$br_id, 0]);
        R::exec('INSERT INTO oc_category_path (category_id, path_id, level) VALUES (?, ?, ?) ', [$br_id, $br_id, 0]);

        foreach ($colls as $coll) {
            $image = str_replace('images/', 'catalog/', str_replace('&', '_',$coll->image));
            if ($brand->name == $coll->brand) {
                $parent_id = $br_id;
                R::exec('INSERT INTO oc_category (image, parent_id) VALUES (?,?)', [$image, $parent_id]);
                $coll_id = R::getInsertID();
                if (!empty($coll->desc)) {
                    $desc = implode(',', $coll->desc);
                    R::exec('INSERT INTO oc_category_description (category_id, language_id, name, description, meta_title, meta_description, meta_keyword) VALUES (?,?,?,?,?,?,?)', [$coll_id, 1, $coll->name, $desc, $coll->title, $desc, $desc]);
                }
                R::exec('INSERT INTO oc_category_to_store (category_id, store_id) VALUES (?, ?) ', [$coll_id, 0]);
                R::exec('INSERT INTO oc_category_path (category_id, path_id, level) VALUES (?, ?, ?) ', [$coll_id, $parent_id, 1]);

                //PRODUCTS
                foreach ($products as $product) {
                    if ($product->collection == $coll->name) {
                        $category_id = $coll_id;
                        $opt = explode(' ', $product->options[2]);
                        $width = $opt[3];
                        $length = $opt[5];
                        $image = str_replace('images/', 'catalog/', str_replace('&', '_', $product->image));
                        if (!empty($product->price)) {
                            R::exec('INSERT INTO oc_product (model, sku, location, quantity, stock_status_id, image, price, length, width, status) VALUES (?,?,?,?,?,?,?,?,?,?)', [$product->vendor_code, $product->vendor_code, 'Москва', 50, 7, $image, $product->price, $length, $width, 1]);
                        } else {
                            R::exec('INSERT INTO oc_product (model, sku, location, quantity, image, price, length, width, status) VALUES (?,?,?,?,?,?,?,?,?)', [$product->vendor_code, $product->vendor_code, 'Москва', 50, $image, 0, $length, $width, 1]);
                        }
                        $product_id = R::getInsertID();
                        R::exec('INSERT INTO oc_product_description (product_id, language_id, name, description, meta_title, meta_description) VALUES (?,?,?,?,?,?)', [$product_id, 1, $product->vendor_code, $product->description, $product->name, $product->description]);

                        R::exec('INSERT INTO oc_product_image (product_id, image) VALUES (?, ?) ', [$product_id, $image]);

                        R::exec('INSERT INTO oc_product_to_category (product_id, category_id) VALUES (?, ?) ', [$product_id, $category_id]);

                        R::exec('INSERT INTO oc_product_to_store (product_id, store_id) VALUES (?, ?) ', [$product_id, 0]);
                    }
                }
            }
        }
    }
}
