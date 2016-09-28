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

class ControllerCronManagerOrder extends Controller {

    private $_electrum;
    private $_mail;

    private $_errors    = array();
    private $_languages = array();

    private $_count_processed   = 0;
    private $_count_approved    = 0;

    public function __construct($registry) {

        parent::__construct($registry);

        // Validate request
        if ($this->request->getServerAddress() != $this->request->getRemoteAddress()) die(tt('Access denied'));

        // Load dependencies
        $this->load->model('common/language');
        $this->load->model('common/order');
        $this->load->model('account/user');
        $this->load->model('account/notification');
        $this->load->model('account/subscription');
        $this->load->model('catalog/product');

        $this->load->library('electrum');

        // Init languages
        foreach ($this->model_common_language->getLanguages() as $language) {
            $this->_languages[$language->language_id] = $language->code;
        }

        // Init mail
        $this->_mail = new Mail();
        $this->_mail->setFrom(MAIL_EMAIL_SUPPORT_ADDRESS);
        $this->_mail->setReplyTo(MAIL_EMAIL_SUPPORT_ADDRESS);
        $this->_mail->setSender(MAIL_EMAIL_SENDER_NAME);

        // Init BitCoin
        try {
            $this->_electrum = new Electrum(ELECTRUM_RPC_HOST, ELECTRUM_RPC_PORT);
        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
        }
    }

