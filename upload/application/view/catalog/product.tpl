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
 ?>

<?php echo $header ?>
<div class="row">
  <div class="col-lg-12">
    <div class="page-header">
      <h1 id="forms"><?php echo $title ?></h1>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <?php echo $module_breadcrumbs ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <?php echo $alert_warning ?>
    <?php echo $alert_success ?>
    <?php echo $alert_danger ?>
  </div>
</div>
<div class="catalog-product">
  <div class="row">
    <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
      <div class="bs-component product-image">
        <img onclick="zoomImage('<?php echo $product_image_orig_url ?>', '<?php echo $product_title ?>')" src="<?php echo $product_image_url ?>" alt="<?php echo $product_title ?>" title="<?php echo $product_title ?>" data-toggle="modal" data-target="#zoomImage" />
      </div>
      <?php if ($product_images > 1) { ?>
        <div class="bs-component product-images">
          <?php foreach ($product_images as $key => $image) { ?>
            <img onclick="zoomImage('<?php echo $image['original'] ?>', '<?php echo $image['title'] ?>')" src="<?php echo $image['preview'] ?>" alt="<?php echo $image['title'] ?>" title="<?php echo $image['title'] ?>" data-toggle="modal" data-target="#zoomImage" />
          <?php } ?>
        </div>
      <?php } ?>
      <div id="zoomImage" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="zoomImage" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title"><?php echo $product_title ?></h4>
            </div>
            <div class="modal-body">
              <img src="<?php echo $product_image_url ?>" alt="<?php echo $product_title ?>" title="<?php echo $product_title ?>" />
            </div>
          </div>
        </div>
      </div>
      <?php if ($product_audios) { ?>
        <div class="bs-component product-audios">
          <audio id="audio" preload="none">
            <source id="audioMP3" src="" type="audio/mpeg" />
            <source id="audioOGG" src="" type="audio/ogg" />
            <?php echo tt('Your browser does not support the audio element.') ?>
          </audio>
          <?php foreach ($product_audios as $key => $audio) { ?>
            <h5 id="audio<?php echo $key ?>" class="audio" onclick="audio(<?php echo $key ?>, '<?php echo $audio['ogg'] ?>', '<?php echo $audio['mp3'] ?>')">
              <span class="btn btn-default btn-xs">
                <i class="glyphicon glyphicon-music"></i>
              </span>
              <?php echo sprintf(tt('Track %s: %s'), $key + 1, $audio['title']) ?>
            </h5>
          <?php } ?>
        </div>
      <?php } ?>
      <?php if ($product_videos) { ?>
        <div class="bs-component product-videos">
          <?php foreach ($product_videos as $key => $video) { ?>
            <h5 id="video<?php echo $key ?>" class="video" onclick="video('<?php echo $video['title'] ?>', '<?php echo $video['ogg'] ?>', '<?php echo $video['mp4'] ?>')" data-toggle="modal" data-target="#zoomVideo">
              <span class="btn btn-default btn-xs">
                <i class="glyphicon glyphicon-facetime-video"></i>
              </span>
              <?php echo sprintf(tt('Track %s: %s'), $key + 1, $video['title']) ?>
            </h5>
          <?php } ?>
        </div>
        <div id="zoomVideo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="zoomVideo" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?php echo $product_title ?></h4>
              </div>
              <div class="modal-body">
                <video id="video" preload="none" width="570" controls="controls">
                  <source id="videoMP4" src="" type="video/mp4" />
                  <source id="videoOGG" src="" type="video/ogg" />
                  <?php echo tt('Your browser does not support the video element.') ?>
                </video>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
    <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
      <div class="bs-component catalog-product-info">
        <div class="product-misc">
          <?php if ($verified) { ?>
            <div class="text-success"><span class="glyphicon glyphicon-ok"></span> <strong><?php echo tt('Verified Seller') ?></strong></div>
          <?php } else { ?>
            <div><span class="glyphicon glyphicon-eye-close"></span> <strong><?php echo tt('Unverified Seller') ?></strong></div>
          <?php } ?>
          <div><?php echo sprintf(tt('Release: %s'), $product_date_added) ?></div>
          <div><?php echo sprintf(tt('Update: %s'), $product_date_modified) ?></div>
          <div><?php echo $product_sales ? sprintf(tt('Sales: %s'), $product_sales)  : false ?></div>
        </div>
        <div class="product-user">
          <?php if ($product_is_self) { ?>
          <a href="<?php echo $product_href_user ?>"><?php echo tt('My') ?></a> <?php echo tt('product') ?>
          <?php } else { ?>
            <a href="<?php echo $product_href_user ?>"><?php echo $product_username ?></a> <?php echo tt('made it for you') ?>
          <?php } ?>
        </div>
        <div class="product-price">
          <form id="priceForm" name="license-form" method="POST" action="<?php echo $license_form_action ?>">
            <?php if ($product_has_special_regular_price || $product_has_special_exclusive_price) { ?>
              <div class="price-text"><?php echo tt('Special price') ?></div>
              <div class="special-price">
                <?php if ($product_has_special_regular_price) { ?><label><input type="radio" name="license" value="regular" <?php echo ($regular ? 'checked="checked"' : false) ?> /> <span class="regular-price <?php echo $product_has_special_exclusive_price ? 'bold' : false ?>"><?php echo $product_special_regular_price ?></span></label><?php } ?>
                <?php if ($product_has_special_regular_price && $product_has_special_exclusive_price) { ?>/<?php } ?>
                <?php if ($product_has_special_exclusive_price) { ?><label><input type="radio" name="license" value="exclusive" <?php echo ($exclusive ? 'checked="checked"' : false) ?> /> <span class="exclusive-price"><?php echo $product_special_exclusive_price ?></span></label><?php } ?>
                <sup class="time-left"><?php echo $product_special_expires ?></sup>
              </div>
              <div class="price-text"><?php echo tt('Default') ?></div>
              <?php if ($product_has_special_regular_price) { ?><span class="default-price"><?php echo $product_regular_price ?></span><?php } ?>
              <?php if ($product_has_special_exclusive_price) { ?><span class="default-price exclusive-price"><?php echo $product_exclusive_price ?></span><?php } ?>
            <?php } else { ?>
              <?php if ($product_has_regular_price) { ?>
                <div class="price-text"><?php echo tt('Price') ?></div>
                <div class="regular-price"><label><input type="radio" name="license" value="regular" <?php echo ($regular ? 'checked="checked"' : false) ?> /> <?php echo $product_regular_price ?></label></div>
              <?php } ?>
              <?php if ($product_has_exclusive_price) { ?>
                <div class="price-text"><?php echo tt('Exclusive price') ?></div>
                <div class="exclusive-price"><label><input type="radio" name="license" value="exclusive" <?php echo ($exclusive ? 'checked="checked"' : false) ?> /> <?php echo $product_exclusive_price ?></label></div>
              <?php } ?>
            <?php } ?>
          </form>
        </div>
        <div class="product-action">
          <?php if ($product_demo) { ?>
            <?php if ($product_demos) { ?>
              <div class="btn-group">
                <a class="btn btn-primary btn-lg" href="<?php echo $product_href_demo ?>" target="_blank"><i class="glyphicon glyphicon-eye-open"></i> <?php echo tt('Live preview') ?></a>
                <a href="#" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <?php foreach ($product_demos as $demo) { ?>
                    <li><a href="<?php echo $demo['url'] ?>" target="_blank"><?php echo $demo['title'] ?></a></li>
                  <?php } ?>
                </ul>
              </div>
            <?php } else { ?>
              <a class="btn btn-primary btn-lg" href="<?php echo $product_href_demo ?>" target="_blank"><i class="glyphicon glyphicon-eye-open"></i> <?php echo tt('Live preview') ?></a>
            <?php } ?>
          <?php } ?>
          <?php if ($product_order_status == 'approved') { ?>
            <div class="btn-group">
              <a href="<?php echo $product_href_download ?>" class="btn btn-success btn-lg"><i class="glyphicon glyphicon-circle-arrow-down"></i> <?php echo tt('Get') ?></a>
              <a href="#" class="btn btn-success dropdown-toggle btn-lg" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a data-toggle="modal" data-target="#productReport" onclick="report(<?php echo $product_id ?>, '<?php echo tt('Report') ?>: <?php echo $product_title ?>')"><?php echo tt('Report') ?></a></li>
              </ul>
            </div>
          <?php } else if ($product_order_status == 'processed') { ?>
            <div class="btn-group">
              <a class="btn btn-info btn-lg disabled dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-hourglass"></i> <?php echo tt('Confirmation...') ?></a>
            </div>
          <?php } else { ?>
            <div class="btn-group">
              <a class="btn btn-primary btn-lg" id="buyProduct"><i class="glyphicon glyphicon-shopping-cart"></i> <?php echo tt('Buy') ?></a>
              <a href="#" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a data-toggle="modal" data-target="#productReport" onclick="report(<?php echo $product_id ?>, '<?php echo tt('Report') ?>: <?php echo $product_title ?>')"><?php echo tt('Report') ?></a></li>
              </ul>
            </div>
          <?php } ?>
          <div class="btn btn-success btn-lg" onclick="favorite(<?php echo $product_id ?>, <?php echo (int) $user_is_logged ?>)" id="productFavoriteButton<?php echo $product_id ?>"><i class="glyphicon <?php echo $product_favorite ? 'glyphicon-heart' : 'glyphicon-heart-empty' ?>"></i> <span><?php echo $product_favorites ?></span></div>
        </div>
      </div>
      <?php if ($product_tags) { ?>
        <div class="bs-component catalog-product-tags">
          <?php foreach ($product_tags as $key => $tag) { ?>
            <a href="<?php echo $tag['url'] ?>" class="label <?php echo $color_labels[$key] ?>"><i class="glyphicon glyphicon-record"></i> <?php echo $tag['name'] ?></a>
          <?php } ?>
        </div>
      <?php } ?>
      <div class="bs-component catalog-product-details">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#productDescription" data-toggle="tab"><?php echo tt('Description') ?></a></li>
          <li><a href="#productLicense" data-toggle="tab"><?php echo tt('License') ?></a></li>
          <li><a href="#productReviews" data-toggle="tab"><?php echo tt('Reviews') ?></a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade active in" id="productDescription">
            <p><?php echo $product_description ?></p>
          </div>
          <div class="tab-pane fade" id="productLicense">
            <?php foreach ($licenses as $license) { ?>
              <div class="well">
                <p><?php echo $license['description'] ?></p>
              </div>
              <?php foreach ($license['conditions'] as $condition) { ?>
                <div style="margin-left:20px">
                  <span class="glyphicon glyphicon-circle-arrow-right"></span> <?php echo $condition ?>
                </div>
              <?php } ?>
            <?php } ?>
            <p>&nbsp;</p>
            <p><?php echo tt('This is a human-readable summary of (and not a substitute for) the') ?> <a href="licenses"><?php echo tt('Licensing Policy') ?></a>.</p>
          </div>
          <div class="tab-pane fade" id="productReviews">
            <div id="productReviewList" class="product-reviews"></div>
            <div id="productReviewForm" class="product-review-form">
              <?php if ($user_is_logged) { ?>
                <div class="review-form"><textarea name="review" id="productReviewContent" class="form-control" placeholder="<?php echo tt('Please explain you review and click submit button') ?>"></textarea></div>
                <div class="review-control"><div class="btn btn-primary" id="productReviewButton"> <span><?php echo tt('Submit') ?></span></div></div>
              <?php } else { ?>
                <textarea name="review" id="productReviewContent" class="form-control disabled" disabled="disabled"><?php echo tt('Please login or register to write reviews') ?></textarea>
                <div class="review-control"><div class="btn btn-primary disabled" id="productReviewButton"> <span><?php echo tt('Submit') ?></span></div></div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade product-purchase" id="productPurchase" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"><?php echo sprintf(tt('%s Purchase'), $title) ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="modal-loading"><i class="glyphicon glyphicon-hourglass"></i> <?php echo tt('Please, wait...') ?></div>
          </div>
        </div>

        <div class="row hide" id="paymentResult">
          <div class="col-xs-4 col-md-4 col-lg-4" id="paymentResultImg"></div>
          <div class="col-xs-8 col-md-8 col-lg-8 text-left" style="padding: 20px">
            <div class="row">
              <div class="col-xs-12 col-md-12 col-lg-12">
                <p></p>
                <pre><?php echo tt('Loading...') ?></pre>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-3 col-md-3 col-lg-3 text-left">
                <a id="initBitcoinWallet" class="btn btn-primary" href=""><?php echo tt('Use wallet') ?></a>
              </div>
              <div class="col-xs-9 col-md-9 col-lg-9 text-muted small">
                <i class="glyphicon glyphicon-info-sign"></i> <?php echo tt('Don\'t have a Wallet?') ?> <a href="https://bitcoin.org/en/choose-your-wallet" rel="nofollow" target="_blank"><?php echo tt('Get it now!') ?></a>
                <br />
                <i class="glyphicon glyphicon-heart-empty text-danger"></i> <?php echo tt('Support the author by paying above min price:') ?>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="payment-support text-right" id="paymentSupport"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript"><!--

  $(document).ready(function() {

    // License changing
    $('#priceForm input[name=license]').change(function () {
      $('#priceForm').submit();
    });

    // Product ordering
    $('#buyProduct').click(function () {
      <?php if (!$user_is_logged) { ?>
        $('#loginForm').modal('toggle');
      <?php } else { ?>
        $.ajax({
            url:  'ajax/order/bitcoin/create',
            type: 'POST',
            data: { product_id: <?php echo $product_id ?>, license: $('#priceForm input[name=license]:checked').val() },
            beforeSend: function(e) {
              $('#productPurchase').modal('toggle');
            },
            success: function (e) {
              if (e['status']) {
                $('#paymentResult').removeClass('hide');
                $('#productPurchase .modal-loading').hide();
                $('#productPurchase pre').html(e['address']);
                $('#productPurchase p').html(e['text']);
                $('#productPurchase p').prepend('<span style="float:right" id="paymentTimer"></span>');
                $('#paymentResultImg').html('<img src="' + e['src'] + '" alt="' + e['address'] + '" />');

                support  = ' <a href="' + e['amounts'][0]['href'] + '" class="label btn btn-xs btn-info">'    + e['amounts'][0]['label'] + '</a>';
                support += ' <a href="' + e['amounts'][1]['href'] + '" class="label btn btn-xs btn-success">' + e['amounts'][1]['label'] + '</a>';
                support += ' <a href="' + e['amounts'][2]['href'] + '" class="label btn btn-xs btn-warning">' + e['amounts'][2]['label'] + '</a>';
                support += ' <a href="' + e['amounts'][3]['href'] + '" class="label btn btn-xs btn-danger">'  + e['amounts'][3]['label'] + '</a>';

                $('#paymentSupport').html(support);

                $('#initBitcoinWallet').attr('href', e['href']);
                timer(900, document.getElementById('paymentTimer'));
              } else {
                $('#productPurchase .modal-loading').html('Maintenance mode. Please wait few minutes and try again.');
              }
            },
            error: function (e) {
              $('#productPurchase').modal('toggle');
              alert('Connection error! Please, try again later.');
            }
        });
      <?php } ?>
    });

    // Init reviews
    function loadReviews(product_id) {
        $('#productReviewList').load('ajax/reviews&product_id=' + product_id);
    }

    loadReviews(<?php echo $product_id ?>);

    // Add review
    $('#productReviewButton').click(function () {

      if ('' == $('#productReviewContent').val()) {
        $('#productReviewForm .review-form').addClass('has-error');
      } else {
        $.ajax({
            url:  'ajax/review',
            type: 'POST',
            data: { product_id: <?php echo $product_id ?>, review: $('#productReviewContent').val() },
            beforeSend: function(e) {

              $('#productReviewForm .alert').remove();
              $('#productReviewForm .review-form').removeClass('has-error');
              $('#productReviewButton').addClass('disabled').prepend('<i class="glyphicon glyphicon-hourglass"></i> ');
              $('#productReviewContent').attr('disabled', true);

            },
            success: function (e) {
              if (e['success_message']) {

                $('#productReviewContent').val('').before('<div class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">×</button>' + e['success_message'] + '</div>');
                loadReviews(<?php echo $product_id ?>);

              } else if (e['error_message']) {
                alert(e['error_message']);
                $('#productReviewForm .review-form').addClass('has-error');
              }
            },
            error: function (e) {
              alert('Internal server error! Please, try again later.');
            }
        });

        $('#productReviewButton').removeClass('disabled').find('i').remove();
        $('#productReviewContent').attr('disabled', false);
      }
    });
  });
//--></script>
<?php echo $footer ?>
