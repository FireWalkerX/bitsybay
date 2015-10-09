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
require('../../system/library/translate.php');

// Init Database
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

// Init translate API
$translate = new Translate();

// Init counters
$total_translated = 0;
$total_added      = 0;

// Get language registry
$statement = $db->query('SELECT * FROM `language`');

$languages = array();
foreach ($statement->fetchAll() as $language) {
    $languages[$language->language_id] = $language->code;


    /*
     * Language upgrade mode: begin
     * Uncomment to add new language rows


    // Product descriptions
    $products = $db->query("SELECT `product_id` FROM `product`");
    if ($products->rowCount()) {

        // Check all products
        foreach ($products->fetchAll() as $product) {

            // Product description
            $product_description = $db->prepare("SELECT NULL FROM `product_description` WHERE `product_id` = ? AND `language_id` = ? LIMIT 1");
            $product_description->execute(array($product->product_id, $language->language_id));

            // Add new language row if not exist
            if (!$product_description->rowCount()) {
                $insert = $db->prepare("INSERT INTO `product_description` SET `product_id` = ?, `language_id` = ?, `title` = '', `description` = ''");
                $insert->execute(array($product->product_id, $language->language_id));

                $total_added++;
            }
        }
    }

    // Product tags
    $tags = $db->query("SELECT `tag_id` FROM `tag`");
    if ($tags->rowCount()) {

        // Check all products
        foreach ($tags->fetchAll() as $tag) {

            // Product description
            $tag_description = $db->prepare("SELECT NULL FROM `tag_description` WHERE `tag_id` = ? AND `language_id` = ? LIMIT 1");
            $tag_description->execute(array($tag->tag_id, $language->language_id));

            // Add new language row if not exist
            if (!$tag_description->rowCount()) {
                $insert = $db->prepare("INSERT INTO `tag_description` SET `tag_id` = ?, `language_id` = ?, `name` = ''");
                $insert->execute(array($tag->tag_id, $language->language_id));

                $total_added++;
            }
        }
    }

    // Product demo descriptions
    $product_demos = $db->query("SELECT `product_demo_id` FROM `product_demo`");
    if ($product_demos->rowCount()) {

        // Check all products
        foreach ($product_demos->fetchAll() as $product_demo) {

            // Product description
            $product_demo_description = $db->prepare("SELECT NULL FROM `product_demo_description` WHERE `product_demo_id` = ? AND `language_id` = ? LIMIT 1");
            $product_demo_description->execute(array($product_demo->product_demo_id, $language->language_id));

            // Add new language row if not exist
            if (!$product_demo_description->rowCount()) {
                $insert = $db->prepare("INSERT INTO `product_demo_description` SET `product_demo_id` = ?, `language_id` = ?, `title` = ''");
                $insert->execute(array($product_demo->product_demo_id, $language->language_id));

                $total_added++;
            }
        }
    }

    // Product image descriptions
    $product_images = $db->query("SELECT `product_image_id` FROM `product_image`");
    if ($product_images->rowCount()) {

        // Check all products
        foreach ($product_images->fetchAll() as $product_image) {

            // Product description
            $product_image_description = $db->prepare("SELECT NULL FROM `product_image_description` WHERE `product_image_id` = ? AND `language_id` = ? LIMIT 1");
            $product_image_description->execute(array($product_image->product_image_id, $language->language_id));

            // Add new language row if not exist
            if (!$product_image_description->rowCount()) {
                $insert = $db->prepare("INSERT INTO `product_image_description` SET `product_image_id` = ?, `language_id` = ?, `title` = ''");
                $insert->execute(array($product_image->product_image_id, $language->language_id));

                $total_added++;
            }
        }
    }

    // Product video descriptions
    $product_videos = $db->query("SELECT `product_video_id` FROM `product_video`");
    if ($product_videos->rowCount()) {

        // Check all products
        foreach ($product_videos->fetchAll() as $product_video) {

            // Product description
            $product_video_description = $db->prepare("SELECT NULL FROM `product_video_description` WHERE `product_video_id` = ? AND `language_id` = ? LIMIT 1");
            $product_video_description->execute(array($product_video->product_video_id, $language->language_id));

            // Add new language row if not exist
            if (!$product_video_description->rowCount()) {
                $insert = $db->prepare("INSERT INTO `product_video_description` SET `product_video_id` = ?, `language_id` = ?, `title` = ''");
                $insert->execute(array($product_video->product_video_id, $language->language_id));

                $total_added++;
            }
        }
    }

    // Product audio descriptions
    $product_audios = $db->query("SELECT `product_audio_id` FROM `product_audio`");
    if ($product_audios->rowCount()) {

        // Check all products
        foreach ($product_audios->fetchAll() as $product_audio) {

            // Product description
            $product_audio_description = $db->prepare("SELECT NULL FROM `product_audio_description` WHERE `product_audio_id` = ? AND `language_id` = ? LIMIT 1");
            $product_audio_description->execute(array($product_audio->product_audio_id, $language->language_id));

            // Add new language row if not exist
            if (!$product_audio_description->rowCount()) {
                $insert = $db->prepare("INSERT INTO `product_audio_description` SET `product_audio_id` = ?, `language_id` = ?, `title` = ''");
                $insert->execute(array($product_audio->product_audio_id, $language->language_id));

                $total_added++;
            }
        }
    }

    * Language upgrade mode: end
    *
    */

}


