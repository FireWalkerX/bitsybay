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

class ControllerModuleNotification extends Controller {

    public function index() {

        // Load dependencies
        $this->load->model('account/notification');

        $data = array();

        $data['total_unread'] = $this->model_account_notification->getTotalNotifications($this->auth->getId(),
                                                                                         array('read' => 0));

        $data['total_all']    = $this->model_account_notification->getTotalNotifications($this->auth->getId());

        $data['href_account_notification']     = $this->url->link('account/notification');
        $data['href_account_notification_all'] = $this->url->link('account/notification', 'all=1');

        return $this->load->view('module/notification.tpl', $data);
    }
}
