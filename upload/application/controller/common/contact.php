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

class ControllerCommonContact extends Controller {

    private $_error = array();

    public function __construct($registry) {

        parent::__construct($registry);

        $this->load->library('captcha/captcha');
    }

    public function index() {

        $data['email']   = isset($this->request->post['email']) ? $this->request->post['email'] : ($this->auth->isLogged() ? $this->auth->getEmail() : false);
        $data['subject'] = isset($this->request->post['subject']) ? $this->request->post['subject'] : false;
        $data['message'] = isset($this->request->post['message']) ? $this->request->post['message'] : false;

        if ('POST' == $this->request->getRequestMethod() && $this->_validatePost()) {

            $this->mail->setTo(MAIL_EMAIL_SUPPORT_ADDRESS);
            $this->mail->setFrom($this->request->post['email']);
            $this->mail->setSender($this->request->post['email']);
            $this->mail->setSubject($this->request->post['subject']);
            $this->mail->setText($this->request->post['message']);
            $this->mail->send();

            $this->session->setUserMessage(array('success' => tt('Your message was sent successfully!')));

            $data['subject'] = false;
            $data['message'] = false;
        }

        $this->document->setTitle(tt('Contact Us'));

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('Contact Us'), 'href' => $this->url->link('common/contact'), 'active' => true),
        ));

        $data['error']  = $this->_error;

        $data['href_common_information_licenses']  = $this->url->link('common/information/licenses');
        $data['href_common_information_terms']     = $this->url->link('common/information/terms');
        $data['href_common_information_faq']       = $this->url->link('common/information/faq');

        $captcha = new Captcha();
        $this->session->setCaptcha($captcha->getCode());

        $data['captcha'] = $this->url->link('common/contact/captcha');

        $data['action']  = $this->url->link('common/contact');

        $data['alert_success']  = $this->load->controller('common/alert/success');

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('common/contact.tpl', $data));
    }

    public function captcha() {
        $captcha = new Captcha();
        $captcha->getImage($this->session->getCaptcha());
    }

    private function _validatePost() {

        if (!isset($this->request->post['email']) || empty($this->request->post['email'])) {
            $this->_error['email'] = tt('Email is required');
        }

        if (!isset($this->request->post['subject']) || empty($this->request->post['subject'])) {
            $this->_error['subject'] = tt('Subject is required');
        }

        if (!isset($this->request->post['message']) || empty($this->request->post['message'])) {
            $this->_error['message'] = tt('Message is required');
        }

        if (!isset($this->request->post['captcha']) || empty($this->request->post['captcha'])) {
            $this->_error['captcha'] = tt('Magic word is required');
        } else if (strtoupper($this->request->post['captcha']) != strtoupper($this->session->getCaptcha())) {
            $this->_error['captcha'] = tt('Incorrect magic word');
        }

        return !$this->_error;
    }
}
