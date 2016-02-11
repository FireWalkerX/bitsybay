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

    private $_bitcoin;
    private $_mail;

    private $_errors    = array();
    private $_languages = array();

    private $_count_pending     = 0;
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

        $this->load->library('bitcoin');

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

            $this->_bitcoin = new BitCoin(
                BITCOIN_RPC_USERNAME,
                BITCOIN_RPC_PASSWORD,
                BITCOIN_RPC_HOST,
                BITCOIN_RPC_PORT
            );

        } catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
        }
    }

    // Actions
    public function index() {

        /*************************************
         *
         * Process all orders except approved
         *
         *************************************/
        foreach ($this->model_common_order->getPendingOrders(DEFAULT_LANGUAGE_ID, ORDER_APPROVED_STATUS_ID) as $order) {

            $this->_count_pending++;

            // Generate address ID
            $address_id = BITCOIN_ORDER_PREFIX . $order->order_id;


            /*****************************
             *
             * Update processed orders
             *
             *****************************/

            // When transaction has a minimum confirmations
            if ((float) $this->_bitcoin->getreceivedbyaccount($address_id, BITCOIN_MIN_TRANSACTION_CONFIRMATIONS) >= (float) $order->amount) {


                // Set order status to approved
                if ($this->model_common_order->updateOrderStatus($order->order_id, ORDER_APPROVED_STATUS_ID)) $this->_count_approved++;


                // Add file quota bonus
                $this->model_account_user->addFileQuota($order->seller_user_id, QUOTA_BONUS_SIZE_PER_ORDER);


                /*****************************
                 *
                 * Funds withdrawal
                 *
                 *****************************/

                // Zero fees for all contributors
                if ($order->contributor == 1) {
                    $fund_profit   = (float) 0;
                    $seller_profit = (float) $order->amount;
                } else {
                    $fund_profit   = (float) $order->amount * FEE_PER_ORDER / 100;
                    $seller_profit = (float) $order->amount - $fund_profit;
                }

                // Withdraw seller profit
                if (!$transaction_id = $this->_bitcoin->sendtoaddress($order->withdraw_address, $seller_profit, sprintf('Order ID %s Payout', $order->order_id))) {
                    $this->_errors[] = sprintf('Withdrawal error: %s', $this->_bitcoin->error);
                }

                // Withdraw fund profit, if exists
                if ($fund_profit > 0 && $transaction_id = $this->_bitcoin->sendtoaddress(BITCOIN_FUND_ADDRESS, $fund_profit, sprintf('Order ID %s Profit', $order->order_id))) {
                    $this->_errors[] = sprintf("Withdrawal error: %s", $this->_bitcoin->error);
                }


                /************************************************
                 *
                 * Send cheers to seller via notification center
                 *
                 ************************************************/

                if ($seller_notification_id = $this->model_account_notification->addNotification($order->seller_user_id, 'activity')) {

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
                                $order->buyer_username,
                                $order->product_title
                            )
                        );
                    }
                }


                /************************************************
                 *
                 * Send cheers to buyer via notification center
                 *
                 ************************************************/

                if ($buyer_notification_id = $this->model_account_notification->addNotification($order->buyer_user_id, 'activity')) {

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
                                $order->product_title
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
                if ($this->model_account_subscription->checkUserSubscription($order->seller_user_id, PURCHASE_SUBSCRIPTION_ID)) {

                    // Load current language
                    $seller      = $this->model_account_user->getUser($order->seller_user_id);
                    $translation = $this->language->loadTranslation($seller->language_id);

                    // Prepare email template
                    $mail_data = array();

                    $mail_data['project_name'] = PROJECT_NAME;

                    $mail_data['subject'] = sprintf(tt('Your product has been purchased - %s', $translation), PROJECT_NAME);
                    $mail_data['message'] = sprintf(tt('@%s has purchased your product %s. Awesome!', $translation), $order->buyer_username, $order->product_title);

                    $mail_data['href_home']         = URL_BASE;
                    $mail_data['href_contact']      = URL_BASE . 'contact';
                    $mail_data['href_subscription'] = URL_BASE . 'subscriptions';

                    $mail_data['href_facebook'] = URL_FACEBOOK;
                    $mail_data['href_twitter']  = URL_TWITTER;
                    $mail_data['href_tumblr']   = URL_TUMBLR;
                    $mail_data['href_github']   = URL_GITHUB;

                    $this->_mail->setTo($order->seller_email);
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
                $buyer       = $this->model_account_user->getUser($order->buyer_user_id);
                $translation = $this->language->loadTranslation($buyer->language_id);

                $mail_data = array();

                $mail_data['project_name'] = PROJECT_NAME;

                $mail_data['subject'] = sprintf(tt('Your purchase has been confirmed - %s', $translation), PROJECT_NAME);
                $mail_data['message'] = sprintf(tt('%s purchase is ready to download. Cheers!', $translation), $order->product_title);

                $mail_data['href_home']         = URL_BASE;
                $mail_data['href_contact']      = URL_BASE . 'contact';
                $mail_data['href_subscription'] = URL_BASE . 'subscriptions';
                $mail_data['href_download']     = URL_BASE . 'search?purchased=1';

                $mail_data['href_facebook'] = URL_FACEBOOK;
                $mail_data['href_twitter']  = URL_TWITTER;
                $mail_data['href_tumblr']   = URL_TUMBLR;
                $mail_data['href_github']   = URL_GITHUB;

                $mail_data['module'] = $this->load->view('email/module/download.tpl', $mail_data);

                $this->_mail->setTo($order->buyer_email);
                $this->_mail->setSubject($mail_data['subject']);
                $this->_mail->setHtml($this->load->view('email/common.tpl', $mail_data));
                $this->_mail->send();

                continue;
            }


            /************************
             *
             * Update pending orders
             *
             ************************/

            // When order has been purchased and amount is correct
            if ((float) $this->_bitcoin->getreceivedbyaccount($address_id, 0) >= (float) $order->amount) {

                // Set order status to processed
                if ($this->model_common_order->updateOrderStatus($order->order_id, ORDER_PROCESSED_STATUS_ID)) $this->_count_processed++;


                /***********************************
                 *
                 * Send notice about money receiving
                 *
                 ***********************************/

                // Load current language
                $buyer       = $this->model_account_user->getUser($order->buyer_user_id);
                $translation = $this->language->loadTranslation($buyer->language_id);

                $mail_data = array();

                $mail_data['project_name'] = PROJECT_NAME;

                $mail_data['subject'] = sprintf(tt('Your payment has been received - %s', $translation), PROJECT_NAME);
                $mail_data['message'] = sprintf(tt('Please wait for %s confirmations. It may take some time. Thanks!', $translation), BITCOIN_MIN_TRANSACTION_CONFIRMATIONS);

                $mail_data['href_home']         = URL_BASE;
                $mail_data['href_contact']      = URL_BASE . 'contact';
                $mail_data['href_subscription'] = URL_BASE . 'subscriptions';

                $mail_data['href_facebook'] = URL_FACEBOOK;
                $mail_data['href_twitter']  = URL_TWITTER;
                $mail_data['href_tumblr']   = URL_TUMBLR;
                $mail_data['href_github']   = URL_GITHUB;

                $this->_mail->setTo($order->buyer_email);
                $this->_mail->setSubject($mail_data['subject']);
                $this->_mail->setHtml($this->load->view('email/common.tpl', $mail_data));
                $this->_mail->send();
            }
        }


        /*************************
         *
         * Notice admin via email
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
