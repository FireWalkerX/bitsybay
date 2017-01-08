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

final class Session {

    /**
     * @var int
     */
    private $_user_id        = 0;

    /**
     * @var array
     */
    private $_user_message   = array();

    /**
     * @var array
     */
    private $_captcha = false;

    public function __construct() {
        if (!session_id()) {
            ini_set('session.use_only_cookies', 'On');
            ini_set('session.use_trans_sid', 'Off');
            ini_set('session.cookie_httponly', 'On');

            session_set_cookie_params(0, '/');
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->setUserId($_SESSION['user_id']);
        }

        if (isset($_SESSION['user_message'])) {
            $this->setUserMessage($_SESSION['user_message']);
        }

        if (isset($_SESSION['captcha'])) {
            $this->setCaptcha($_SESSION['captcha']);
        }
    }

    /**
    * Set user id
    *
    * @param int $user_id
    * @return null
    */
    public function setUserId($user_id = 0) {
        $this->_user_id = $_SESSION['user_id'] = (int) $user_id;
    }

    /**
    * Set captcha code
    *
    * @param string $code
    * @return null
    */
    public function setCaptcha($code) {
        $this->_captcha = $_SESSION['captcha'] = $code;
    }

    /**
    * Set user messages
    *
    * To creating alerts, popup, etc
    *
    * @param array $user_message Formatted as alert_key => alert_value, alert_key => alert_value
    * @return null
    */
    public function setUserMessage(array $user_message = array()) {
        $this->_user_message = $_SESSION['user_message'] = (array) $user_message;
    }

    /**
    * Get user id
    *
    * @return int Returns user id
    */
    public function getUserId() {
        return $this->_user_id;
    }

    /**
    * Get captcha code
    *
    * @return int Returns stored captcha code
    */
    public function getCaptcha() {
        return $this->_captcha;
    }

    /**
    * Get user messages
    *
    * @return array Returns array, formatted as alert_key => alert_value, alert_key => alert_value
    */
    public function getUserMessage() {
        return $this->_user_message;
    }

    /**
    * Get session id
    *
    * @return string
    */
    public function getId() {
        return session_id();
    }

    /**
    * Destroy session
    *
    * @return bool
    */
    public function destroy() {
        return session_destroy();
    }
}