// Translate product descriptions
$statement = $db->query("SELECT * FROM `product_description` WHERE `title` = '' OR description = ''");

if ($statement->rowCount()) {

    foreach ($statement->fetchAll() as $untranslated) {

        // Get translated data
        $translated = $db->prepare("SELECT `product_id`, `language_id`, `title`, `description` FROM `product_description` WHERE `title` <> '' AND `description` <> '' AND `product_id` = ? LIMIT 1");
        $translated->execute(array($untranslated->product_id));

        if ($translated->rowCount() && $translated = $translated->fetch()) {

            // Translate title
            if (empty($untranslated->title) &&
                false !== $title = $translate->translate($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_description` SET `title` = ? WHERE `product_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_id, $untranslated->language_id));

                $total_translated++;
            }

            // Translate description
            if (empty($untranslated->description) &&
                false !== $description = $translate->translate($translated->description, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_description` SET `description` = ? WHERE `product_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($description, $untranslated->product_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Translate tag descriptions
$statement = $db->query("SELECT * FROM `tag_description` WHERE `name` = ''");

if ($statement->rowCount()) {

    foreach ($statement->fetchAll() as $untranslated) {

        // Get translated data
        $translated = $db->prepare("SELECT `tag_id`, `language_id`, `name` FROM `tag_description` WHERE `name` <> '' AND `tag_id` = ? LIMIT 1");
        $translated->execute(array($untranslated->tag_id));

        if ($translated->rowCount() && $translated = $translated->fetch()) {

            // Translate name
            if (empty($untranslated->name) &&
                false !== $name = $translate->translate($translated->name, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `tag_description` SET `name` = LCASE(?) WHERE `tag_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($name, $untranslated->tag_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Translate demo descriptions
$statement = $db->query("SELECT * FROM `product_demo_description` WHERE `title` = ''");

if ($statement->rowCount()) {

    foreach ($statement->fetchAll() as $untranslated) {

        // Get translated data
        $translated = $db->prepare("SELECT `product_demo_id`, `language_id`, `title` FROM `product_demo_description` WHERE `title` <> '' AND `product_demo_id` = ? LIMIT 1");
        $translated->execute(array($untranslated->product_demo_id));

        if ($translated->rowCount() && $translated = $translated->fetch()) {

            // Translate title
            if (empty($untranslated->title) &&
                false !== $title = $translate->translate($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_demo_description` SET `title` = ? WHERE `product_demo_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_demo_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Translate image descriptions
$statement = $db->query("SELECT * FROM `product_image_description` WHERE `title` = ''");

if ($statement->rowCount()) {

    foreach ($statement->fetchAll() as $untranslated) {

        // Get translated data
        $translated = $db->prepare("SELECT `product_image_id`, `language_id`, `title` FROM `product_image_description` WHERE `title` <> '' AND `product_image_id` = ? LIMIT 1");
        $translated->execute(array($untranslated->product_image_id));

        if ($translated->rowCount() && $translated = $translated->fetch()) {

            // Translate title
            if (empty($untranslated->title) &&
                false !== $title = $translate->translate($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_image_description` SET `title` = ? WHERE `product_image_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_image_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Translate video descriptions
$statement = $db->query("SELECT * FROM `product_video_description` WHERE `title` = ''");

if ($statement->rowCount()) {

    foreach ($statement->fetchAll() as $untranslated) {

        // Get translated data
        $translated = $db->prepare("SELECT `product_video_id`, `language_id`, `title` FROM `product_video_description` WHERE `title` <> '' AND `product_video_id` = ? LIMIT 1");
        $translated->execute(array($untranslated->product_video_id));

        if ($translated->rowCount() && $translated = $translated->fetch()) {

            // Translate title
            if (empty($untranslated->title) &&
                false !== $title = $translate->translate($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_video_description` SET `title` = ? WHERE `product_video_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_video_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Translate audio descriptions
$statement = $db->query("SELECT * FROM `product_audio_description` WHERE `title` = ''");

if ($statement->rowCount()) {

    foreach ($statement->fetchAll() as $untranslated) {

        // Get translated data
        $translated = $db->prepare("SELECT `product_audio_id`, `language_id`, `title` FROM `product_audio_description` WHERE `title` <> '' AND `product_audio_id` = ? LIMIT 1");
        $translated->execute(array($untranslated->product_audio_id));

        if ($translated->rowCount() && $translated = $translated->fetch()) {

            // Translate title
            if (empty($untranslated->title) &&
                false !== $title = $translate->translate($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_audio_description` SET `title` = ? WHERE `product_audio_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_audio_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Response
die(sprintf('total added: %s, total translated: %s', $total_added, $total_translated));