    public function index() {


        /************************
         *
         * Update pending orders
         *
         ************************/

        foreach ($this->model_common_order->getOrdersByStatus(ORDER_PENDING_STATUS_ID) as $order) {

            // Get balance by address
            $response = $this->_electrum->getaddressbalance($order->address);

            if (!isset($response['result']['unconfirmed']) || !$order->address) {
                continue;
            }

            // When order has been purchased and amount is correct
            if ((float) $response['result']['unconfirmed'] >= (float) $order->amount) {

                // Processed counter
                $this->_count_processed++;

                // Get buyer info
                $buyer = $this->model_account_user->getUser($order->user_id);

                // Set order status to processed
                $this->model_common_order->updateOrderStatus($order->order_id, ORDER_PROCESSED_STATUS_ID);


                /***********************************
                 *
                 * Send notice about money receiving
                 *
                 ***********************************/

                // Load current language
                $translation = $this->language->loadTranslation($buyer->language_id);

                $mail_data = array();

                $mail_data['translation']  = $translation;

                $mail_data['project_name'] = PROJECT_NAME;

                $mail_data['subject'] = sprintf(tt('Your payment has been received - %s', $translation), PROJECT_NAME);
                $mail_data['message'] = tt('Please wait for confirmation. It may take some time. Thanks!', $translation);

                $mail_data['href_home']         = URL_BASE;
                $mail_data['href_contact']      = URL_BASE . 'contact';
                $mail_data['href_subscription'] = URL_BASE . 'subscriptions';

                $mail_data['href_facebook'] = URL_FACEBOOK;
                $mail_data['href_twitter']  = URL_TWITTER;
                $mail_data['href_tumblr']   = URL_TUMBLR;
                $mail_data['href_github']   = URL_GITHUB;

                $this->_mail->setTo($buyer->email);
                $this->_mail->setSubject($mail_data['subject']);
                $this->_mail->setHtml($this->load->view('email/common.tpl', $mail_data));
                $this->_mail->send();
            }
        }


        /*****************************
         *
         * Update processed orders
         *
         *****************************/
        foreach ($this->model_common_order->getOrdersByStatus(ORDER_PROCESSED_STATUS_ID) as $order) {

            // Get balance by address
            $response = $this->_electrum->getaddressbalance($order->address);

            if (!isset($response['result']['unconfirmed']) || !$order->address) {
                continue;
            }

            // When transaction has a minimum confirmations
            if ((float) $response['result']['confirmed'] >= (float) $order->amount) {

                // Get confirmed amount
                $amount = (float) $response['result']['confirmed'];

                // Approved counter
                $this->_count_approved++;

                // Get product withdraw address
                $product_withdraw_address = $this->model_catalog_product->getWithdrawAddress($order->product_id);

                // Get product withdraw address
                $product_user_id = $this->model_catalog_product->getUserId($order->product_id);

                // Get product descriptions
                $product_descriptions = array(); foreach ($this->model_catalog_product->getProductDescriptions($order->product_id) as $product_description) {
                    $product_descriptions[$product_description->language_id] = array(
                        'title' => $product_description->title,
                    );
                }

                // Get seller info
                $seller = $this->model_account_user->getUser($product_user_id);

                // Get buyer info
                $buyer = $this->model_account_user->getUser($order->user_id);

                // Set order status to approved
                $this->model_common_order->updateOrderStatus($order->order_id, ORDER_APPROVED_STATUS_ID);

                // Add file quota bonus
                $this->model_account_user->addFileQuota($seller->user_id, QUOTA_BONUS_SIZE_PER_ORDER);


                /*****************************
                 *
                 * Funds withdrawal
                 *
                 *****************************/

                // Zero fees for all contributors
                if ($seller->contributor) {
                    $fund_profit   = (float) 0;
                    $seller_profit = (float) $amount;
                } else {
                    $fund_profit   = (float) $amount * FEE_PER_ORDER / 100;
                    $seller_profit = (float) $amount - $fund_profit;
                }

                // Withdraw seller profit
                $this->_electrum->payto(
                    array(
                        'amount'      => $seller_profit,
                        'destination' => $product_withdraw_address
                    )
                );

                // Withdraw fund profit, if exists
                if ($fund_profit > 0 ) {
                    $this->_electrum->payto(
                        array(
                            'amount'      => $fund_profit,
                            'destination' => BITCOIN_FUND_ADDRESS
                        )
                    );
                }

                /************************************************
                 *
                 * Send cheers to seller via notification center
                 *
                 ************************************************/

                if ($seller_notification_id = $this->model_account_notification->addNotification($seller->user_id, 'activity')) {

                    // Add notification description for each system language
                    foreach ($this->_languages as $language_id => $code) {

                        // Load current language
                        $translation = $this->language->loadTranslation($language_id);

                        // Generate multilingual translations
                        $this->model_account_notification->addNotificationDescription(
                            $seller_notification_id,
                            $language_id,
                            tt('Your product has been purchased', $translation),
                            sprintf(
                                tt('@%s has purchased your product %s. Awesome!', $translation),
                                $buyer->username,
                                $product_descriptions[$language_id]['title']
                            )
                        );
                    }
                }


                /************************************************
                 *
                 * Send cheers to buyer via notification center
                 *
                 ************************************************/

                if ($buyer_notification_id = $this->model_account_notification->addNotification($buyer->user_id, 'activity')) {

                    // Add notification description for each system language
                    foreach ($this->_languages as $language_id => $code) {

                        // Load current language
                        $translation = $this->language->loadTranslation($language_id);

                        // Generate multilingual translations
                        $this->model_account_notification->addNotificationDescription(
                            $buyer_notification_id,
                            $language_id,
                            tt('Your purchase has been confirmed', $translation),
                            sprintf(
                                tt('%s purchase is ready to download. Cheers!', $translation),
                                $product_descriptions[$language_id]['title']
                            )
                        );
                    }
                }


                /**********************************
                 *
                 * Send cheers to seller via email
                 *
                 **********************************/

                // Check seller subscription
                if ($this->model_account_subscription->checkUserSubscription($seller->user_id, PURCHASE_SUBSCRIPTION_ID)) {

                    // Load current language
                    $translation = $this->language->loadTranslation($seller->language_id);

                    // Prepare email template
                    $mail_data = array();

                    $mail_data['translation']  = $translation;

                    $mail_data['project_name'] = PROJECT_NAME;

                    $mail_data['subject'] = sprintf(tt('Your product has been purchased - %s', $translation), PROJECT_NAME);
                    $mail_data['message'] = sprintf(tt('@%s has purchased your product %s. Awesome!', $translation), $buyer->username, $product_descriptions[$seller->language_id]['title']);

                    $mail_data['href_home']         = URL_BASE;
                    $mail_data['href_contact']      = URL_BASE . 'contact';
                    $mail_data['href_subscription'] = URL_BASE . 'subscriptions';

                    $mail_data['href_facebook'] = URL_FACEBOOK;
                    $mail_data['href_twitter']  = URL_TWITTER;
                    $mail_data['href_tumblr']   = URL_TUMBLR;
                    $mail_data['href_github']   = URL_GITHUB;

                    $this->_mail->setTo($seller->email);
                    $this->_mail->setSubject($mail_data['subject']);
                    $this->_mail->setHtml($this->load->view('email/common.tpl', $mail_data));
                    $this->_mail->send();
                }


                /**********************************
                 *
                 * Send cheers to buyer via email
                 *
                 **********************************/

                // Load current language
                $translation = $this->language->loadTranslation($buyer->language_id);

                $mail_data = array();

                $mail_data['translation']  = $translation;

                $mail_data['project_name'] = PROJECT_NAME;

                $mail_data['subject'] = sprintf(tt('Your purchase has been confirmed - %s', $translation), PROJECT_NAME);
                $mail_data['message'] = sprintf(tt('%s purchase is ready to download. Cheers!', $translation), $product_descriptions[$buyer->language_id]['title']);

                $mail_data['href_home']         = URL_BASE;
                $mail_data['href_contact']      = URL_BASE . 'contact';
                $mail_data['href_subscription'] = URL_BASE . 'subscriptions';
                $mail_data['href_download']     = URL_BASE . 'search?purchased=1';

                $mail_data['href_facebook'] = URL_FACEBOOK;
                $mail_data['href_twitter']  = URL_TWITTER;
                $mail_data['href_tumblr']   = URL_TUMBLR;
                $mail_data['href_github']   = URL_GITHUB;

                $mail_data['module'] = $this->load->view('email/module/download.tpl', $mail_data);

                $this->_mail->setTo($buyer->email);
                $this->_mail->setSubject($mail_data['subject']);
                $this->_mail->setHtml($this->load->view('email/common.tpl', $mail_data));
                $this->_mail->send();
            }
        }


        /*************************
         *
         * Report admin via email
         *
         *************************/

        $report = array();

        if ($this->_errors) {
            $report []= implode("\n", $this->_errors);
        }

        if ($this->_count_processed) {
            $report []= sprintf("Orders processed: %s", $this->_count_processed);
        }

        if ($this->_count_approved) {
            $report []= sprintf("Orders approved: %s", $this->_count_approved);
        }

        if ($report) {
            $this->_mail->setTo(MAIL_EMAIL_BILLING_ADDRESS);
            $this->_mail->setSubject(sprintf('%s REPORT', PROJECT_NAME));
            $this->_mail->setHtml(false);
            $this->_mail->setText(implode("\n", $report));
            $this->_mail->send();
        }
    }
}
