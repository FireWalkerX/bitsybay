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

class ModelCommonLanguage extends Model {

    /**
    * Get languages
    *
    * @param int $language_id
    * @return array|bool Languages rows or false if throw exception
    */
    public function getLanguages($language_id = 0) {

        try {
            $statement = $this->db->prepare('SELECT * FROM `language` ORDER BY `language_id` = ? DESC, `name` DESC');
            $statement->execute(array($language_id));

            if ($statement->rowCount()) {
                return $statement->fetchAll();
            } else {
                return array();
            }
        } catch (PDOException $e) {

            trigger_error($e->getMessage());
            return false;
        }
    }

    /**
    * Get language
    *
    * @param int $language_id
    * @return array|bool Language row or false if throw exception
    */
    public function getLanguage($language_id) {

        try {
            $statement = $this->db->prepare('SELECT * FROM `language` WHERE `language_id` = ? LIMIT 1');
            $statement->execute(array($language_id));

            if ($statement->rowCount()) {
                return $statement->fetch();
            } else {
                return array();
            }
        } catch (PDOException $e) {

            trigger_error($e->getMessage());
            return false;
        }
    }
}
