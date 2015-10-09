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
require('../../../config.php');
require('../../../system/library/translate.php');

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

// Get language registry
$statement = $db->query('SELECT * FROM `language`');

$languages = array();
foreach ($statement->fetchAll() as $language) {
    $languages[$language->language_id] = $language->code;
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
                false !== $title = $translate->string($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_description` SET `title` = ? WHERE `product_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_id, $untranslated->language_id));

                $total_translated++;
            }

            // Translate description
            if (empty($untranslated->description) &&
                false !== $description = $translate->string($translated->description, $languages[$translated->language_id], $languages[$untranslated->language_id])
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
                false !== $name = $translate->string($translated->name, $languages[$translated->language_id], $languages[$untranslated->language_id])
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
                false !== $title = $translate->string($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
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
                false !== $title = $translate->string($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
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
                false !== $title = $translate->string($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
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
                false !== $title = $translate->string($translated->title, $languages[$translated->language_id], $languages[$untranslated->language_id])
            ) {

                $update = $db->prepare("UPDATE `product_audio_description` SET `title` = ? WHERE `product_audio_id` = ? AND `language_id` = ? LIMIT 1");
                $update->execute(array($title, $untranslated->product_audio_id, $untranslated->language_id));

                $total_translated++;
            }
        }
    }
}


// Response
die(sprintf('Total translated: %s', $total_translated));