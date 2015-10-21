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

class ModelCommonLicense extends Model {

    /**
     * Get all licenses
     *
     * @param int $language_id
     * @return array|bool licenses or FALSE if throw exception
     */
    public function getLicenses($language_id) {

        try {

            $statement = $this->db->prepare('SELECT * FROM `license` AS `l`
                                                      JOIN `license_description` AS `ld` ON (`ld`.`license_id` = `l`.`license_id`)

                                                      WHERE `ld`.`language_id` = :language_id
                                                      ORDER BY `l`.`sort_order` ASC');
            $statement->execute(
                array(
                    ':language_id' => $language_id
                )
            );

            if ($statement->rowCount()) {
                return $statement->fetchAll();
            } else {
                return array();
            }

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
     * Get license conditions
     *
     * @param int $license_id
     * @param int $language_id
     * @return array|bool licenses or FALSE if throw exception
     */
    public function getLicenseConditions($license_id, $language_id) {

        try {

            $statement = $this->db->prepare('SELECT * FROM `license_condition` AS `lc`
                                                      JOIN `license_condition_description` AS `lcd` ON (`lcd`.`license_condition_id` = `lc`.`license_condition_id`)

                                                      WHERE `lc`.`license_id`   = :license_id
                                                      AND   `lcd`.`language_id` = :language_id
                                                      ORDER BY `lc`.`sort_order` ASC');
            $statement->execute(
                array(
                    ':license_id'  => $license_id,
                    ':language_id' => $language_id
                )
            );

            if ($statement->rowCount()) {
                return $statement->fetchAll();
            } else {
                return array();
            }

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());

            return false;
        }
    }

    /**
     * Get license condition
     *
     * @param int $license_condition_id
     * @return array|bool license condition row or false if throw exception
     */
    public function getLicenseCondition($license_condition_id) {

        try {
            $statement = $this->db->prepare('SELECT * FROM `license_condition` WHERE `license_condition_id` = ? LIMIT 1');
            $statement->execute(array($license_condition_id));

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
