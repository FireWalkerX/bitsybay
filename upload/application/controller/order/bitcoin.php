<?php

/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    BitsyBay Engine
 * @copyright  Copyright (c) 2015 The BitsyBay Project (https://github.com/bitsybay)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */

class ControllerOrderBitcoin extends Controller {

    public function __construct($registry) {

        parent::__construct($registry);

        // Load dependencies
        $this->load->model('catalog/product');
        $this->load->model('common/order');
        $this->load->model('account/user');

        $this->load->library('electrum');
    }

    public function index() {
        $this->security_log->write('Try to get empty method');
        exit;

    }


    // AJAX actions begin
    public function create() {

        // Only for logged users
        if (!$this->auth->isLogged()) {
            $this->security_log->write('Try to order product from guest request');
            exit;
        }

        // Check request
        if (!$this->request->isAjax()) {
            $this->security_log->write('Try to order product without ajax request');
            exit;
        }

        // Check dependencies
        if (!isset($this->request->post['product_id'])) {
            $this->security_log->write('Try to order product without product_id parameter');
            exit;
        }

        // Check dependencies
        if (!isset($this->request->post['license']) || !in_array($this->request->post['license'], array('regular', 'exclusive'))) {
            $this->security_log->write('Try to order product without license parameter');
            exit;
        }

        // Try to get product
        if (!$product_info = $this->model_catalog_product->getProduct((int) $this->request->post['product_id'], $this->language->getId(), $this->auth->getId(), ORDER_APPROVED_STATUS_ID)) {
            $this->security_log->write('Try to order not exists product');
            exit;
        }

        // Try to get denied product
        if (!$product_info->status) {
            $this->security_log->write('Try to order product ' . (int)$this->request->post['product_id'] . ' with status ' . $product_info->status);
            exit;
        }

        // Check if product already ordered
        if ($product_info->order_status_id == ORDER_APPROVED_STATUS_ID) {
            $this->security_log->write('Try to order ordered product');
            exit;
        }

        // Check if order self product
        if ($product_info->user_id == $this->auth->getId()) {
            $this->security_log->write('Try to order self product');
            exit;
        }

        // Check regular price
        if ($this->request->post['license'] == 'regular' && ($product_info->regular_price > 0 || $product_info->special_regular_price > 0)) {
            $amount = (float) $product_info->special_regular_price > 0 ? $product_info->special_regular_price : $product_info->regular_price;

        // Check exclusive price
        } else if ($this->request->post['license'] == 'exclusive' && ($product_info->exclusive_price > 0 || $product_info->special_exclusive_price > 0)) {
            $amount = (float) $product_info->special_exclusive_price > 0 ? $product_info->special_exclusive_price : $product_info->exclusive_price;

        // License parameter error
        } else {
            $this->security_log->write('Try to purchase product by undefined license');
            exit;
        }

        // Init variables
        $json = array('status' => false);

        // Create a new order in DB
        if (!$order_id = $this->model_common_order->createOrder($this->auth->getId(),
                                                                $product_info->product_id,
                                                                $this->request->post['license'],
                                                                $amount,
                                                                FEE_PER_ORDER,
                                                                ORDER_PENDING_STATUS_ID,
                                                                DEFAULT_CURRENCY_ID)) {

            $this->security_log->write('Can not create the order');
            exit;
        }

        // Generate label
        $label = sprintf('%s Order #%s', PROJECT_NAME, $order_id);

        // Get order address if exists
        $order_info = $this->model_common_order->getOrder($order_id);

        if ($order_info->address) {

            $address = $order_info->address;

        // Create a new BitCoin Address
        } else {

            try {
                $electrum = new Electrum(ELECTRUM_RPC_HOST, ELECTRUM_RPC_PORT);

                $response = $electrum->addrequest(
                    array(
                        'amount'     => $amount,
                        'memo'       => $label,
                        'force'      => true,
                    )
                );

                if (isset($response['result']['address'])) {

                    $address = $response['result']['address'];
                    $this->model_common_order->updateAddress($order_id, $address);

                } else {
                    $this->security_log->write($response);
                }

            } catch (Exception $e) {
                $this->security_log->write($e->getMessage());
            }
        }

        if (isset($address)) {
            $json = array(
                'status'  => true,
                'address' => $address,
                'amount'  => $amount,
                'label'   => $label,
                'text'    => sprintf(tt('Send %s or more to this address:'), $this->currency->format($amount)),
                'href'    => sprintf('bitcoin:%s?amount=%s&label=%s', $address, $amount, $label),
                'src'     => $this->url->link('common/image/qr', 'code=' . $address),
                'amounts' => array(
                    array(
                        'label'  => $this->currency->format($amount_1 = round($amount + ($amount * 10 / 100), 4)), // +10%
                        'amount' => $amount_1,
                        'href'   => sprintf('bitcoin:%s?amount=%s&label=%s', $address, $amount_1, $label),
                    ),
                    array(
                        'label'  => $this->currency->format($amount_2 = round($amount + ($amount * 25 / 100), 4)), // +25%
                        'amount' => $amount_2,
                        'href'   => sprintf('bitcoin:%s?amount=%s&label=%s', $address, $amount_2, $label),
                    ),
                    array(
                        'label'  => $this->currency->format($amount_3 = round($amount + ($amount * 50 / 100), 4)), // +50%
                        'amount' => $amount_3,
                        'href'   => sprintf('bitcoin:%s?amount=%s&label=%s', $address, $amount_3, $label),
                    ),
                    array(
                        'label'  => $this->currency->format($amount_4 = round($amount + ($amount * 100 / 100), 4)), // +100%
                        'amount' => $amount_4,
                        'href'   => sprintf('bitcoin:%s?amount=%s&label=%s', $address, $amount_4, $label),
                    ),
                )
            );
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
