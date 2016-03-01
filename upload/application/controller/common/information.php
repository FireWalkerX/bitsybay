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

class ControllerCommonInformation extends Controller {

    public function __construct($registry) {

        parent::__construct($registry);

        // Load dependencies
        $this->load->model('common/license');
        $this->load->helper('highlight');
    }

    public function about() {

        $this->document->setTitle(tt('About Us'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('About Us'), 'href' => $this->url->link('common/information/about'), 'active' => true),
        ));

        $this->response->setOutput($this->load->view('common/information/about.tpl', $data));
    }

    public function licenses() {

        $this->document->setTitle(tt('Licenses'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('Licensing Policy'), 'href' => $this->url->link('common/information/licenses'), 'active' => true),
        ));

        // Get all licenses
        $licenses = $this->model_common_license->getLicenses($this->language->getId());

        $data['licenses'] = array();
        foreach ($licenses as $license) {

            // Get license conditions
            $license_conditions = $this->model_common_license->getLicenseConditions($license->license_id, $this->language->getId());

            $conditions = array();
            foreach ($license_conditions as $license_condition) {

                if ($license_condition->optional) {

                    $condition = sprintf(
                        $license_condition->condition,
                        tt('may') . tt(' or ') . tt('shall not')
                    );

                    $conditions[$license_condition->license_condition_id] = highlight_license_condition($condition, tt('may'), tt('shall not'));
                } else {
                    $conditions[$license_condition->license_condition_id] = highlight_license_condition($license_condition->condition, tt('may'), tt('shall not'));
                }
            }

            // Merge
            $data['licenses'][$license->license_id] = array(
                'name'        => $license->name . ' ' . tt('License'),
                'description' => $license->description,
                'conditions'  => $conditions
            );
        }

        $this->response->setOutput($this->load->view('common/information/license.tpl', $data));
    }

    public function terms() {

        $this->document->setTitle(tt('Terms of Service'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('Terms of Service'), 'href' => $this->url->link('common/information/terms'), 'active' => true),
        ));

        $this->response->setOutput($this->load->view('common/information/terms.tpl', $data));
    }

    public function faq() {

        $this->document->setTitle(tt('General F.A.Q'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('General F.A.Q'), 'href' => $this->url->link('common/information/faq'), 'active' => true),
        ));

        $this->response->setOutput($this->load->view('common/information/faq.tpl', $data));
    }

    public function team() {

        $this->document->setTitle(tt('Team'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('Team'), 'href' => $this->url->link('common/information/team'), 'active' => true),
        ));


        // Contributors list
        $github = curl_init(GITHUB_API_URL_CONTRIBUTORS);
        curl_setopt($github, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($github, CURLOPT_USERAGENT, PROJECT_NAME);
        curl_setopt($github, CURLOPT_RETURNTRANSFER, true);

        $contributors = curl_exec($github);

        $data['contributions'] = 0;
        $data['contributors']  = array();

        if($contributors) {
            foreach (json_decode($contributors, true) as $contributor) {
                if (isset($contributor['id'])) {
                    $data['contributions'] += $contributor['contributions'];
                    $data['contributors'][] = array(
                        'username'      => $contributor['login'],
                        'href_avatar'   => $contributor['avatar_url'],
                        'href_profile'  => $contributor['html_url'],
                        'contributions' => $contributor['contributions']
                    );
                } else if (isset($contributor['message'])) {
                    $this->security_log->write($contributor['message']);
                }
            }
        }

        $this->response->setOutput($this->load->view('common/information/team.tpl', $data));
    }

    public function promo() {

        $this->document->setTitle(tt('Promotional Assets'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('Promotional Assets'), 'href' => $this->url->link('common/information/promo'), 'active' => true),
        ));


        $this->response->setOutput($this->load->view('common/information/promo.tpl', $data));
    }

    public function bitcoin() {

        $this->document->setTitle(tt('What is Bitcoin?'));

        $data['footer']         = $this->load->controller('common/footer');
        $data['header']         = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
            array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
            array('name' => tt('What is Bitcoin'), 'href' => $this->url->link('common/information/bitcoin'), 'active' => true),
        ));


        $this->response->setOutput($this->load->view('common/information/bitcoin.tpl', $data));
    }
}
