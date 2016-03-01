<?php

class ToolLocalisationDatabase {
    
    private $_db;
    private $_language_id_from;
    private $_language_id_to;
    
    public function __construct($language_id_from, $language_id_to) {

        $this->_language_id_from = $language_id_from;
        $this->_language_id_to   = $language_id_to;

        try {
            $this->_db = new PDO(
                'mysql:dbname=' . DB_DATABASE . ';host=' . DB_HOSTNAME . ';charset=utf8',
                DB_USERNAME,
                DB_PASSWORD,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                )
            );

            $this->_db->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->_db->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_OBJ
            );

        } catch(PDOException $e) {
            $error[] = $e->getMessage();
            exit;
        }
        
    }
    
    public function addProductDescriptions() {
        
        $query = $this->_db->prepare("SELECT * FROM `product` AS `p` JOIN `product_description` AS `pd` ON (`pd`.`product_id` = `p`.`product_id`) WHERE `pd`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $product) {

                $insert = $this->_db->prepare("INSERT INTO `product_description` SET `language_id` = ?, `product_id` = ?, `title` = ?, `description` = ?");
                $insert->execute(array($this->_language_id_to, $product->product_id, $product->title, $product->description));
            }
        }
    }

    public function addProductDemoDescriptions() {

        $query = $this->_db->prepare("SELECT * FROM `product_demo` AS `pd` JOIN `product_demo_description` AS `pdd` ON (`pdd`.`product_demo_id` = `pd`.`product_demo_id`) WHERE `pdd`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $product_demo) {

                $insert = $this->_db->prepare("INSERT INTO `product_demo_description` SET `language_id` = ?, `product_demo_id` = ?, `title` = ?");
                $insert->execute(array($this->_language_id_to, $product_demo->product_demo_id, $product_demo->title));
            }
        }
    }

    public function addProductImageDescriptions() {

        $query = $this->_db->prepare("SELECT * FROM `product_image` AS `pi` JOIN `product_image_description` AS `pid` ON (`pid`.`product_image_id` = `pi`.`product_image_id`) WHERE `pid`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $product_image) {

                $insert = $this->_db->prepare("INSERT INTO `product_image_description` SET `language_id` = ?, `product_image_id` = ?, `title` = ?");
                $insert->execute(array($this->_language_id_to, $product_image->product_image_id, $product_image->title));
            }
        }
    }

    public function addProductVideoDescriptions() {

        $query = $this->_db->prepare("SELECT * FROM `product_video` AS `pv` JOIN `product_video_description` AS `pvd` ON (`pvd`.`product_video_id` = `pv`.`product_video_id`) WHERE `pvd`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $product_video) {

                $insert = $this->_db->prepare("INSERT INTO `product_video_description` SET `language_id` = ?, `product_video_id` = ?, `title` = ?");
                $insert->execute(array($this->_language_id_to, $product_video->product_video_id, $product_video->title));
            }
        }
    }

    public function addAudioVideoDescriptions() {

        $query = $this->_db->prepare("SELECT * FROM `product_audio` AS `pa` JOIN `product_audio_description` AS `pad` ON (`pad`.`product_audio_id` = `pa`.`product_audio_id`) WHERE `pad`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $product_audio) {

                $insert = $this->_db->prepare("INSERT INTO `product_audio_description` SET `language_id` = ?, `product_audio_id` = ?, `title` = ?");
                $insert->execute(array($this->_language_id_to, $product_audio->product_audio_id, $product_audio->title));
            }
        }
    }

    public function addTagDescriptions() {

        $query = $this->_db->prepare("SELECT * FROM `tag` AS `t` JOIN `tag_description` AS `td` ON (`td`.`tag_id` = `t`.`tag_id`) WHERE `td`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $tag) {

                $insert = $this->_db->prepare("INSERT INTO `tag_description` SET `language_id` = ?, `tag_id` = ?, `name` = ?");
                $insert->execute(array($this->_language_id_to, $tag->tag_id, $tag->name));
            }
        }
    }

    public function addUserNotificationDescriptions() {

        $query = $this->_db->prepare("SELECT * FROM `user_notification` AS `un` JOIN `user_notification_description` AS `und` ON (`un`.`user_notification_id` = `und`.`user_notification_id`) WHERE `und`.`language_id` = ?");
        $query->execute(array($this->_language_id_from));

        if ($query->rowCount()) {

            foreach ($query->fetchAll() as $notification) {

                $insert = $this->_db->prepare("INSERT INTO `user_notification_description` SET `language_id` = ?, `user_notification_id` = ?, `title` = ?, `description` = ?");
                $insert->execute(array($this->_language_id_to, $notification->user_notification_id, $notification->title, $notification->description));
            }
        }
    }
}