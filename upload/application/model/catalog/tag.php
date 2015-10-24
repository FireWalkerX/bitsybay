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

class ModelCatalogTag extends Model {

    /**
     * Add tag
     *
     * @return int|bool tag_id or FALSE if throw exception
     */
    public function addTag() {

        try {

            $statement = $this->db->prepare('INSERT INTO `tag` SET `date_added` = NOW()');
            $statement->execute();

            return $this->db->lastInsertId();

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
     * Add tag description
     *
     * @param int $tag_id
     * @param int $language_id
     * @param string $name
     * @return int|bool tag_description_id or FALSE if throw exception
     */
    public function addTagDescription($tag_id, $language_id, $name) {

        try {

            $statement = $this->db->prepare('INSERT INTO `tag_description` SET
                                            `tag_id`      = :tag_id,
                                            `language_id` = :language_id,
                                            `name`        = :name');

            $statement->execute(array(
                ':tag_id'      => $tag_id,
                ':language_id' => $language_id,
                ':name'        => $name));

            return $this->db->lastInsertId();

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
    * Get tag descriptions
    *
    * @param int $tag_id
    * @return array|bool Tag description rows or FALSE if throw exception
    */
    public function getTagDescriptions($tag_id) {

        try {
            $statement = $this->db->prepare('SELECT * FROM `tag_description` WHERE `tag_id` = ?');
            $statement->execute(array($tag_id));

            return $statement->rowCount() ? $statement->fetchAll() : array();

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
    * Get tag description by multilingual tag name
    *
    * @param string $name
    * @return array|false Tag description row or FALSE if throw exception
    */
    public function getTagByName($name) {

        try {
            $statement = $this->db->prepare('SELECT `tag_id` FROM `tag_description` WHERE `name` = ? LIMIT 1');
            $statement->execute(array($name));


            return $statement->rowCount() ? $statement->fetch(): false;

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
    * Get tags
    *
    * @param array $filter_data
    * @param int $language_id
    * @return array|bool Tag rows or FALSE if throw exception
    */
    public function getTags(array $filter_data = array(), $language_id) {

        $sql = 'SELECT `t`.`tag_id`,
                       `td`.`name`

                        FROM `tag` AS `t`
                        JOIN `tag_description` AS `td` ON (`t`.`tag_id` = `td`.`tag_id`)
                        LEFT JOIN `product_to_tag` AS `p2t` ON (`p2t`.`tag_id` = `t`.`tag_id`)
                        LEFT JOIN `product` AS `p` ON (`p`.`product_id` = `p2t`.`product_id`)
                        WHERE `td`.`language_id` = :language_id';

        if (isset($filter_data['category_id'])) {
            $sql .= ' AND `p`.`category_id` = ' . (int) $filter_data['category_id'];
        }

        $sql .= ' GROUP BY `t`.`tag_id` ';

        if (isset($filter_data['order']) && in_array(mb_strtolower($filter_data['order']), array('rand()'))) {
            $sql .= ' ORDER BY ' . $filter_data['order'];
        }

        if (isset($filter_data['limit'])) {
            $sql .= ' LIMIT ' . (int) $filter_data['limit'];
        }

        try {
            $statement = $this->db->prepare($sql);

            $statement->execute(array(':language_id' => $language_id));

            return $statement->rowCount() ? $statement->fetchAll() : array();

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }
}
