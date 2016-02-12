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

class ModelCommonOrder extends Model {

    /**
    * Create an order
    *
    * @param int $user_id
    * @param int $product_id
    * @param string $license
    * @param float $amount
    * @param int $fee
    * @param int $order_status_id
    * @param int $currency_id
    * @return array|bool last insert id or available id or false if throw exception
    */
    public function createOrder($user_id, $product_id, $license, $amount, $fee, $order_status_id, $currency_id) {

        try {

            // Check if configuration is exist
            $statement = $this->db->prepare('   SELECT `order_id`
                                                FROM   `order`
                                                WHERE  `user_id` = ?
                                                AND    `product_id` = ?
                                                AND    `license` = ?
                                                AND    `amount` = ?
                                                AND    `fee` = ?
                                                LIMIT 1');

            $statement->execute(array($user_id, $product_id, $license, $amount, $fee));

            if ($statement->rowCount()) {
                $order_info = $statement->fetch();
                return $order_info->order_id;

            // Insert if configuration is not exist
            } else {
                $statement = $this->db->prepare('INSERT IGNORE
                                                 INTO  `order`
                                                 SET   `user_id` = ?,
                                                       `product_id` = ?,
                                                       `license` = ?,
                                                       `order_status_id` = ?,
                                                       `amount` = ?,
                                                       `fee` = ?,
                                                       `currency_id` = ?,
                                                       `date_added` = NOW()');

                $statement->execute(
                    array(
                        $user_id,
                        $product_id,
                        $license,
                        $order_status_id,
                        $amount,
                        $fee,
                        $currency_id
                    )
                );

                return $this->db->lastInsertId();
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
    * Update order status
    *
    * @param int $order_status_id
    * @param int $order_id
    * @return array|bool affected rows or false if throw exception
    */
    public function updateOrderStatus($order_id, $order_status_id) {

        try {

            $statement = $this->db->prepare('UPDATE `order` SET `order_status_id` = ? WHERE `order_id` = ? LIMIT 1');
            $statement->execute(array($order_status_id, $order_id));

            return $statement->rowCount();

        } catch (PDOException $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            trigger_error($e->getMessage());
            return false;
        }
    }

    /**
    * Get customer's order status by product_id
    *
    * @param int $product_id
    * @param int $user_id
    * @return int|bool order_status_id or false if throw exception
    */
    public function getOrderStatus($product_id, $user_id) {

        try {

            $statement = $this->db->prepare('SELECT `order_status_id`
                                             FROM   `order`
                                             WHERE  `product_id` = ?
                                             AND    `user_id` = ?
                                             LIMIT 1');

            $statement->execute(array($product_id, $user_id));

            if ($statement->rowCount()) {

                $order_info = $statement->fetch();

                return $order_info->order_status_id;
            } else {
                return 0;
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
    * Get order info
    *
    * @param int $order_id
    * @return object|bool Order PDOStatement::fetch object or false if throw exception
    */
    public function getOrder($order_id) {
        try {
            $statement = $this->db->prepare('SELECT * FROM `order` WHERE `order_id` = ? LIMIT 1');
            $statement->execute(array($order_id));

            if ($statement->rowCount()) {
                return $statement->fetch();
            } else {
                return false;
            }
        } catch (PDOException $e) {

            trigger_error($e->getMessage());
            return false;
        }
    }


    /**
    * Get orders by status
    *
    * @param int $status_id
    * @return object|array Order PDOStatement::fetchAll object or array if throw exception
    */
    public function getOrdersByStatus($status_id) {

        try {

            // Get pending orders
            $statement = $this->db->prepare('SELECT *
                                             FROM   `order`
                                             WHERE  `order_status_id` = ?
                                             AND    `product_id` IS NOT NULL -- may be removed by author --');

            $statement->execute(array($status_id));

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

}
