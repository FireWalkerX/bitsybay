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

class ControllerAccountProduct extends Controller {

    private $_error = array();

    public function __construct($registry) {

        parent::__construct($registry);

        // Load dependencies
        $this->load->model('common/language');
        $this->load->model('common/currency');
        $this->load->model('common/redirect');
        $this->load->model('common/license');

        $this->load->model('account/user');

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/tag');

        $this->load->helper('validator/product');
        $this->load->helper('validator/upload');
        $this->load->helper('validator/bitcoin');

        $this->load->helper('filter/uri');
        $this->load->helper('highlight');

        $this->load->library('color');
        $this->load->library('ffmpeg');
        $this->load->library('identicon');
        $this->load->library('translate');

    }

    // Route actions begin
    public function index() {

        // Redirect to login page if user is not logged
        if (!$this->auth->isLogged()) {
            $this->response->redirect($this->url->link('account/account/login', 'redirect=' . urlencode($this->url->link('account/product'))));
        }

        $data = array();

        $this->document->setTitle(tt('All products'));

        $data['href_account_product_create'] = $this->url->link('account/product/create');

        $data['alert_success']  = $this->load->controller('common/alert/success');
        $data['alert_danger']   = $this->load->controller('common/alert/danger');

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['products'] = array();

        $filter_data = array('user_id' => $this->auth->getId());


        $products = $this->model_catalog_product->getProducts($filter_data, $this->language->getId(), $this->auth->getId(), ORDER_APPROVED_STATUS_ID);

        // Skip unnecessary clicks
        if (!$products) {
            $this->response->redirect($this->url->link('account/product/create'));
        }

        foreach ($products as $product) {
            $data['products'][] = array(
                'product_id'              => $product->product_id,
                'title'                   => $product->title,
                'image'                   => $this->cache->image($product->main_product_image_id, $product->user_id, 36, 36),
                'date_added'              => date(tt('Y.m.d'), strtotime($product->date_added)),
                'special_regular_price'   => $product->special_regular_price ? $this->currency->format($product->special_regular_price, $product->currency_id) : 0,
                'special_exclusive_price' => $product->special_exclusive_price ? $this->currency->format($product->special_exclusive_price, $product->currency_id) : 0,
                'regular_price'           => $this->currency->format($product->regular_price, $product->currency_id),
                'exclusive_price'         => $this->currency->format($product->exclusive_price, $product->currency_id),
                'regular_status'          => $product->special_regular_price > 0 || $product->regular_price > 0 ? true : false,
                'exclusive_status'        => $product->special_exclusive_price > 0 || $product->exclusive_price > 0 ? true : false,
                'sales'                   => $product->sales,
                'favorites'               => $product->favorites,
                'viewed'                  => $product->viewed,
                'status'                  => $product->status,
                'href_edit'               => $this->url->link('account/product/update', 'product_id=' . $product->product_id),
                'href_delete'             => $this->url->link('account/product/delete', 'product_id=' . $product->product_id),
                'href_download'           => $this->url->link('catalog/product/download', 'product_id=' . $product->product_id),
                'href_view'               => $this->url->link('catalog/product', 'product_id=' . $product->product_id)
            );
        }

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
                    array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
                    array('name' => tt('Product list'), 'href' => $this->url->link('account/product'), 'active' => true)
            ));

        // Renter the template
        $this->response->setOutput($this->load->view('account/product/product_list.tpl', $data));
    }

    public function create() {

        // Redirect to login page if user is not logged
        if (!$this->auth->isLogged()) {
            $this->response->redirect($this->url->link('account/account/login',
                                                       'redirect=' . urlencode($this->url->link('account/product/create'))));
        }

        if ('POST' == $this->request->getRequestMethod() && $this->_validateProductForm()) {

            // Load dependencies
            $translate = new Translate();
            $color     = new Color();

            // Create languages registry
            $languages = array(); foreach ($this->model_common_language->getLanguages() as $language) $languages[$language->language_id] = $language->code;

            // Set active directory
            $directory = DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR;

            // Start transaction
            $this->db->beginTransaction();

            // Add product
            $product_id = $this->model_catalog_product->createProduct(  $this->auth->getId(),
                                                                        $this->request->post['category_id'],
                                                                        $this->request->post['currency_id'],
                                                                        $this->request->post['regular_price'],
                                                                        $this->request->post['exclusive_price'],
                                                                        $this->request->post['withdraw_address'],
                                                                        FilterUri::alias($this->request->post['product_description'][$this->language->getId()]['title']),
                                                                        (int) $this->auth->isVerified());

            // Add product description
            foreach ($this->request->post['product_description'] as $language_id => $product_description) {
                 $this->model_catalog_product->createProductDescription($product_id,
                                                                        $language_id,
                                                                        (empty(trim($product_description['title']))       ? $translate->string($this->request->post['product_description'][$this->language->getId()]['title'],       $this->language->getCode(), $languages[$language_id]) : $product_description['title']),
                                                                        (empty(trim($product_description['description'])) ? $translate->string($this->request->post['product_description'][$this->language->getId()]['description'], $this->language->getCode(), $languages[$language_id]) : $product_description['description']));
            }

            // Add Tags
            foreach ($this->request->post['product_description'] as $language_id => $product_description) {

                // Process current language not empty field only
                if (!empty($product_description['tags']) && $language_id == $this->language->getId()) {

                    // Separate a tags string and create multilingual registry
                    foreach (explode(',', $product_description['tags']) as $name) {

                        // Get tag id
                        $name = mb_strtolower(trim($name));

                        // Saved tags registry
                        if ($tag = $this->model_catalog_tag->getTagByName($name)) {

                            $tag_id = $tag->tag_id;

                        } else {

                            // Create new tag
                            $tag_id = $this->model_catalog_tag->addTag();

                            // Create descriptions for each language
                            foreach ($languages as $language_id => $code) {
                                $this->model_catalog_tag->addTagDescription($tag_id,
                                                                            $language_id,
                                                                            $translate->string($name, $this->language->getCode(), $code));
                            }
                        }

                        // Save new relations
                        $this->model_catalog_product->addProductToTag($product_id, $tag_id);
                    }
                }
            }

            // Add file
            if ($file_content = file_get_contents($directory . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION)) {

                $product_file_id = $this->model_catalog_product->createProductFile( $product_id,
                                                                                    md5($file_content),
                                                                                    sha1($file_content));
                rename(
                    $directory . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION,
                    $directory . $product_file_id . '.' . STORAGE_FILE_EXTENSION
                );
            }

            // Add demos
            if (isset($this->request->post['demo'])) {
                foreach ($this->request->post['demo'] as $row => $demo) {
                    $product_demo_id = $this->model_catalog_product->createProductDemo( $product_id,
                                                                                        $demo['sort_order'],
                                                                                        $demo['url'],
                                                                                        $this->request->post['main_demo'] == $row ? 1 : 0);

                    foreach ($demo['title'] as $language_id => $title) {
                        $this->model_catalog_product->createProductDemoDescription( $product_demo_id,
                                                                                    $language_id,
                                                                                    (empty(trim($title)) ? $translate->string($demo['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }
                }
            }

            // Add images
            if (isset($this->request->post['image'])) {
                foreach ($this->request->post['image'] as $row => $image) {

                        // Create product image
                        $product_image_id = $this->model_catalog_product->createProductImage($product_id,
                                                                                             $image['sort_order'],
                                                                                             $this->request->post['main_image'] == $row ? 1 : 0,
                                                                                             isset($image['watermark']) ? 1 : 0);

                        // Generate image titles
                        foreach ($image['title'] as $language_id => $title) {
                            $this->model_catalog_product->createProductImageDescription($product_image_id,
                                                                                        $language_id,
                                                                                        (empty(trim($title)) ? $translate->string($image['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                        }

                        // Extract image colors
                        if ($color->setImage($directory . $image['product_image_id'] . '.' . STORAGE_IMAGE_EXTENSION) && $colors = $color->getColors()) {
                            foreach ($colors as $key => $value) {

                                $this->model_catalog_product->createProductImageColor(  $product_image_id,
                                                                                        $value['hex'],
                                                                                        $value['hue'],
                                                                                        $value['saturation'],
                                                                                        $value['value'],
                                                                                        $value['red'],
                                                                                        $value['green'],
                                                                                        $value['blue'],
                                                                                        $value['frequency']);
                            }
                        }

                        // Rename temporary file
                        rename(
                            $directory . $image['product_image_id'] . '.' . STORAGE_IMAGE_EXTENSION,
                            $directory . $product_image_id . '.' . STORAGE_IMAGE_EXTENSION
                        );

                }

            // Generate unique image if others images is not exists
            } else {

                $product_image_id = $this->model_catalog_product->createProductImage($product_id, 1, 1, 0, 1);

                // Generate image titles from product title
                foreach ($this->request->post['product_description'] as $language_id => $product_description) {
                     $this->model_catalog_product->createProductImageDescription($product_image_id,
                                                                                 $language_id,
                                                                                 (empty(trim($product_description['title'])) ? $translate->string($this->request->post['product_description'][$this->language->getId()]['title'], $this->language->getCode(), $languages[$language_id]) : $product_description['title']));
                }

                $identicon = new Identicon();
                $image     = new Image($identicon->generateImageResource(sha1($product_id),
                                                                         PRODUCT_IMAGE_ORIGINAL_WIDTH,
                                                                         PRODUCT_IMAGE_ORIGINAL_HEIGHT), true);

                $image->save(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $product_image_id . '.' . STORAGE_IMAGE_EXTENSION);
            }

            // Add videos
            if (isset($this->request->post['video'])) {
                foreach ($this->request->post['video'] as $video) {

                    $product_video_id = $this->model_catalog_product->createProductVideo($product_id,
                                                                                        (isset($video['reduce']) ? 1 : 0),
                                                                                        $video['sort_order']);

                    foreach ($video['title'] as $language_id => $title) {
                        $this->model_catalog_product->createProductVideoDescription($product_video_id,
                                                                                    $language_id,
                                                                                    (empty(trim($title)) ? $translate->string($video['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }

                    rename(
                        $directory . $video['product_video_id'] . '.' . STORAGE_VIDEO_EXTENSION,
                        $directory . $product_video_id . '.' . STORAGE_VIDEO_EXTENSION
                    );
                }
            }

            // Add audios
            if (isset($this->request->post['audio'])) {
                foreach ($this->request->post['audio'] as $audio) {

                    $product_audio_id = $this->model_catalog_product->createProductAudio($product_id,
                                                                                        (isset($audio['cut']) ? 1 : 0),
                                                                                         $audio['sort_order']);

                    foreach ($audio['title'] as $language_id => $title) {
                         $this->model_catalog_product->createProductAudioDescription($product_audio_id,
                                                                                     $language_id,
                                                                                     (empty(trim($title)) ? $translate->string($audio['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }

                    rename(
                        $directory . $audio['product_audio_id'] . '.' . STORAGE_AUDIO_EXTENSION,
                        $directory . $product_audio_id . '.' . STORAGE_AUDIO_EXTENSION
                    );
                }
            }

            // Add specials
            if (isset($this->request->post['special'])) {
                foreach ($this->request->post['special'] as $special) {
                    $this->model_catalog_product->createProductSpecial( $product_id,
                                                                        $special['regular_price'],
                                                                        $special['exclusive_price'],
                                                                        $special['date_start'],
                                                                        $special['date_end'],
                                                                        $special['sort_order']);
                }
            }

            // Add license conditions
            if (isset($this->request->post['license_conditions'])) {
                foreach ($this->request->post['license_conditions'] as $license_condition_id => $value) {
                    $this->model_catalog_product->addLicenseConditionValue($product_id, $license_condition_id);
                }
            }

            $this->db->commit();

            // Reset cache
            $this->cache->clean($this->auth->getId());
            $this->storage->clean($this->auth->getId());

            // Set success message
            $this->session->setUserMessage(array('success' => tt('Product successfully published!')));

            // Admin alert if current user is not verified (created product has been disabled)
            if (!$this->auth->isVerified()) {
                $this->mail->setSender($this->auth->getEmail());
                $this->mail->setFrom($this->auth->getEmail());
                $this->mail->setTo(MAIL_EMAIL_SUPPORT_ADDRESS);
                $this->mail->setSubject(sprintf(tt('New product has been created - %s'), PROJECT_NAME));
                $this->mail->setText(sprintf(tt('New product ID %s by %s has been created and waiting for approving'), $product_id, $this->auth->getUsername()));
                $this->mail->send();
            }

            $this->response->redirect($this->url->link('account/product'));
        }

        $data = $this->_populateForm($this->url->link('account/product/create'));

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
                    array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
                    array('name' => tt('Product list'), 'href' => $this->url->link('account/product'), 'active' => false),
                    array('name' => tt('Add product'), 'href' => $this->url->link('account/product/create'), 'active' => true),
            ));

        // Renter the template
        $this->response->setOutput($this->load->view('account/product/product_form.tpl', $data));
    }

    public function update() {

        $product_id = 0;

        // Redirect to product create if product_id is not exists
        if (isset($this->request->get['product_id'])) {
            $product_id = (int) $this->request->get['product_id'];
        } else {
            // Log hack attempt
            $this->security_log->write('Try to get product without product_id param');
            $this->response->redirect($this->url->link('account/product/create'));
        }

        // Redirect to login page if user is not logged
        if (!$this->auth->isLogged()) {
            $this->response->redirect($this->url->link('account/account/login', 'redirect=' . urlencode($this->url->link('account/product/update', 'product_id=' . $product_id))));
        }

        // Check if user has product
        if (!$this->model_catalog_product->userHasProduct($this->auth->getId(), $product_id)) {

            // Log hack attempt
            $this->security_log->write('Try to get not own\'s product_id #' . $product_id);

            // Redirect to safe page
            $this->response->redirect($this->url->link('account/product'));
        }

        if ('POST' == $this->request->getRequestMethod() && $this->_validateProductForm()) {

            // Load dependencies
            $translate = new Translate();
            $color     = new Color();

            // Create languages registry
            $languages = array(); foreach ($this->model_common_language->getLanguages() as $language) $languages[$language->language_id] = $language->code;

            // Set active directory
            $directory = DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR;

            // Start transaction
            $this->db->beginTransaction();

            // Add product
            $this->model_catalog_product->updateProduct($product_id,
                                                        $this->request->post['category_id'],
                                                        $this->request->post['currency_id'],
                                                        $this->request->post['regular_price'],
                                                        $this->request->post['exclusive_price'],
                                                        $this->request->post['withdraw_address'],
                                                        FilterUri::alias($this->request->post['product_description'][$this->language->getId()]['title']),
                                                        (int) $this->auth->isVerified());

            // Add 301 rule if product has new URI
            $url = new Url($this->db, $this->request, $this->response, $this->url->link('common/home'));

            $old_url = $this->url->link('catalog/product', 'product_id=' . $product_id);
            $new_url = $url->link('catalog/product', 'product_id=' . $product_id);

            if ($old_url != $new_url) {
                $this->model_common_redirect->createRedirect(
                    301,
                    str_replace($this->url->link('common/home'), false, $old_url),
                    str_replace($this->url->link('common/home'), false, $new_url)
                );
            }

            // Add product description
            $this->model_catalog_product->deleteProductDescriptions($product_id);

            foreach ($this->request->post['product_description'] as $language_id => $product_description) {

                $this->model_catalog_product->createProductDescription( $product_id,
                                                                        $language_id,
                                                                        (empty(trim($product_description['title']))       ? $translate->string($this->request->post['product_description'][$this->language->getId()]['title'],       $this->language->getCode(), $languages[$language_id]) : $product_description['title']),
                                                                        (empty(trim($product_description['description'])) ? $translate->string($this->request->post['product_description'][$this->language->getId()]['description'], $this->language->getCode(), $languages[$language_id]) : $product_description['description']));

            }


            // Add Tags
            $this->model_catalog_product->deleteProductToTagByProductId($product_id);

            // Prepare tags from request
            foreach ($this->request->post['product_description'] as $language_id => $product_description) {

                // Process current language not empty field only
                if (!empty($product_description['tags']) && $language_id == $this->language->getId()) {

                    // Separate a tags string and create multilingual registry
                    foreach (explode(',', $product_description['tags']) as $name) {

                        // Get tag id
                        $name = mb_strtolower(trim($name));

                        // Saved tags registry
                        if ($tag = $this->model_catalog_tag->getTagByName($name)) {

                            $tag_id = $tag->tag_id;

                        } else {

                            // Create new tag
                            $tag_id = $this->model_catalog_tag->addTag();

                            // Create descriptions for each language
                            foreach ($languages as $language_id => $code) {
                                $this->model_catalog_tag->addTagDescription($tag_id,
                                                                            $language_id,
                                                                            $translate->string($name, $this->language->getCode(), $code));

                            }
                        }

                        // Save new relations
                        $this->model_catalog_product->addProductToTag($product_id, $tag_id);
                    }
                }
            }

            // Add file
            if ($file_content = file_get_contents($directory . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION)) {

                $this->model_catalog_product->deleteProductFiles($product_id);
                $product_file_id = $this->model_catalog_product->createProductFile( $product_id,
                                                                                    md5($file_content),
                                                                                    sha1($file_content));
                rename(
                    $directory . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION,
                    $directory . $product_file_id . '.' . STORAGE_FILE_EXTENSION
                );
            }

            // Add demos
            $this->model_catalog_product->deleteProductDemos($product_id);

            if (isset($this->request->post['demo'])) {
                foreach ($this->request->post['demo'] as $row => $demo) {
                    $product_demo_id = $this->model_catalog_product->createProductDemo($product_id, $demo['sort_order'], $demo['url'], $this->request->post['main_demo'] == $row ? 1 : 0);

                    foreach ($demo['title'] as $language_id => $title) {
                        $this->model_catalog_product->createProductDemoDescription( $product_demo_id,
                                                                                    $language_id,
                                                                                    (empty(trim($title)) ? $translate->string($demo['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }
                }
            }

            // Update images
            $this->model_catalog_product->deleteProductImages($product_id);

            if (isset($this->request->post['image'])) {
                foreach ($this->request->post['image'] as $row => $image) {

                    // Add new images
                    $product_image_id = $this->model_catalog_product->createProductImage($product_id,
                                                                                         $image['sort_order'],
                                                                                         $this->request->post['main_image'] == $row ? 1 : 0,
                                                                                         isset($image['watermark']) ? 1 : 0);


                    // Generate image titles
                    foreach ($image['title'] as $language_id => $title) {
                        $this->model_catalog_product->createProductImageDescription($product_image_id,
                                                                                    $language_id,
                                                                                    (empty(trim($title)) ? $translate->string($image['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }

                    // Extract image colors
                    if ($color->setImage($directory . $image['product_image_id'] . '.' . STORAGE_IMAGE_EXTENSION) && $colors = $color->getColors()) {
                        foreach ($colors as $key => $value) {

                            $this->model_catalog_product->createProductImageColor(  $product_image_id,
                                                                                    $value['hex'],
                                                                                    $value['hue'],
                                                                                    $value['saturation'],
                                                                                    $value['value'],
                                                                                    $value['red'],
                                                                                    $value['green'],
                                                                                    $value['blue'],
                                                                                    $value['frequency']);
                        }
                    }

                    rename(
                        $directory . $image['product_image_id'] . '.' . STORAGE_IMAGE_EXTENSION,
                        $directory . $product_image_id . '.' . STORAGE_IMAGE_EXTENSION
                    );
                }

            // Generate unique image if others images is not exists
            } else {

                $product_image_id = $this->model_catalog_product->createProductImage($product_id, 1, 1, 0, 1);

                // Generate image titles from product title
                foreach ($this->request->post['product_description'] as $language_id => $product_description) {
                    $this->model_catalog_product->createProductImageDescription($product_image_id,
                                                                                $language_id,
                                                                                (empty(trim($product_description['title'])) ? $translate->string($this->request->post['product_description'][$this->language->getId()]['title'], $this->language->getCode(), $languages[$language_id]) : $product_description['title']));
                }

                $identicon = new Identicon();
                $image     = new Image($identicon->generateImageResource(sha1($product_id),
                                                                         PRODUCT_IMAGE_ORIGINAL_WIDTH,
                                                                         PRODUCT_IMAGE_ORIGINAL_HEIGHT), true);

                $image->save(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $product_image_id . '.' . STORAGE_IMAGE_EXTENSION);
            }


            // Add videos
            $this->model_catalog_product->deleteProductVideos($product_id);

            if (isset($this->request->post['video'])) {
                foreach ($this->request->post['video'] as $video) {

                    $product_video_id = $this->model_catalog_product->createProductVideo($product_id,
                                                                                        (isset($video['reduce']) ? 1 : 0),
                                                                                         $video['sort_order']);

                    foreach ($video['title'] as $language_id => $title) {
                        $this->model_catalog_product->createProductVideoDescription($product_video_id,
                                                                                    $language_id,
                                                                                    (empty(trim($title)) ? $translate->string($video['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }

                    rename(
                        $directory . $video['product_video_id'] . '.' . STORAGE_VIDEO_EXTENSION,
                        $directory . $product_video_id . '.' . STORAGE_VIDEO_EXTENSION
                    );
                }
            }

            // Add audios
            $this->model_catalog_product->deleteProductAudios($product_id);

            // Add audios
            if (isset($this->request->post['audio'])) {
                foreach ($this->request->post['audio'] as $audio) {

                    $product_audio_id = $this->model_catalog_product->createProductAudio($product_id,
                                                                                        (isset($audio['cut']) ? 1 : 0),
                                                                                        $audio['sort_order']);

                    foreach ($audio['title'] as $language_id => $title) {
                        $this->model_catalog_product->createProductAudioDescription($product_audio_id,
                                                                                    $language_id,
                                                                                    (empty(trim($title)) ? $translate->string($audio['title'][$this->language->getId()], $this->language->getCode(), $languages[$language_id]) : $title));
                    }

                    rename(
                        $directory . $audio['product_audio_id'] . '.' . STORAGE_AUDIO_EXTENSION,
                        $directory . $product_audio_id . '.' . STORAGE_AUDIO_EXTENSION
                    );
                }
            }

            // Add specials
            $this->model_catalog_product->deleteProductSpecials($product_id);

            if (isset($this->request->post['special'])) {
                foreach ($this->request->post['special'] as $special) {
                    $this->model_catalog_product->createProductSpecial( $product_id,
                                                                        $special['regular_price'],
                                                                        $special['exclusive_price'],
                                                                        $special['date_start'],
                                                                        $special['date_end'],
                                                                        $special['sort_order']);
                }
            }

            // Add license conditions
            $this->model_catalog_product->deleteLicenseConditions($product_id);
            if (isset($this->request->post['license_conditions'])) {
                foreach ($this->request->post['license_conditions'] as $license_condition_id => $value) {
                    $this->model_catalog_product->addLicenseConditionValue($product_id, $license_condition_id);
                }
            }

            $this->db->commit();

            // Cleaning
            $this->cache->clean($this->auth->getId());
            $this->storage->clean($this->auth->getId());

            // Set success message
            $this->session->setUserMessage(array('success' => tt('Product successfully updated!')));

            // Admin alert if current user is not verified (updated product has been disabled)
            if (!$this->auth->isVerified()) {
                $this->mail->setSender($this->auth->getEmail());
                $this->mail->setFrom($this->auth->getEmail());
                $this->mail->setTo(MAIL_EMAIL_SUPPORT_ADDRESS);
                $this->mail->setSubject(sprintf(tt('Product has been updated - %s'), PROJECT_NAME));
                $this->mail->setText(sprintf(tt('Product ID %s by %s has been updated and waiting for approving!'), $product_id, $this->auth->getUsername()));
                $this->mail->send();
            }

            $this->response->redirect($this->url->link('account/product'));
        }

        $data = $this->_populateForm($this->url->link('account/product/update', 'product_id=' . $product_id));

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['module_breadcrumbs'] = $this->load->controller('module/breadcrumbs', array(
                    array('name' => tt('Home'), 'href' => $this->url->link('common/home'), 'active' => false),
                    array('name' => tt('Product list'), 'href' => $this->url->link('account/product'), 'active' => false),
                    array('name' => tt('Update product'), 'href' => $this->url->link('account/product/update', 'product_id=' . $product_id), 'active' => true),
            ));

        // Renter the template
        $this->response->setOutput($this->load->view('account/product/product_form.tpl', $data));
    }

    public function delete() {

        // Redirect to product create if product_id is not exists
        if (!isset($this->request->get['product_id'])) {

            // Log hack attempt
            $this->security_log->write('Try to delete product without product_id param');
            $this->response->redirect($this->url->link('account/product'));
        }

        $product_id = (int) $this->request->get['product_id'];

        // Redirect to login page if user is not logged
        if (!$this->auth->isLogged()) {
            $this->response->redirect($this->url->link('account/account/login',
                                                       'redirect=' . urlencode($this->url->link('account/product/delete', 'product_id=' . $product_id)),
                                                       'SSL'));
        }

        // Check if user has product
        if (!$this->model_catalog_product->userHasProduct($this->auth->getId(), $product_id)) {

            // Log hack attempt
            $this->security_log->write('Try to delete not own\'s product #' . $product_id);
            $this->response->redirect($this->url->link('account/product'));
        }

        // Check if all customers already download package files
        if ($this->model_catalog_product->productHasRelations($product_id, ORDER_PENDING_STATUS_ID, ORDER_PROCESSED_STATUS_ID, ORDER_APPROVED_STATUS_ID)) {

            $this->session->setUserMessage(array('danger' => tt('Looks like someone has ordered this product, try again later!')));
            $this->response->redirect($this->url->link('account/product'));
        }

        // Begin action
        $this->document->setTitle(tt('Deleting...'));

        // Start transaction
        $this->db->beginTransaction();

        // Delete product description
        $this->model_catalog_product->deleteProductDescriptions($product_id);

        // Delete Tags
        $this->model_catalog_product->deleteProductToTagByProductId($product_id);

        // Delete product files
        $product_file_info = $this->model_catalog_product->getProductFileInfo($product_id);
        unlink(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $product_file_info->product_file_id . '.' . STORAGE_FILE_EXTENSION);
        $this->model_catalog_product->deleteProductFiles($product_id);

        // Delete demos
        $this->model_catalog_product->deleteProductDemos($product_id);

        // Delete images
        $product_images = $this->model_catalog_product->getProductImages($product_id, DEFAULT_LANGUAGE_ID);
        foreach ($product_images as $product_image) {
            unlink(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $product_image->product_image_id . '.' . STORAGE_IMAGE_EXTENSION);
        }

        $this->model_catalog_product->deleteProductImages($product_id);

        // Delete videos
        $this->model_catalog_product->deleteProductVideos($product_id);

        // Delete audios
        $this->model_catalog_product->deleteProductAudios($product_id);

        // Delete specials
        $this->model_catalog_product->deleteProductSpecials($product_id);

        // Delete favorites
        $this->model_catalog_product->deleteProductFavorites($product_id);

        // Delete reviews
        $this->model_catalog_product->deleteProductReviews($product_id);

        // Delete license conditions
        $this->model_catalog_product->deleteLicenseConditions($product_id);

        // Reconfigure orders relations
        $this->model_catalog_product->reconfigureProductToOrders($product_id);

        // Delete product
        $this->model_catalog_product->deleteProduct($product_id);


        $this->db->commit();

        // Reset cache
        $this->cache->clean($this->auth->getId());
        $this->storage->clean($this->auth->getId());

        $this->session->setUserMessage(array('success' => tt('Product successfully deleted!')));
        $this->response->redirect($this->url->link('account/product'));

    }

    // AJAX actions begin
    public function uploadImage() {

        if (!$this->auth->isLogged()) {
            $this->security_log->write('Try to upload image from guest request');
            exit;
        }

        if (!$this->request->isAjax()) {
            $this->security_log->write('Try to upload image without ajax request');
            exit;
        }

        $json = array('error_message' => tt('Undefined upload error'));

        if ('POST' == $this->request->getRequestMethod() && $this->_validateImage()) {

            // Create user's folder if not exists
            if (!is_dir(DIR_STORAGE . $this->auth->getId())) {
                mkdir(DIR_STORAGE . $this->auth->getId(), 0755);
            }

            $image = new Image($this->request->files['image']['tmp_name'][$this->request->get['row']]);

            // Resize to default original format
            if (PRODUCT_IMAGE_ORIGINAL_WIDTH < $image->getWidth() || PRODUCT_IMAGE_ORIGINAL_HEIGHT < $image->getHeight()) {
                $image->resize(PRODUCT_IMAGE_ORIGINAL_WIDTH, PRODUCT_IMAGE_ORIGINAL_HEIGHT, 1, false, true);
            }

            $image_path     = DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR;
            $image_filename = '_' . sha1(rand().microtime().$this->auth->getId());

            // Save image to the temporary file
            if ($image->save($image_path . $image_filename . '.' . STORAGE_IMAGE_EXTENSION)) {
                $json = array('success_message'   => tt('Image successfully uploaded!'),
                              'url'               => $this->cache->image($image_filename, $this->auth->getId(), 36, 36),
                              'product_image_id'  => $image_filename);
            }

        } else if (isset($this->_error['image']['common'])) {
            $json = array('error_message' => $this->_error['image']['common']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function uploadAudio() {

        if (!$this->auth->isLogged()) {
            $this->security_log->write('Try to upload audio from guest request');
            exit;
        }

        if (!$this->request->isAjax()) {
            $this->security_log->write('Try to upload audio without ajax request');
            exit;
        }

        $json = array('error_message' => tt('Undefined upload error'));

        if ('POST' == $this->request->getRequestMethod() && $this->_validateAudio()) {

            // Create user's folder if not exists
            if (!is_dir(DIR_STORAGE . $this->auth->getId())) mkdir(DIR_STORAGE . $this->auth->getId(), 0755);

            // Init variables
            $audio_path     = DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR;
            $audio_filename = '_' . sha1(rand().microtime().$this->auth->getId());

            // Save audio to the temporary file
            if ($this->ffmpeg->convert(
                $this->request->files['audio']['tmp_name'][$this->request->get['row']],
                $audio_path . $audio_filename  . '.' . STORAGE_AUDIO_EXTENSION
            )) {
                $json = array(
                    'success_message'   => tt('Audio successfully uploaded!'),
                    'ogg'               => $this->cache->audio($audio_filename, $this->auth->getId(), 'oga'),
                    'mp3'               => $this->cache->audio($audio_filename, $this->auth->getId(), 'mp3'),
                    'product_audio_id'  => $audio_filename
                );
            }
        } else if (isset($this->_error['audio']['common'])) {
            $json = array('error_message' => $this->_error['audio']['common']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function uploadVideo() {

        if (!$this->auth->isLogged()) {
            $this->security_log->write('Try to upload video from guest request');
            exit;
        }

        if (!$this->request->isAjax()) {
            $this->security_log->write('Try to upload video without ajax request');
            exit;
        }

        $json = array('error_message' => tt('Undefined upload error'));

        if ('POST' == $this->request->getRequestMethod() && $this->_validateVideo()) {

            // Create user's folder if not exists
            if (!is_dir(DIR_STORAGE . $this->auth->getId())) mkdir(DIR_STORAGE . $this->auth->getId(), 0755);

            // Init variables
            $video_path     = DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR;
            $video_filename = '_' . sha1(rand().microtime().$this->auth->getId());

            // Save video to the temporary file
            if ($this->ffmpeg->convert(
                $this->request->files['video']['tmp_name'][$this->request->get['row']],
                $video_path . $video_filename  . '.' . STORAGE_VIDEO_EXTENSION
            )) {
                $json = array(
                    'success_message'   => tt('Video successfully uploaded!'),
                    'ogg'               => $this->cache->video($video_filename, $this->auth->getId(), 'ogv'),
                    'mp4'               => $this->cache->video($video_filename, $this->auth->getId(), 'mp4'),
                    'product_video_id'  => $video_filename
                );
            }
        } else if (isset($this->_error['video']['common'])) {
            $json = array('error_message' => $this->_error['video']['common']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function uploadPackage() {

        if (!$this->auth->isLogged()) {
            $this->security_log->write('Trying to access to uploadPackage method from guest request');
            exit;
        }

        if (!$this->request->isAjax()) {
            $this->security_log->write('Trying to access to uploadPackage method without ajax request');
            exit;
        }

        $json = array('error_message' => tt('Undefined upload error'));

        if ('POST' == $this->request->getRequestMethod() && $this->_validatePackage()) {

            $file_content = file_get_contents($this->request->files['package']['tmp_name']);

            // Generate unique path names
            $filename  = '_' . sha1(rand().microtime().$this->auth->getId());

            // Create user's folder if not exists
            if (!is_dir(DIR_STORAGE . $this->auth->getId())) {
                mkdir(DIR_STORAGE . $this->auth->getId(), 0755);
            }

            // Return result
            if (move_uploaded_file($this->request->files['package']['tmp_name'], DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $filename . '.' . STORAGE_FILE_EXTENSION)) {
                $json = array('success_message'   => tt('Package file was successfully uploaded!'),
                              'product_file_id'   => $filename,
                              'hash_md5'          => 'MD5:  ' . md5($file_content),
                              'hash_sha1'         => 'SHA1: ' . sha1($file_content));
            }

        } else if (isset($this->_error['file']['common'])) {
            $json = array('error_message' => $this->_error['file']['common']);
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // Local helpers begin
    private function _populateForm($action) {

        $data = array();

        // Common
        $data['date_today']           = date('Y-m-d');
        $data['date_tomorrow']        = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));

        $data['product_id']           = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : false;
        $data['error']                = $this->_error;
        $data['action']               = $action;
        $data['href_account_product'] = $this->url->link('account/product');

        $data['this_language_id']     = $this->language->getId();

        // Get saved info
        if (isset($this->request->get['product_id'])) {
            $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id'], $this->language->getId(), $this->auth->getId(), ORDER_APPROVED_STATUS_ID);
            $this->document->setTitle(sprintf(tt('Edit %s'), $product_info->title));
            $data['title'] = sprintf(tt('Edit %s'), $product_info->title);
        } else {
            $product_info = array();
            $this->document->setTitle(tt('Add product'));
            $data['title'] = tt('Add product');
        }

        // File
        if ( isset($this->request->post['product_file_id']) &&
            !empty($this->request->post['product_file_id']) &&
            file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION) &&
            $file_content = file_get_contents(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION)) {

            $data['package_hash_md5']    = 'MD5:  ' . md5($file_content);
            $data['package_hash_sha1']   = 'SHA1: ' . sha1($file_content);
            $data['product_file_id']     = $this->request->post['product_file_id'];

        } else if ($product_info && $product_file_info = $this->model_catalog_product->getProductFileInfo($product_info->product_id)) {

            $data['package_hash_md5']    = 'MD5:  ' . $product_file_info->hash_md5;
            $data['package_hash_sha1']   = 'SHA1: ' . $product_file_info->hash_sha1;
            $data['product_file_id'] = $product_file_info->product_file_id;

            $file_size = $this->storage->getFileSize($product_file_info->product_file_id,
                                                     $this->auth->getId(),
                                                     STORAGE_FILE_EXTENSION);

        } else {

            $data['package_hash_md5']    = false;
            $data['package_hash_sha1']   = false;
            $data['product_file_id'] = false;
        }

        // Languages
        $languages = $this->model_common_language->getLanguages($this->language->getId());

        $data['languages']  = array();
        foreach ($languages as $language) {
            $data['languages'][$language->language_id] = array(
                'language_id' => $language->language_id,
                'code'        => $language->code,
                'name'        => $language->name
            );
        }


        // Licenses
        $licenses = $this->model_common_license->getLicenses($this->language->getId());

        $data['licenses'] = array();
        foreach ($licenses as $license) {

            // Get license conditions
            $license_conditions = $this->model_common_license->getLicenseConditions($license->license_id, $this->language->getId());

            $conditions = array();
            foreach ($license_conditions as $license_condition) {

                // Get product's license condition value
                if (isset($this->request->post['license_conditions'][$license_condition->license_condition_id])) {
                    $license_condition_value = true;
                } else if ($product_info) {
                    $license_condition_value = $this->model_catalog_product->getLicenseConditionValue($product_info->product_id, $license_condition->license_condition_id);
                } else {
                    $license_condition_value = true;
                }


                if ($license_condition->optional) {

                    $condition = sprintf(
                        $license_condition->condition,
                        tt('may')
                    );

                    $conditions[$license_condition->license_condition_id] = array(
                        'license_condition_id' => $license_condition->license_condition_id,
                        'optional'             => true,
                        'checked'              => $license_condition_value,
                        'text'                 => highlight_license_condition($condition, tt('may'), tt('shall not')),
                    );

                } else {
                    $conditions[$license_condition->license_condition_id] = array(
                        'license_condition_id' => $license_condition->license_condition_id,
                        'optional'             => false,
                        'checked'              => true,
                        'text'                 => highlight_license_condition($license_condition->condition, tt('may'), tt('shall not')),
                    );
                }
            }

            // Merge
            $data['licenses'][$license->license_id] = array(
                'name'        => $license->name . ' ' . tt('License'),
                'description' => $license->description,
                'conditions'  => $conditions
            );
        }

        // Product descriptions
        $data['product_description'] = array();

        if (isset($this->request->post['product_description'])) {
            foreach ($this->request->post['product_description'] as $language_id => $product_description) {
                $data['product_description'][$language_id] = array('title'       => isset($product_description['title']) ? $product_description['title'] : false,
                                                                   'description' => isset($product_description['description']) ? $product_description['description'] : false,
                                                                   'tags'        => isset($product_description['tags']) ? $product_description['tags'] : false);

            }
        } elseif ($product_info) {

            $product_tags = $this->model_catalog_product->getTagsByProductId($this->request->get['product_id']);

            $product_tag_descriptions = array();
            foreach ($product_tags as $product_tag) {
                foreach ($this->model_catalog_tag->getTagDescriptions($product_tag->tag_id) as $tag_description) {
                    $product_tag_descriptions[$tag_description->language_id][] = $tag_description->name;
                }
            }




            foreach ($this->model_catalog_product->getProductDescriptions($this->request->get['product_id'], $this->language->getId()) as $product_description) {
                $data['product_description'][$product_description->language_id] = array('title'       => $product_description->title,
                                                                                        'description' => $product_description->description,
                                                                                        'tags'        => isset($product_tag_descriptions[$product_description->language_id]) ? implode(', ', $product_tag_descriptions[$product_description->language_id]) : false);
            }
        } else {
            foreach ($languages as $language) {
                $data['product_description'][$language->language_id] = array('title'       => false,
                                                                             'description' => false,
                                                                             'tags'        => false);
            }
        }

        // Demos
        $demo_rows = array(0);
        $data['demos'] = array();

        if (isset($this->request->post['demo'])) {
            foreach ($this->request->post['demo'] as $row => $demo) {

                $demo_rows[] = $row;

                $demo_titles = array();

                foreach ($demo['title'] as $language_id => $title) {
                    $demo_titles[$language_id] = $title;
                }

                $data['demos'][$row] = array(
                    'main'  => isset($this->request->post['main_demo']) && $this->request->post['main_demo'] == $row ? true : false,
                    'url'   => isset($demo['url']) ? $demo['url'] : false,
                    'title' => $demo_titles);
            }

        } else if ($product_info) {
            foreach ($this->model_catalog_product->getProductDemos($product_info->product_id, $this->language->getId()) as $product_demo) {

                $demo_rows[] = $product_demo->product_demo_id;

                $demo_titles = array();

                foreach ($this->model_catalog_product->getProductDemoDescriptions($product_demo->product_demo_id, $this->language->getId()) as $demo_description) {
                    $demo_titles[$demo_description->language_id] = $demo_description->title;
                }

                $data['demos'][$product_demo->product_demo_id] = array(
                    'main'  => $product_demo->main,
                    'url'   => $product_demo->url,
                    'title' => $demo_titles);
            }
        }

        $data['demo_max_row']    = max($demo_rows);
        $data['demo_total_rows'] = count($demo_rows) - 1;

        // Images
        $image_rows = array(0);
        $data['images'] = array();

        if (isset($this->request->post['image'])) {
            foreach ($this->request->post['image'] as $row => $image) {

                $image_rows[]      = $row;
                $image_titles      = array();
                $product_image_url = false;

                foreach ($image['title'] as $language_id => $title) {
                    $image_titles[$language_id] = $title;
                }

                // If image already stored in exist product
                if ( isset($image['product_image_id']) &&
                    !empty($image['product_image_id']) &&
                    file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $image['product_image_id'] . '.' . STORAGE_IMAGE_EXTENSION)) {

                    $product_image_url = $this->cache->image($image['product_image_id'], $this->auth->getId(), 36, 36);
                }

                $data['images'][$row] = array(
                    'product_image_id'     => $image['product_image_id'],
                    'url'                  => $product_image_url,
                    'identicon'            => isset($image['identicon']) ? 1 : 0,
                    'watermark'            => isset($image['watermark']) ? 1 : 0,
                    'main'                 => isset($this->request->post['main_image']) && $this->request->post['main_image'] == $row ? true : false,
                    'title'                => $image_titles);
            }

        } else if ($product_info) {

            foreach ($this->model_catalog_product->getProductImagesInfo($product_info->product_id) as $row => $image) {

                $row++;
                $image_rows[] = $row;
                $image_titles = array();

                foreach ($this->model_catalog_product->getProductImageDescriptions($image->product_image_id, $this->language->getId()) as $image_description) {
                    $image_titles[$image_description->language_id] = $image_description->title;
                }

                $data['images'][$row] = array(
                    'product_image_id'     => $image->product_image_id,
                    'watermark'            => $image->watermark,
                    'main'                 => $image->main,
                    'identicon'            => $image->identicon,
                    'url'                  => $this->cache->image($image->product_image_id, $this->auth->getId(), 36, 36),
                    'title'                => $image_titles);
            }
        }

        $data['image_max_row']    = max($image_rows);
        $data['image_total_rows'] = count($image_rows) - 1;


        // Videos
        $video_rows = array(0);
        $data['videos'] = array();

        if (isset($this->request->post['video'])) {
            foreach ($this->request->post['video'] as $row => $video) {

                $video_rows[]      = $row;
                $video_titles      = array();
                $mp4               = false;
                $ogg               = false;

                foreach ($video['title'] as $language_id => $title) {
                    $video_titles[$language_id] = $title;
                }

                // If video already stored in exist product
                if ( isset($video['product_video_id']) &&
                    !empty($video['product_video_id']) &&
                    file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $video['product_video_id'] . '.' . STORAGE_VIDEO_EXTENSION)) {

                    $mp4 = $this->cache->video($video['product_video_id'], $this->auth->getId(), 'mp4');
                    $ogg = $this->cache->video($video['product_video_id'], $this->auth->getId(), 'ogv');
                }

                $data['videos'][$row] = array(
                    'product_video_id' => $video['product_video_id'],
                    'ogg'              => $ogg,
                    'mp4'              => $mp4,
                    'reduce'           => isset($video['reduce']) ? 1 : 0,
                    'title'            => $video_titles);
            }

        } else if ($product_info) {

            foreach ($this->model_catalog_product->getProductVideos($product_info->product_id, $this->language->getId()) as $row => $video) {

                $row++;
                $video_rows[] = $row;
                $video_titles = array();

                foreach ($this->model_catalog_product->getProductVideoDescriptions($video->product_video_id, $this->language->getId()) as $video_description) {
                    $video_titles[$video_description->language_id] = $video_description->title;
                }

                $data['videos'][$row] = array(
                    'product_video_id' => $video->product_video_id,
                    'reduce'           => $video->reduce,
                    'ogg'              => $this->cache->video($video->product_video_id, $this->auth->getId(), 'ogv'),
                    'mp4'              => $this->cache->video($video->product_video_id, $this->auth->getId(), 'mp4'),
                    'title'            => $video_titles);
            }
        }

        $data['video_max_row']    = max($video_rows);
        $data['video_total_rows'] = count($video_rows) - 1;

        // Audios
        $audio_rows = array(0);
        $data['audios'] = array();

        if (isset($this->request->post['audio'])) {
            foreach ($this->request->post['audio'] as $row => $audio) {

                $audio_rows[]      = $row;
                $audio_titles      = array();
                $mp3               = false;
                $ogg               = false;

                foreach ($audio['title'] as $language_id => $title) {
                    $audio_titles[$language_id] = $title;
                }

                // If audio already stored in exist product
                if ( isset($audio['product_audio_id']) &&
                    !empty($audio['product_audio_id']) &&
                    file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $audio['product_audio_id'] . '.' . STORAGE_AUDIO_EXTENSION)) {

                    $mp3 = $this->cache->audio($audio['product_audio_id'], $this->auth->getId(), 'mp3');
                    $ogg = $this->cache->audio($audio['product_audio_id'], $this->auth->getId(), 'oga');
                }

                $data['audios'][$row] = array(
                    'product_audio_id' => $audio['product_audio_id'],
                    'ogg'              => $ogg,
                    'mp3'              => $mp3,
                    'cut'              => isset($audio['cut']) ? 1 : 0,
                    'title'            => $audio_titles);
            }

        } else if ($product_info) {

            foreach ($this->model_catalog_product->getProductAudios($product_info->product_id, $this->language->getId()) as $row => $audio) {

                $row++;
                $audio_rows[] = $row;
                $audio_titles = array();

                foreach ($this->model_catalog_product->getProductAudioDescriptions($audio->product_audio_id, $this->language->getId()) as $audio_description) {
                    $audio_titles[$audio_description->language_id] = $audio_description->title;
                }

                $data['audios'][$row] = array(
                    'product_audio_id' => $audio->product_audio_id,
                    'cut'              => $audio->cut,
                    'ogg'              => $this->cache->audio($audio->product_audio_id, $this->auth->getId(), 'oga'),
                    'mp3'              => $this->cache->audio($audio->product_audio_id, $this->auth->getId(), 'mp3'),
                    'title'            => $audio_titles);
            }
        }

        $data['audio_max_row']    = max($audio_rows);
        $data['audio_total_rows'] = count($audio_rows) - 1;


        // Specials
        $special_rows = array(0);
        $data['specials'] = array();

        if (isset($this->request->post['special'])) {
            foreach ($this->request->post['special'] as $row => $special) {

                $special_rows[] = $row;

                $data['specials'][$row] = array(
                    'regular_price'   => isset($special['regular_price']) ? $special['regular_price'] : false,
                    'exclusive_price' => isset($special['exclusive_price']) ? $special['exclusive_price'] : false,
                    'date_start'      => isset($special['date_start']) ? $special['date_start'] : false,
                    'date_end'        => isset($special['date_end']) ? $special['date_end'] : false);
            }

        } else if ($product_info) {
            foreach ($this->model_catalog_product->getProductSpecials($product_info->product_id) as $product_special) {

                $special_rows[] = $product_special->product_special_id;

                $data['specials'][$product_special->product_special_id] = array(
                    'regular_price'   => $product_special->regular_price > 0 ? $product_special->regular_price : false,
                    'exclusive_price' => $product_special->exclusive_price > 0 ? $product_special->exclusive_price : false,
                    'date_start'      => $product_special->date_start,
                    'date_end'        => $product_special->date_end);
            }
        }

        $data['special_max_row']    = max($special_rows);
        $data['special_total_rows'] = count($special_rows) - 1;


        // Current exclusive price
        if (isset($this->request->post['withdraw_address'])) {
            $data['withdraw_address'] = $this->request->post['withdraw_address'];
        } else if ($product_info) {
            $data['withdraw_address'] = $this->model_catalog_product->getWithdrawAddress($product_info->product_id);
        } else {
            $data['withdraw_address'] = false;
        }

        // Current exclusive price
        if (isset($this->request->post['exclusive_price'])) {
            $data['exclusive_price'] = $this->request->post['exclusive_price'];
        } else if ($product_info) {
            $data['exclusive_price'] = $product_info->exclusive_price > 0 ? $product_info->exclusive_price : false;
        } else {
            $data['exclusive_price'] = false;
        }

        // Current regular price
        if (isset($this->request->post['regular_price'])) {
            $data['regular_price'] = $this->request->post['regular_price'];
        } else if ($product_info) {
            $data['regular_price'] = $product_info->regular_price > 0 ? $product_info->regular_price : false;
        } else {
            $data['regular_price'] = false;
        }

        // Currencies list
        $data['currencies'] = array();
        foreach ($this->model_common_currency->getCurrencies() as $currency) {
            $data['currencies'][$currency->currency_id] = $currency->code;
        }

        // Current currency
        if (isset($this->request->post['currency_id'])) {
            $data['currency_id'] = $this->request->post['currency_id'];
        } else if ($product_info) {
            $data['currency_id'] = $product_info->currency_id;
        } else {
            $data['currency_id'] = 0;
        }

        // Categories list
        $data['categories'] = array();
        foreach ($this->model_catalog_category->getCategories(null, $this->language->getId()) as $category) {
            foreach ($this->model_catalog_category->getCategories($category->category_id, $this->language->getId()) as $child_category) {
                $data['categories'][$category->title][$child_category->category_id] = $child_category->title;
            }
        }

        // Current category
        if (isset($this->request->post['category_id'])) {
            $data['category_id'] = $this->request->post['category_id'];
        } else if ($product_info) {
            $data['category_id'] = $product_info->category_id;
        } else {
            $data['category_id'] = 0;
        }

        return $data;
    }

    private function _validateProductForm() {

        // Category
        if (!isset($this->request->post['category_id']) ||
            ($this->request->post['category_id'] != 0 &&
            !$this->model_catalog_category->getCategory($this->request->post['category_id'], $this->language->getId()))
            ) {
            $this->_error['general']['category_id'] = tt('Wrong category field');

            // Filter critical request
            $this->security_log->write('Wrong category_id field');
            $this->request->post['category_id'] = 0;

        } else if ($this->request->post['category_id'] == 0) {
            $this->_error['general']['category_id'] = tt('Category is required');
        }

        // Product description
        if(isset($this->request->post['product_description'])) {

            foreach ($this->request->post['product_description'] as $language_id => $product_description) {

                // Language
                if (!$this->language->hasId($language_id)) {
                    $this->_error['general']['common'] = tt('Wrong language field');

                    // Filter critical request
                    $this->security_log->write('Wrong language_id field');
                    unset($this->request->post['product_description'][$language_id]);
                    break;
                }

                // Title
                if (!isset($product_description['title'])) {
                    $this->_error['general']['product_description'][$language_id]['title'] = tt('Wrong title input');

                    // Filter critical request
                    $this->security_log->write('Wrong product_description[title] field');
                    unset($this->request->post['product_description'][$language_id]);
                    break;

                } else if (empty($product_description['title']) && $language_id == $this->language->getId()) {
                    $this->_error['general']['product_description'][$language_id]['title'] = tt('Title is required');
                } else if (!ValidatorProduct::titleValid(html_entity_decode($product_description['title']))) {
                    $this->_error['general']['product_description'][$language_id]['title'] = tt('Invalid title format');
                }

                // Description
                if (!isset($product_description['description'])) {
                    $this->_error['general']['product_description'][$language_id]['description'] = tt('Wrong description input');

                    // Filter critical request
                    $this->security_log->write('Wrong product_description[description] field');
                    unset($this->request->post['product_description'][$language_id]);
                    break;

                } else if (empty($product_description['description']) && $language_id == $this->language->getId()) {
                    $this->_error['general']['product_description'][$language_id]['description'] = tt('Description is required');
                } else if (!ValidatorProduct::descriptionValid(html_entity_decode($product_description['description']))) {
                    $this->_error['general']['product_description'][$language_id]['description'] = tt('Invalid description format');
                }

                // Tags
                if (!isset($product_description['tags'])) {
                    $this->_error['general']['product_description'][$language_id]['tags'] = tt('Wrong tags input');

                    // Filter critical request
                    $this->security_log->write('Wrong product_description[tags] field');
                    unset($this->request->post['product_description'][$language_id]);
                    break;

                } else if (!ValidatorProduct::tagsValid(html_entity_decode($product_description['tags']))) {
                    $this->_error['general']['product_description'][$language_id]['tags'] = tt('Invalid tags format');
                }
            }
        }

        // Package file
        if (isset($this->request->files['package']['tmp_name']) && !empty($this->request->files['package']['tmp_name'])) {

            $this->_error['file']['common'] = tt('Package file is not allowed for this action');
            $this->security_log->write('Try to load package file without ajax interface');
            unset($this->request->files['package']);

        } else if (empty($this->request->post['product_file_id'])) {

            $this->_error['file']['common'] = tt('Package file is required');

        } else if (!isset($this->request->post['product_file_id'])) {

            $this->_error['file']['common'] = tt('Package file input is wrong');
            $this->security_log->write('Wrong product package field');

        } else if (!file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $this->request->post['product_file_id'] . '.' . STORAGE_FILE_EXTENSION)) {

            $this->_error['file']['common'] = tt('Temporary package file is wrong');
            $this->security_log->write('Try to access not own\'s temporary package file');
        }

        // Demos
        if (isset($this->request->post['demo'])) {

            // Main Demo
            if (!isset($this->request->post['main_demo'])) {
                $this->_error['demo']['common'] = tt('Main demo is required');

                // Filter critical request
                $this->security_log->write('Wrong product main_demo field');
                unset($this->request->post['demo']);
            }

            $demo_count = 0;
            foreach ($this->request->post['demo'] as $row => $demo) {

                $demo_count++;

                // Title
                if (isset($demo['title'])) {
                    foreach ($demo['title'] as $language_id => $title) {

                        // Language
                        if (!$this->language->hasId($language_id)) {
                            $this->_error['demo']['common'] = tt('Wrong language field');

                            // Filter critical request
                            $this->security_log->write('Wrong product demo language_id field');
                            unset($this->request->post['demo'][$row]);
                            break;
                        }

                        // Title validation
                        if (empty($title) && $language_id == $this->language->getId()) {
                            $this->_error['demo'][$row]['title'][$language_id] = tt('Title is required');
                        } else if (!ValidatorProduct::titleValid(html_entity_decode($title))) {
                            $this->_error['demo'][$row]['title'][$language_id] = tt('Invalid title format');
                        }
                    }
                } else {
                    $this->_error['demo']['common'] = tt('Wrong title input');

                    // Filter critical request
                    $this->security_log->write('Wrong product demo title field');
                    unset($this->request->post['demo'][$row]);
                    break;
                }

                // Url
                if (isset($demo['url'])) {
                    if (empty($demo['url'])) {
                        $this->_error['demo'][$row]['url'] = tt('Demo URL is required');
                    } else if (!ValidatorProduct::urlValid(html_entity_decode($demo['url']))) {
                        $this->_error['demo'][$row]['url'] = tt('Invalid URL format');
                    }
                } else {
                    $this->_error['demo']['common'] = tt('Wrong demo URL input');

                    // Filter critical request
                    $this->security_log->write('Wrong product demo URL field');
                    unset($this->request->post['demo'][$row]);
                    break;
                }

                // Sort order
                if (!isset($demo['sort_order']) || !$demo['sort_order']) {
                    $this->_error['demo']['common'] = tt('Wrong sort order input');

                    // Filter critical request
                    $this->security_log->write('Wrong product demo sort_order field');
                    unset($this->request->post['demo'][$row]);
                    break;
                }
            }

            // Maximum demo pages per product
            if (QUOTA_DEMO_PER_PRODUCT < $demo_count) {
                $this->_error['demo']['common'] = sprintf(tt('Allowed maximum %s demo pages per one product'), QUOTA_DEMO_PER_PRODUCT);

                // Filter critical request
                $this->security_log->write('Exceeded limit of product demo');
                unset($this->request->post['demo']);
            }
        }

        // Images
        if (isset($this->request->post['image'])) {

            // Filter downloads (moved to AJAX)
            unset($this->request->files['image']);

            // Required main image
            if (!isset($this->request->post['main_image'])) {
                $this->_error['image']['common'] = tt('Main image is required');

                // Filter critical request
                $this->security_log->write('Wrong product main_image field');
                unset($this->request->post['image']);
            }

            $image_count = 0;
            foreach ($this->request->post['image'] as $row => $image) {

                $image_count++;

                // Title
                if (isset($image['title'])) {
                    foreach ($image['title'] as $language_id => $title) {

                        // Language
                        if (!$this->language->hasId($language_id)) {
                            $this->_error['image']['common'] = tt('Wrong language field');

                            // Filter critical request
                            $this->security_log->write('Wrong product image language_id field');
                            unset($this->request->post['image']);
                            break;
                        }

                        // Title validation
                        if (empty($title) && $language_id == $this->language->getId()) {
                            $this->_error['image'][$row]['title'][$language_id] = tt('Title is required');
                        } else if (!ValidatorProduct::titleValid(html_entity_decode($title))) {
                            $this->_error['image'][$row]['title'][$language_id] = tt('Invalid title format');
                        }
                    }
                } else {
                    $this->_error['image']['common'] = tt('Wrong title input');

                    // Filter critical request
                    $this->security_log->write('Wrong product image title field');
                    unset($this->request->post['image']);
                    break;
                }

                // Require sort order field
                if (!isset($image['sort_order']) || !$image['sort_order']) {
                    $this->_error['image']['common'] = tt('Wrong sort order input');

                    // Filter critical request
                    $this->security_log->write('Wrong product image sort_order field');
                    unset($this->request->post['image']);
                    break;
                }

                // Require product product_image_id
                if (!isset($image['product_image_id'])) {
                    $this->_error['image']['common'] = tt('Wrong temporary ID image input');

                    // Filter critical request
                    $this->security_log->write('Wrong product image product_image_id field');
                    unset($this->request->post['image']);
                    break;
                }

                // Require product product_image_id
                if (!isset($image['product_image_id'])) {
                    $this->_error['image']['common'] = tt('Wrong image ID input');

                    // Filter critical request
                    $this->security_log->write('Wrong product image product_image_id field');
                    unset($this->request->post['image']);
                    break;
                }

                // Check if new temporary and stored image fields is not empty
                if (empty($image['product_image_id'])) {
                    $this->_error['image']['common'] = tt('Image file is required');
                }

                // Check temporary image file if exists
                if (!empty($image['product_image_id']) && !file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $image['product_image_id'] . '.' . STORAGE_IMAGE_EXTENSION)) {

                    $this->_error['image']['common'] = tt('Temporary image ID is wrong');
                    $this->security_log->write('Try to access not own\'s temporary image file');

                    unset($this->request->post['image']);
                    break;
                }
            }

            // Maximum images per one product
            if (QUOTA_IMAGES_PER_PRODUCT < $image_count) {
                $this->_error['image']['common'] = sprintf(tt('Maximum %s images pages per one product'), QUOTA_IMAGES_PER_PRODUCT);

                // Filter critical request
                $this->security_log->write('Exceeded limit of product images');
                unset($this->request->post['image']);
            }
        }

        // Videos
        if (isset($this->request->post['video'])) {

            unset($this->request->files['video']);

            $video_count = 0;
            foreach ($this->request->post['video'] as $row => $video) {

                $video_count++;

                // Title
                if (isset($video['title'])) {
                    foreach ($video['title'] as $language_id => $title) {

                        // Language
                        if (!$this->language->hasId($language_id)) {
                            $this->_error['video']['common'] = tt('Wrong language field');

                            // Filter critical request
                            $this->security_log->write('Wrong product video language_id field');
                            unset($this->request->post['video']);
                            break;
                        }

                        // Title validation
                        if (empty($title) && $language_id == $this->language->getId()) {
                            $this->_error['video'][$row]['title'][$language_id] = tt('Title is required');
                        } else if (!ValidatorProduct::titleValid(html_entity_decode($title))) {
                            $this->_error['video'][$row]['title'][$language_id] = tt('Invalid title format');
                        }
                    }
                } else {
                    $this->_error['video']['common'] = tt('Wrong title input');

                    // Filter critical request
                    $this->security_log->write('Wrong product video title field');
                    unset($this->request->post['video']);
                    break;
                }

                // Require sort order field
                if (!isset($video['sort_order']) || !$video['sort_order']) {
                    $this->_error['video']['common'] = tt('Wrong sort order input');

                    // Filter critical request
                    $this->security_log->write('Wrong product video sort_order field');
                    unset($this->request->post['video']);
                    break;
                }

                // Require product product_video_id
                if (!isset($video['product_video_id'])) {
                    $this->_error['video']['common'] = tt('Wrong temporary ID video input');

                    // Filter critical request
                    $this->security_log->write('Wrong product video product_video_id field');
                    unset($this->request->post['video']);
                    break;
                }

                // Require product product_video_id
                if (!isset($video['product_video_id'])) {
                    $this->_error['video']['common'] = tt('Wrong video ID input');

                    // Filter critical request
                    $this->security_log->write('Wrong product video product_video_id field');
                    unset($this->request->post['video']);
                    break;
                }

                // Check if new temporary and stored video fields is not empty
                if (empty($video['product_video_id'])) {
                    $this->_error['video']['common'] = tt('Video file is required');
                }

                // Check temporary video file if exists
                if (!empty($video['product_video_id']) &&
                    !file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $video['product_video_id'] . '.' . STORAGE_VIDEO_EXTENSION)) {

                    $this->_error['video']['common'] = tt('Temporary video ID is wrong');
                    $this->security_log->write('Try to access not own\'s temporary video file');

                    unset($this->request->post['video']);
                    break;
                }
            }

            // Maximum videos per one product
            if (QUOTA_VIDEO_PER_PRODUCT < $video_count) {
                $this->_error['video']['common'] = sprintf(tt('Maximum %s videos pages per one product'), QUOTA_VIDEO_PER_PRODUCT);

                // Filter critical request
                $this->security_log->write('Exceeded limit of product videos');
                unset($this->request->post['video']);
            }
        }

        // Audios
        if (isset($this->request->post['audio'])) {

            unset($this->request->files['audio']);

            $audio_count = 0;
            foreach ($this->request->post['audio'] as $row => $audio) {

                $audio_count++;

                // Title
                if (isset($audio['title'])) {
                    foreach ($audio['title'] as $language_id => $title) {

                        // Language
                        if (!$this->language->hasId($language_id)) {
                            $this->_error['audio']['common'] = tt('Wrong language field');

                            // Filter critical request
                            $this->security_log->write('Wrong product audio language_id field');
                            unset($this->request->post['audio']);
                            break;
                        }

                        // Title validation
                        if (empty($title) && $language_id == $this->language->getId()) {
                            $this->_error['audio'][$row]['title'][$language_id] = tt('Title is required');
                        } else if (!ValidatorProduct::titleValid(html_entity_decode($title))) {
                            $this->_error['audio'][$row]['title'][$language_id] = tt('Invalid title format');
                        }
                    }
                } else {
                    $this->_error['audio']['common'] = tt('Wrong title input');

                    // Filter critical request
                    $this->security_log->write('Wrong product audio title field');
                    unset($this->request->post['audio']);
                    break;
                }

                // Require sort order field
                if (!isset($audio['sort_order']) || !$audio['sort_order']) {
                    $this->_error['audio']['common'] = tt('Wrong sort order input');

                    // Filter critical request
                    $this->security_log->write('Wrong product audio sort_order field');
                    unset($this->request->post['audio']);
                    break;
                }

                // Require product product_audio_id
                if (!isset($audio['product_audio_id'])) {
                    $this->_error['audio']['common'] = tt('Wrong temporary ID audio input');

                    // Filter critical request
                    $this->security_log->write('Wrong product audio product_audio_id field');
                    unset($this->request->post['audio']);
                    break;
                }

                // Require product product_audio_id
                if (!isset($audio['product_audio_id'])) {
                    $this->_error['audio']['common'] = tt('Wrong audio ID input');

                    // Filter critical request
                    $this->security_log->write('Wrong product audio product_audio_id field');
                    unset($this->request->post['audio']);
                    break;
                }

                // Check if new temporary and stored audio fields is not empty
                if (empty($audio['product_audio_id'])) {
                    $this->_error['audio']['common'] = tt('Audio file is required');
                }

                // Check temporary audio file if exists
                if (!empty($audio['product_audio_id']) &&
                    !file_exists(DIR_STORAGE . $this->auth->getId() . DIR_SEPARATOR . $audio['product_audio_id'] . '.' . STORAGE_AUDIO_EXTENSION)) {

                    $this->_error['audio']['common'] = tt('Temporary audio ID is wrong');
                    $this->security_log->write('Try to access not own\'s temporary audio file');

                    unset($this->request->post['audio']);
                    break;
                }
            }

            // Maximum audios per one product
            if (QUOTA_AUDIO_PER_PRODUCT < $audio_count) {
                $this->_error['audio']['common'] = sprintf(tt('Maximum %s audios pages per one product'), QUOTA_AUDIO_PER_PRODUCT);

                // Filter critical request
                $this->security_log->write('Exceeded limit of product audios');
                unset($this->request->post['audio']);
            }
        }

        // Currency
        if (!isset($this->request->post['currency_id'])) {

            // Filter critical request
            $this->security_log->write('Wrong product currency field');
            $this->request->post['currency_id'] = $this->currency->getId();

        } else if (!$this->currency->hasId($this->request->post['currency_id'])) {
            $this->_error['price']['common'] = tt('Wrong currency field');

            // Filter critical request
            $this->security_log->write('Wrong product currency_id field');
            $this->request->post['currency_id'] = $this->currency->getId();

        } else if (empty($this->request->post['currency_id']) || $this->request->post['currency_id'] == 0) {
            $this->_error['price']['currency_id'] = tt('Currency is required');
        }

        // Withdraw address
        if (!isset($this->request->post['withdraw_address'])) {
            $this->_error['price']['withdraw_address'] = tt('Wrong withdraw address field');

            // Filter critical request
            $this->security_log->write('Wrong product withdraw_address field');
            $this->request->post['withdraw_address'] = false;

        } else if (empty($this->request->post['withdraw_address'])) {
            $this->_error['price']['withdraw_address'] = tt('Withdraw address is required');
        } else if (!ValidatorBitcoin::addressValid(html_entity_decode($this->request->post['withdraw_address']))) {
            $this->_error['price']['withdraw_address'] = tt('Invalid withdraw address');
        }

        // Pricing

        // Requirements
        if (!isset($this->request->post['regular_price'])) {

            $this->_error['price']['regular_price'] = tt('Wrong regular price field');

            // Filter critical request
            $this->security_log->write('Wrong regular price field');
            $this->request->post['regular_price'] = 0;
        }

        if (!isset($this->request->post['exclusive_price'])) {

            $this->_error['price']['exclusive_price'] = tt('Wrong exclusive price field');

            // Filter critical request
            $this->security_log->write('Wrong exclusive price field');
            $this->request->post['exclusive_price'] = 0;
        }

        // Regular price
        if (!empty($this->request->post['regular_price'])) {

            if ($this->request->post['regular_price'] < ALLOWED_PRODUCT_MIN_PRICE) {
                $this->_error['price']['regular_price'] = sprintf(tt('Price must be %s or more'), $this->currency->format(ALLOWED_PRODUCT_MIN_PRICE));
            } else if ($this->request->post['regular_price'] > ALLOWED_PRODUCT_MAX_PRICE) {
                $this->_error['price']['regular_price'] = sprintf(tt('Maximum price is %s'), $this->currency->format(ALLOWED_PRODUCT_MAX_PRICE));
            } else if (!ValidatorBitcoin::amountValid(html_entity_decode($this->request->post['regular_price']))) {
                $this->_error['price']['regular_price'] = tt('Invalid price format');
            }
        }

        // Exclusive price
        if (!empty($this->request->post['exclusive_price'])) {
            if ($this->request->post['exclusive_price'] < ALLOWED_PRODUCT_MIN_PRICE) {
                $this->_error['price']['exclusive_price'] = sprintf(tt('Price must be %s or more'), $this->currency->format(ALLOWED_PRODUCT_MIN_PRICE));
            } else if ($this->request->post['exclusive_price'] > ALLOWED_PRODUCT_MAX_PRICE) {
                $this->_error['price']['exclusive_price'] = sprintf(tt('Maximum price is %s'), $this->currency->format(ALLOWED_PRODUCT_MAX_PRICE));
            } else if (!ValidatorBitcoin::amountValid(html_entity_decode($this->request->post['exclusive_price']))) {
                $this->_error['price']['exclusive_price'] = tt('Invalid price format');
            }
        }

        // Logic validation
        if (empty($this->request->post['regular_price']) && empty($this->request->post['exclusive_price'])) {
            $this->_error['price']['regular_exclusive_price'] = tt('Regular or exclusive price is required');
        } else if ($this->request->post['regular_price'] == $this->request->post['exclusive_price']) {
            $this->_error['price']['regular_exclusive_price'] = tt('The regular and exclusive prices should not be the same');
        } else if ($this->request->post['exclusive_price'] && $this->request->post['regular_price'] > $this->request->post['exclusive_price']) {
            $this->_error['price']['regular_exclusive_price'] = tt('The regular price should not be greater than exclusive price');
        }

        // Special
        if (isset($this->request->post['special'])) {

            $special_count = 0;

            foreach ($this->request->post['special'] as $row => $special) {

                $special_count++;

                // Requirements
                if (!isset($special['regular_price'])) {

                    $this->_error['special'][$row]['regular_price'] = tt('Wrong regular price field');

                    // Filter critical request
                    $this->security_log->write('Wrong special regular price field');
                    $special['regular_price'] = 0;
                }

                if (!isset($special['exclusive_price'])) {

                    $this->_error['special'][$row]['price']['exclusive_price'] = tt('Wrong exclusive price field');

                    // Filter critical request
                    $this->security_log->write('Wrong special exclusive price field');
                    $special['exclusive_price'] = 0;
                }

                // Regular price
                if (!empty($special['regular_price'])) {

                    if ($special['regular_price'] < ALLOWED_PRODUCT_MIN_PRICE) {
                        $this->_error['special'][$row]['regular_price'] = sprintf(tt('Price must be %s or more'), $this->currency->format(ALLOWED_PRODUCT_MIN_PRICE));
                    } else if ($special['regular_price'] > ALLOWED_PRODUCT_MAX_PRICE) {
                        $this->_error['special'][$row]['regular_price'] = sprintf(tt('Maximum price is %s'), $this->currency->format(ALLOWED_PRODUCT_MAX_PRICE));
                    } else if (!ValidatorBitcoin::amountValid(html_entity_decode($special['regular_price']))) {
                        $this->_error['special'][$row]['regular_price'] = tt('Invalid price format');
                    }
                }

                // Exclusive price
                if (!empty($special['exclusive_price'])) {
                    if ($special['exclusive_price'] < ALLOWED_PRODUCT_MIN_PRICE) {
                        $this->_error['special'][$row]['exclusive_price'] = sprintf(tt('Price must be %s or more'), $this->currency->format(ALLOWED_PRODUCT_MIN_PRICE));
                    } else if ($special['exclusive_price'] > ALLOWED_PRODUCT_MAX_PRICE) {
                        $this->_error['special'][$row]['exclusive_price'] = sprintf(tt('Maximum price is %s'), $this->currency->format(ALLOWED_PRODUCT_MAX_PRICE));
                    } else if (!ValidatorBitcoin::amountValid(html_entity_decode($special['exclusive_price']))) {
                        $this->_error['special'][$row]['exclusive_price'] = tt('Invalid price format');
                    }
                }

                // Logic validation
                if (empty($special['regular_price']) && empty($special['exclusive_price'])) {
                    $this->_error['special'][$row]['regular_exclusive_price'] = tt('Regular or exclusive price is required');
                } else if ($special['regular_price'] == $special['exclusive_price']) {
                    $this->_error['special'][$row]['regular_exclusive_price'] = tt('The regular and exclusive prices should not be the same');
                } else if ($special['exclusive_price'] && $special['regular_price'] > $special['exclusive_price']) {
                    $this->_error['special'][$row]['regular_exclusive_price'] = tt('The regular price should not be greater than exclusive price');
                }

                // Date start
                if (!isset($special['date_start'])) {
                    $this->_error['special'][$row]['date_start'] = tt('Wrong date start input');

                    // Filter critical request
                    $this->security_log->write('Wrong product special date_start field');
                    unset($this->request->post['special'][$row]);
                    break;

                } else if (empty($special['date_start'])) {
                    $this->_error['special'][$row]['date_start'] = tt('Date start is required');
                } else if (!ValidatorProduct::dateValid(html_entity_decode($special['date_start']))) {
                    $this->_error['special'][$row]['date_start'] = tt('Invalid date format');
                }

                // Date end
                if (!isset($special['date_end'])) {
                    $this->_error['special'][$row]['date_end'] = tt('Wrong date end input');

                    // Filter critical request
                    $this->security_log->write('Wrong product special date_end field');
                    unset($this->request->post['special'][$row]);
                    break;

                } else if (empty($special['date_end'])) {
                    $this->_error['special'][$row]['date_end'] = tt('Date end is required');
                } else if (!ValidatorProduct::dateValid(html_entity_decode($special['date_end']))) {
                    $this->_error['special'][$row]['date_end'] = tt('Invalid date format');
                }

                // Logic validation
                if (strtotime($special['date_start']) >= strtotime($special['date_end'])) {
                    $this->_error['special'][$row]['date_end'] = tt('Date end should not begin prior to Date start');
                }

                // Sort order
                if (!isset($special['sort_order']) || !$special['sort_order']) {
                    $this->_error['special']['common'] = tt('Wrong sort order input');

                    // Filter critical request
                    $this->security_log->write('Wrong product special sort_order field');
                    unset($this->request->post['special'][$row]);
                }
            }

            // Maximum special pages per product
            if (QUOTA_SPECIALS_PER_PRODUCT < $special_count) {
                $this->_error['special']['common'] = sprintf(tt('Maximum %s specials per one product'), QUOTA_DEMO_PER_PRODUCT);

                // Filter critical request
                $this->security_log->write('Exceeded limit of product specials');
                unset($this->request->post['special']);
            }
        }


        // License conditions validation: begin

        // Check license conditions for existing
        if (!isset($this->request->post['license_conditions']) || !$this->request->post['license_conditions'] || !is_array($this->request->post['license_conditions'])) {

            $this->_error['license']['common'] = tt('License conditions are not defined');

            // Filter critical request
            $this->security_log->write('License conditions are not defined');
            unset($this->request->post['license_conditions']);
        }


        // Validate each license condition
        foreach ($this->request->post['license_conditions'] as $license_condition_id => $value) {

            // Check for license_condition_id existing
            if (!$this->model_common_license->getLicenseCondition($license_condition_id)) {
                $this->_error['license']['common'] = tt('Invalid license condition ID');

                // Filter critical request
                $this->security_log->write('Requested license condition is not exist in the database');
                unset($this->request->post['license_conditions']);
                break;
            }
        }

        // Check for required license conditions
        $licenses = $this->model_common_license->getLicenses($this->language->getId());
        foreach ($licenses as $license) {

            // Get license conditions
            $license_conditions = $this->model_common_license->getLicenseConditions($license->license_id, $this->language->getId());

            foreach ($license_conditions as $license_condition) {

                if (!$license_condition->optional && !isset($this->request->post['license_conditions'][$license_condition->license_condition_id])) {

                    $this->_error['license']['common'] = tt('Required license conditions are not defined');

                    // Filter critical request
                    $this->security_log->write('Required license conditions are not defined');
                    unset($this->request->post['license_conditions']);
                    break;
                }
            }
        }

        // License conditions validation: end

        return !$this->_error;
    }

    private function _validateImage() {

        if (!isset($this->request->get['row']) || empty($this->request->get['row'])) {

            $this->_error['image']['common'] = tt('Image row is wrong!');
            $this->security_log->write('Uploaded image row is wrong');

        } else if (!isset($this->request->files['image']['tmp_name'][$this->request->get['row']]) || !isset($this->request->files['image']['name'][$this->request->get['row']])) {

            $this->_error['image']['common'] = tt('Image file is wrong!');
            $this->security_log->write('Uploaded image file is wrong');

        } else if (!ValidatorUpload::imageValid(
            array(
                'name'     => $this->request->files['image']['name'][$this->request->get['row']],
                'tmp_name' => $this->request->files['image']['tmp_name'][$this->request->get['row']]
            ),
            QUOTA_IMAGE_MAX_FILE_SIZE,
            PRODUCT_IMAGE_ORIGINAL_MIN_WIDTH,
            PRODUCT_IMAGE_ORIGINAL_MIN_HEIGHT)) {

            $this->_error['image']['common'] = tt('This is a not valid image file!');
            $this->security_log->write('Uploaded image file is not valid');
        }

        return !$this->_error;
    }

    private function _validatePackage() {

        if (!isset($this->request->files['package']['tmp_name']) || !isset($this->request->files['package']['name'])) {

            $this->_error['file']['common'] = tt('Uploaded package file is wrong!');
            $this->security_log->write('Uploaded package file is wrong');

        } else if (!ValidatorUpload::packageValid(
            $this->request->files['package'],
            $this->auth->getFileQuota() - ($this->storage->getUsedSpace($this->auth->getId()) - filesize($this->request->files['package']['tmp_name']) / 1000000))
        ) {

            $this->_error['file']['common'] = tt('Package file is a not valid!');
            $this->security_log->write('Uploaded package file is not valid');
        }

        return !$this->_error;
    }

    private function _validateAudio() {
        if (!isset($this->request->files['audio']['tmp_name']) || !isset($this->request->files['audio']['name'])) {

            $this->_error['audio']['common'] = tt('Uploaded audio file is wrong!');
            $this->security_log->write('Uploaded audio file is wrong');

        } else if (!ValidatorUpload::audioValid(
            array(
                'name'     => $this->request->files['audio']['name'][$this->request->get['row']],
                'tmp_name' => $this->request->files['audio']['tmp_name'][$this->request->get['row']]
            ), QUOTA_AUDIO_MAX_FILE_SIZE)) {

            $this->_error['audio']['common'] = tt('Audio is not valid!');
            $this->security_log->write('Uploaded audio file is not valid');
        }

        return !$this->_error;
    }

    private function _validateVideo() {
        if (!isset($this->request->files['video']['tmp_name']) || !isset($this->request->files['video']['name'])) {

            $this->_error['video']['common'] = tt('Uploaded video file is wrong!');
            $this->security_log->write('Uploaded video file is wrong');

        } else if (!ValidatorUpload::videoValid(
            array(
                'name'     => $this->request->files['video']['name'][$this->request->get['row']],
                'tmp_name' => $this->request->files['video']['tmp_name'][$this->request->get['row']]
            ), QUOTA_AUDIO_MAX_FILE_SIZE)) {

            $this->_error['video']['common'] = tt('Video is not valid!');
            $this->security_log->write('Uploaded video file is not valid');
        }

        return !$this->_error;
    }
}
