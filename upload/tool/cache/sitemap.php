<?php

/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    BitsyBay Engine
 * @copyright  Copyright (c) 2015 The BitsyBay Project (http://bitsybay.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */

// Load dependencies
require('../../config.php');
require('../../system/library/url.php');
require('../../system/library/request.php');
require('../../system/library/response.php');
require('../../system/library/sitemap.php');

// Init the database
try {
    $db = new PDO(
        'mysql:dbname=' . DB_DATABASE . ';host=' . DB_HOSTNAME . ';charset=utf8',
        DB_USERNAME,
        DB_PASSWORD,
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        )
    );

    $db->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $db->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_OBJ
    );

} catch(PDOException $e) {
    $error[] = $e->getMessage();
    exit;
}

// Init helpers
function canonical($language_id, $language_code, $url) {
    return $language_id == DEFAULT_LANGUAGE_ID ? $url : str_replace(URL_BASE, URL_BASE . $language_code . '/', $url);
}

// Init request
$request = new Request();

// Init response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression(GZIP_COMPRESSION_LEVEL);

// Init URL
$url = new Url($db, $request, $response, URL_BASE);

// Init sitemap
$sitemap = new Sitemap(URL_BASE);
$sitemap->setPath(DIR_BASE . DIR_SEPARATOR . 'public' . DIR_SEPARATOR);

// Get alt languages
$statement = $db->query('SELECT * FROM `language`');
$languages = array(); foreach ($statement->fetchAll() as $language) $languages[$language->language_id] = $language->code;

// Add information Pages
foreach ($languages as $language_id => $language_code) {
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/about')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/team')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/terms')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/licenses')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/faq')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/bitcoin')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/information/promo')), '1.0', 'monthly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('common/contact')), '1.0', 'yearly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('account/account/login')), '1.0', 'yearly');
    $sitemap->addItem(canonical($language_id, $language_code, $url->link('account/account/create')), '1.0', 'yearly');
}


// Generate categories
$statement = $db->query('SELECT `c`.`category_id`,
                                (SELECT MAX(`p`.`date_modified`) FROM `product` AS `p` WHERE `p`.`category_id` = `c`.`category_id` AND `p`.`status` = 1) AS `date_modified`
                                FROM `category` AS `c`
                                HAVING `date_modified` IS NOT NULL');

if ($statement->rowCount()) {
    foreach ($statement->fetchAll() as $category) {
        foreach ($languages as $language_id => $language_code) {
            $sitemap->addItem(canonical($language_id, $language_code, $url->link('catalog/category', 'category_id=' . $category->category_id)), '0.9', 'daily', $category->date_modified);
        }
    }
}

// Generate product links
$statement = $db->query('SELECT `product_id`, `date_modified` FROM `product` WHERE `status` = 1');

if ($statement->rowCount()) {
    foreach ($statement->fetchAll() as $product) {
        foreach ($languages as $language_id => $language_code) {
            $sitemap->addItem(canonical($language_id, $language_code, $url->link('catalog/product', 'product_id=' . $product->product_id)), '0.8', 'weekly', $product->date_modified);
        }
    }
}

// Generate tag links

$statement = $db->query('SELECT `td`.`name`,
                            (SELECT MAX(`p`.`date_modified`)
                                FROM `product` AS `p`
                                JOIN `product_to_tag` AS `p2t` ON (`p2t`.`product_id` = `p`.`product_id`)
                                WHERE `p`.`status` = 1 AND `p2t`.`tag_id` = `td`.`tag_id`) AS `date_modified`
                            FROM `tag_description` AS `td`
                            HAVING `date_modified` IS NOT NULL');

if ($statement->rowCount()) {
    foreach ($statement->fetchAll() as $search) {
        foreach ($languages as $language_id => $language_code) {
            $sitemap->addItem(canonical($language_id, $language_code, $url->link('catalog/search', 'q=' . rawurlencode($search->name))), '0.7', 'weekly', $search->date_modified);
        }
    }
}

// Save sitemap
$sitemap->createSitemapIndex(URL_BASE);
