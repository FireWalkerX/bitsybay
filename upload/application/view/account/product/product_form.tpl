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
  <div class="col-lg-3">
    <div class="bs-component product-form-hints">
      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="glyphicon glyphicon-info-sign"></i> <?php echo tt('Hints') ?></h3>
        </div>
        <div class="panel-body">
          <ul id="productDescriptionHints">
            <li><?php echo tt('Use only plain text without any special chars and links') ?></li>
          </ul>
          <ul id="productPackageHints">
            <li><?php echo tt('Package is main product file. Buyer can able to download it after purchase.') ?></li>
            <li><?php echo sprintf(tt('Allowed package format: %s'), strtoupper(STORAGE_FILE_EXTENSION)) ?></li>
          </ul>
          <ul id="productDemoHints">
            <li><?php echo tt('The demo pages used for online product preview in the iframe wrapper') ?></li>
          </ul>
          <ul id="productImageHints">
            <li><?php echo tt('We support') ?> <?php echo tt('JPEG, PNG') ?></li>
            <li><?php echo sprintf(tt('Min image Width: %s px'), PRODUCT_IMAGE_ORIGINAL_MIN_WIDTH) ?></li>
            <li><?php echo sprintf(tt('Min image Height: %s px'), PRODUCT_IMAGE_ORIGINAL_MIN_HEIGHT) ?></li>
            <li><?php echo tt('If image will not be changed we will generate it for you') ?></li>
          </ul>
          <ul id="productVideoHints">
            <li><?php echo tt('We support') ?> <?php echo tt('MOV, MPEG4, MP4, AVI, WMV, MPEGPS, FLV, 3GPP, WEBM, OGG') ?></li>
          </ul>
          <ul id="productAudioHints">
            <li><?php echo tt('We support') ?> <?php echo tt('MP3, OGG, WAW, WAWE, MKA, WMA, MP4, M4A') ?></li>
          </ul>
          <ul id="productPriceHints">
            <li><?php echo tt('The regular price should not be greater than exclusive price') ?></li>
            <li><?php echo sprintf(tt('Minimum price: %s'), ALLOWED_PRODUCT_MIN_PRICE) ?></li>
          </ul>
          <ul id="productLicenseHints">
            <li><?php echo tt('Change your license conditions') ?></li>
            <li><?php echo tt('These terms will overwrite original product license') ?></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-9">
    <div class="form-group bs-component">
      <form role="form" class="form-vertical" id="productForm" action="<?php echo $action ?>" method="POST" enctype="multipart/form-data">
        <div class="control">
          <a href="<?php echo $href_account_product ?>" onclick="return confirm('<?php echo tt("Are you sure?") ?>')" class="btn btn-default"><?php echo tt('Cancel') ?></a>
          <div id="submitForm" class="btn btn-primary"><?php echo tt('Publish') ?></div>
        </div>
        <ul class="nav nav-tabs">
          <li class="active"><a href="#general" data-toggle="tab"><?php echo tt('General') ?><?php echo isset($error['general']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#files" data-toggle="tab" id="aFiles"><?php echo tt('Package') ?><?php echo isset($error['file']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#demos" data-toggle="tab"><?php echo tt('Demo') ?><?php echo isset($error['demo']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#images" data-toggle="tab" id="aImages"><?php echo tt('Images') ?><?php echo isset($error['image']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#videos" data-toggle="tab" id="aVedeos"><?php echo tt('Video') ?><?php echo isset($error['video']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#audios" data-toggle="tab" id="aAudios"><?php echo tt('Audio') ?><?php echo isset($error['audio']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#prices" data-toggle="tab"><?php echo tt('Price') ?><?php echo isset($error['price']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
          <li><a href="#license" data-toggle="tab"><?php echo tt('License') ?><?php echo isset($error['license']) ? ' <span class="text-danger">*</span>' : false ?></a></li>
        </ul>
        <div id="ProductFormTabContent" class="tab-content">
          <div class="tab-pane fade in active" id="general">
            <?php if (isset($error['general']['common'])) { ?>
              <div class="alert alert-dismissible alert-danger">
                <?php echo $error['general']['common'] ?>
              </div>
            <?php } ?>
            <fieldset>
              <legend><?php echo tt('Show in category') ?></legend>
              <div class="form-group <?php echo isset($error['general']['category_id']) ? 'has-error' : false ?>">
                <select name="category_id" class="form-control" id="inputGeneralCategoryId">
                  <option value="0"><?php echo tt('Select') ?></option>
                  <?php foreach ($categories as $category_name => $child_category) { ?>
                    <optgroup label="<?php echo $category_name ?>">
                      <?php foreach ($child_category as $child_category_id => $child_category_name) { ?>
                        <option value="<?php echo $child_category_id ?>" <?php echo $category_id == $child_category_id ? 'selected="selected"' : false ?>><?php echo $child_category_name ?></option>
                      <?php } ?>
                    </optgroup>
                  <?php } ?>
                </select>
                <?php if (isset($error['general']['category_id'])) { ?>
                  <div class="text-danger"><?php echo $error['general']['category_id'] ?></div>
                <?php } ?>
              </div>
            </fieldset>
            <fieldset>
              <legend><?php echo tt('Description') ?></legend>
              <?php foreach ($product_description as $language_id => $description) { ?>
                <?php if ($language_id != $this_language_id) { ?>
                  <div class="language-version" onclick="$('#productDescription<?php echo $language_id ?>').toggle('fast');"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div>
                <?php } ?>
                <div id="productDescription<?php echo $language_id ?>" <?php echo ($language_id != $this_language_id && empty($description['title']) && empty($description['description']) && empty($description['tags']) ? 'style="display:none"' : false) ?>>
                  <div class="form-group <?php echo isset($error['general']['product_description'][$language_id]['title']) ? 'has-error' : false ?>">
                    <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="product_description[<?php echo $language_id ?>][title]" class="form-control" id="inputGeneralTitle<?php echo $language_id ?>" placeholder="<?php echo tt('Title') ?>" value="<?php echo $description['title'] ?>">
                    <?php if (isset($error['general']['product_description'][$language_id]['title'])) { ?>
                      <div class="text-danger"><?php echo $error['general']['product_description'][$language_id]['title'] ?></div>
                    <?php } ?>
                  </div>
                  <div class="form-group <?php echo isset($error['general']['product_description'][$language_id]['description']) ? 'has-error' : false ?>">
                    <textarea onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_DESCRIPTION_MAX_LENGTH ?>)" name="product_description[<?php echo $language_id ?>][description]" placeholder="<?php echo tt('Description') ?>" id="inputGeneralDescription<?php echo $language_id ?>" class="form-control" rows="6"><?php echo $description['description'] ?></textarea>
                    <?php if (isset($error['general']['product_description'][$language_id]['description'])) { ?>
                      <div class="text-danger"><?php echo $error['general']['product_description'][$language_id]['description'] ?></div>
                    <?php } ?>
                  </div>
                  <div <?php echo ($language_id != $this_language_id ? 'style="display:none"' : false) ?> class="form-group <?php echo isset($error['general']['product_description'][$language_id]['tags']) ? 'has-error' : false ?>">
                    <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TAGS_MAX_LENGTH ?>)" type="text" name="product_description[<?php echo $language_id ?>][tags]" class="form-control" id="inputGeneralTags<?php echo $language_id ?>" placeholder="<?php echo tt('Tags (comma separated)') ?>" value="<?php echo $description['tags'] ?>">
                    <?php if (isset($error['general']['product_description'][$language_id]['tags'])) { ?>
                      <div class="text-danger"><?php echo $error['general']['product_description'][$language_id]['tags'] ?></div>
                    <?php } ?>
                  </div>
                </div>
              <?php } ?>
            </fieldset>
          </div>
          <div class="tab-pane fade" id="files">

            <input type="hidden" name="product_file_id" id="productFileTmpId" value="<?php echo $product_file_id ?>" />

            <?php if (isset($error['file']['common'])) { ?>
              <div class="alert alert-dismissible alert-danger">
                <?php echo $error['file']['common'] ?>
              </div>
            <?php } ?>

            <div class="form-group <?php echo !$package_hash_md5 ? 'hide' : false ?>" id="packageControlSum">
              <fieldset>
                <legend><?php echo tt('Stored control sum') ?></legend>
                <pre id="productFileMd5"><?php echo $package_hash_md5 ?></pre>
                <pre id="productFileSha1"><?php echo $package_hash_sha1 ?></pre>
              </fieldset>
            </div>

            <div class="form-group <?php echo isset($error['file']['package']) ? 'has-error' : false ?>" id="inputPackageButton">
              <fieldset>
              <legend><?php echo ($product_id ? tt('Update your package') : tt('Upload your package')) ?></legend>
                <div class="btn btn-file btn-success">
                  <i class="glyphicon glyphicon-upload"></i> <span><?php echo ($product_id ? tt('Update file') : tt('Upload file')) ?></span>
                  <input type="file" name="package" class="form-control product-package" id="inputPackage">
                </div>
              </fieldset>
            </div>

            <div class="form-group hide" id="packageUploadProgress">
              <div class="text-success"><?php echo tt('Uploading...') ?></div>
              <div class="progress progress-striped active"><div id="packageProgress" class="progress-bar progress-bar-success"></div></div>
            </div>

          </div>
          <div class="tab-pane fade" id="demos">
            <?php if (isset($error['demo']['common'])) { ?>
              <div class="alert alert-dismissible alert-danger">
                <?php echo $error['demo']['common'] ?>
              </div>
            <?php } ?>
            <table class="table table-striped table-hover" id="productDemo">
              <thead>
                <tr>
                  <th class="column-main"><?php echo tt('Main') ?></th>
                  <th><?php echo tt('Demo wrapper') ?></th>
                  <th><?php echo tt('Title') ?></th>
                  <th class="column-action"><?php echo tt('Action') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($demos as $row => $demo) { ?>
                  <tr id="productDemoTr<?php echo $row ?>">
                    <td class="form-group"><input type="radio" name="main_demo" id="inputDemoMain<?php echo $row ?>" value="<?php echo $row ?>" <?php echo ($demo['main'] ? 'checked="checked"' : false) ?> /></td>
                    <td class="form-group <?php echo isset($error['demo'][$row]['url']) ? 'has-error' : false ?>">
                      <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_URL_MAX_LENGTH ?>)" type="text" name="demo[<?php echo $row ?>][url]" class="form-control" id="inputDemoUrl<?php echo $row ?>" placeholder="<?php echo tt('URL address') ?>" value="<?php echo $demo['url'] ?>" />
                      <?php if (isset($error['demo'][$row]['url'])) { ?>
                        <div class="text-danger"><?php echo $error['demo'][$row]['url'] ?></div>
                      <?php } ?>
                    </td>
                    <td style="width:40%" class="form-group <?php echo isset($error['demo'][$row]['title']) ? 'has-error' : false ?>">
                      <?php foreach ($demo['title'] as $language_id => $title) { ?>
                        <?php if ($language_id != $this_language_id) { ?>
                          <div class="language-version" onclick="$('#demoTitle<?php echo $language_id ?>-<?php echo $row ?>').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div>
                        <?php } ?>
                        <div id="demoTitle<?php echo $language_id ?>-<?php echo $row ?>" <?php echo ($language_id != $this_language_id && empty($title) ? 'style="display:none"' : false) ?>>
                          <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="demo[<?php echo $row ?>][title][<?php echo $language_id ?>]" class="form-control" id="inputDemoTitle<?php echo $row ?>l<?php echo $language_id ?>" placeholder="<?php echo tt('Title') ?>" value="<?php echo $title ?>" />
                          <?php if (isset($error['demo'][$row]['title'][$language_id])) { ?>
                            <div class="text-danger"><?php echo $error['demo'][$row]['title'][$language_id] ?></div>
                          <?php } ?>
                        </div>
                      <?php } ?>
                    </td>
                    <td class="form-group">
                      <input type="hidden" name="demo[<?php echo $row ?>][sort_order]" value="<?php echo $row ?>"  id="inputDemoSortOrder<?php echo $row ?>" />
                      <span onclick="removeDemo(<?php echo $row ?>)" class="btn btn-danger">
                        <i class="glyphicon glyphicon-trash"></i>
                        <?php echo tt('Remove') ?>
                      </span>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"></td>
                  <td>
                    <span onclick="addDemo()" class="btn btn-success">
                      <i class="glyphicon glyphicon-plus"></i>
                      <?php echo tt('Add demo') ?>
                    </span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="tab-pane fade" id="images">
            <?php if (isset($error['image']['common'])) { ?>
              <div class="alert alert-dismissible alert-danger">
                <?php echo $error['image']['common'] ?>
              </div>
            <?php } ?>
            <table class="table table-striped table-hover" id="productImage">
              <thead>
                <tr>
                  <th class="column-main"><?php echo tt('Main') ?></th>
                  <th class="column-image"><?php echo tt('Image') ?></th>
                  <th><?php echo tt('Watermark') ?></th>
                  <th><?php echo tt('Title') ?></th>
                  <th><?php echo tt('Action') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($images as $row => $image) { ?>
                  <tr id="productImageTr<?php echo $row ?>">
                    <td class="form-group"><input type="radio" name="main_image" id="inputImageMain<?php echo $row ?>" value="<?php echo $row ?>" <?php echo ($image['main'] ? 'checked="checked"' : false) ?> /></td>
                    <td class="form-group">
                      <?php if ($image['url']) { ?>
                        <div class="btn-file" id="imagePicture<?php echo $row ?>">
                          <img src="<?php echo $image['url'] ?>" alt="" />
                          <input type="file" name="image[<?php echo $row ?>]" id="inputImage<?php echo $row ?>" value="" onchange="imageUpload(<?php echo $row ?>)" class="product-image" />
                        </div>
                      <?php } else { ?>
                      <div class="btn-file btn btn-success" id="imagePicture<?php echo $row ?>">
                        <span><i class="glyphicon glyphicon-upload"></i> <?php echo tt("Upload image") ?></span>
                        <img src="<?php echo $image['url'] ?>" alt="" class="hide" />
                        <input type="file" name="image[<?php echo $row ?>]" id="inputImage<?php echo $row ?>" value="" onchange="imageUpload(<?php echo $row ?>)" class="product-image" />
                      </div>
                      <?php } ?>
                      <div class="hide" id="imageUpload<?php echo $row ?>">
                        <div class="progress progress-striped active image-upload" >
                          <div class="progress-bar progress-bar-success" id="imageProgress<?php echo $row ?>" ></div>
                        </div>
                      </div>
                    </td>
                    <td class="form-group"><label class="control-label"><input type="checkbox" name="image[<?php echo $row ?>][watermark]" id="inputImageWatermark<?php echo $row ?>" value="1" <?php echo ($image['watermark'] ? 'checked="checked"' : false) ?> <?php echo ($image['identicon'] ? 'disabled="disabled"' : false) ?> /> <?php echo tt('Protect') ?></label></td>

                    <td class="form-group <?php echo isset($error['image'][$row]['title']) ? 'has-error' : false ?>">
                      <?php foreach ($image['title'] as $language_id => $title) { ?>
                        <?php if ($language_id != $this_language_id) { ?>
                          <div class="language-version" onclick="$('#imageDescription<?php echo $language_id ?>-<?php echo $row ?>').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div>
                        <?php } ?>
                        <div id="imageDescription<?php echo $language_id ?>-<?php echo $row ?>" <?php echo ($language_id != $this_language_id && empty($title) ? 'style="display:none"' : false) ?>>
                          <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="image[<?php echo $row ?>][title][<?php echo $language_id ?>]" class="form-control" id="inputImageTitle<?php echo $row ?>l<?php echo $language_id ?>" placeholder="<?php echo tt('Title') ?>" value="<?php echo $title ?>" />
                          <?php if (isset($error['image'][$row]['title'][$language_id])) { ?>
                            <div class="text-danger"><?php echo $error['image'][$row]['title'][$language_id] ?></div>
                          <?php } ?>
                        </div>
                      <?php } ?>
                    </td>
                    <td class="form-group">
                      <input type="hidden" name="image[<?php echo $row ?>][sort_order]" value="<?php echo $row ?>"  id="inputImageSortOrder<?php echo $row ?>" />
                      <input type="hidden" name="image[<?php echo $row ?>][product_image_id]" value="<?php echo $image['product_image_id'] ?>" id="productImageId<?php echo $row ?>" />
                      <div onclick="removeImage(<?php echo $row ?>)" class="btn btn-danger">
                        <i class="glyphicon glyphicon-trash"></i>
                        <?php echo tt('Remove') ?>
                      </div>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="4"></td>
                  <td class="column-action">
                    <span onclick="addImage()" class="btn btn-success">
                      <i class="glyphicon glyphicon-plus"></i>
                      <?php echo tt('Add image') ?>
                    </span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="tab-pane fade" id="videos">
            <?php if (isset($error['video']['common'])) { ?>
            <div class="alert alert-dismissible alert-danger">
              <?php echo $error['video']['common'] ?>
            </div>
            <?php } ?>
            <table class="table table-striped table-hover" id="productVideo">
              <thead>
              <tr>
                <th class="column-video"><?php echo tt('Video') ?></th>
                <th><?php echo tt('Reduce quality') ?><sup></sup></th>
                <th><?php echo tt('Title') ?></th>
                <th><?php echo tt('Action') ?></th>
              </tr>
              </thead>
              <tbody>
              <?php foreach ($videos as $row => $video) { ?>
              <tr id="productVideoTr<?php echo $row ?>">
                <td class="form-group">
                  <?php if ($video['ogg'] && $video['mp4']) { ?>
                    <div class="btn-file" id="videoTrack<?php echo $row ?>">
                      <video controls="controls" preload="auto" width="280">
                        <source id="videoOGG<?php echo $row ?>" src="<?php echo $video['ogg'] ?>" type="video/ogg" />
                        <source id="videoMP4<?php echo $row ?>" src="<?php echo $video['mp4'] ?>" type="video/mp4" />
                        <?php echo tt('Your browser does not support the video element.') ?>
                      </video>
                    </div>
                  <?php } else { ?>
                    <div class="btn-file btn btn-success" id="videoTrack<?php echo $row ?>">
                      <span><i class="glyphicon glyphicon-upload"></i> <?php echo tt("Upload video") ?></span>
                      <video controls="controls" preload="auto" class="hide" width="280">
                        <source id="videoOGG<?php echo $row ?>" src="" type="video/ogg" />
                        <source id="videoMP4<?php echo $row ?>" src="" type="video/mp4" />
                        <?php echo tt('Your browser does not support the video element.') ?>
                      </video>
                      <input type="file" name="video[<?php echo $row ?>]" id="inputVideo<?php echo $row ?>" value="" onchange="videoUpload(<?php echo $row ?>)" class="product-video" />
                    </div>
                  <?php } ?>
                  <div class="hide" id="videoUpload<?php echo $row ?>">
                    <div class="progress progress-striped active video-upload" >
                      <div class="progress-bar progress-bar-success" id="videoProgress<?php echo $row ?>" ></div>
                    </div>
                  </div>
                </td>
                <td class="form-group"><label class="control-label"><input type="checkbox" name="video[<?php echo $row ?>][reduce]" value="1" <?php echo ($video['reduce'] ? 'checked="checked"' : false) ?> /> <?php echo tt('Protect') ?></label></td>
                <td class="form-group <?php echo isset($error['video'][$row]['title']) ? 'has-error' : false ?>">
                  <?php foreach ($video['title'] as $language_id => $title) { ?>
                    <?php if ($language_id != $this_language_id) { ?>
                      <div class="language-version" onclick="$('#videoDescription<?php echo $language_id ?>-<?php echo $row ?>').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div>
                    <?php } ?>
                    <div id="videoDescription<?php echo $language_id ?>-<?php echo $row ?>" <?php echo ($language_id != $this_language_id && empty($title) ? 'style="display:none"' : false) ?>>
                      <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="video[<?php echo $row ?>][title][<?php echo $language_id ?>]" class="form-control" id="inputVideoTitle<?php echo $row ?>l<?php echo $language_id ?>" placeholder="<?php echo tt('Title') ?>" value="<?php echo $title ?>" />
                      <?php if (isset($error['video'][$row]['title'][$language_id])) { ?>
                        <div class="text-danger"><?php echo $error['video'][$row]['title'][$language_id] ?></div>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </td>
                <td class="form-group">
                  <input type="hidden" name="video[<?php echo $row ?>][sort_order]" value="<?php echo $row ?>"  id="inputVideoSortOrder<?php echo $row ?>" />
                  <input type="hidden" name="video[<?php echo $row ?>][product_video_id]" value="<?php echo $video['product_video_id'] ?>" id="productVideoId<?php echo $row ?>" />
                  <div onclick="removeVideo(<?php echo $row ?>)" class="btn btn-danger">
                    <i class="glyphicon glyphicon-trash"></i>
                    <?php echo tt('Remove') ?>
                  </div>
                </td>
              </tr>
              <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"></td>
                  <td class="column-action">
                    <span onclick="addVideo()" class="btn btn-success">
                      <i class="glyphicon glyphicon-plus"></i>
                      <?php echo tt('Add video') ?>
                    </span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="tab-pane fade" id="audios">
            <?php if (isset($error['audio']['common'])) { ?>
            <div class="alert alert-dismissible alert-danger">
              <?php echo $error['audio']['common'] ?>
            </div>
            <?php } ?>
            <table class="table table-striped table-hover" id="productAudio">
              <thead>
              <tr>
                <th class="column-audio"><?php echo tt('Audio') ?></th>
                <th><?php echo sprintf(tt('Cut to first %s seconds'), PRODUCT_AUDIO_CUT_TIME) ?><sup></sup></th>
                <th><?php echo tt('Title') ?></th>
                <th><?php echo tt('Action') ?></th>
              </tr>
              </thead>
              <tbody>
              <?php foreach ($audios as $row => $audio) { ?>
              <tr id="productAudioTr<?php echo $row ?>">
                <td class="form-group">
                  <?php if ($audio['ogg'] && $audio['mp3']) { ?>
                    <div class="btn-file" id="audioTrack<?php echo $row ?>">
                      <audio controls="controls" preload="auto">
                        <source id="audioOGG<?php echo $row ?>" src="<?php echo $audio['ogg'] ?>" type="audio/ogg" />
                        <source id="audioMP3<?php echo $row ?>" src="<?php echo $audio['mp3'] ?>" type="audio/mpeg" />
                        <?php echo tt('Your browser does not support the audio element.') ?>
                      </audio>
                    </div>
                  <?php } else { ?>
                    <div class="btn-file btn btn-success" id="audioTrack<?php echo $row ?>">
                      <span><i class="glyphicon glyphicon-upload"></i> <?php echo tt("Upload audio") ?></span>
                      <audio controls="controls" preload="auto" class="hide">
                        <source id="audioOGG<?php echo $row ?>" src="" type="audio/ogg" />
                        <source id="audioMP3<?php echo $row ?>" src="" type="audio/mpeg" />
                        <?php echo tt('Your browser does not support the audio element.') ?>
                      </audio>
                      <input type="file" name="audio[<?php echo $row ?>]" id="inputAudio<?php echo $row ?>" value="" onchange="audioUpload(<?php echo $row ?>)" class="product-audio" />
                    </div>
                  <?php } ?>
                  <div class="hide" id="audioUpload<?php echo $row ?>">
                    <div class="progress progress-striped active audio-upload" >
                      <div class="progress-bar progress-bar-success" id="audioProgress<?php echo $row ?>" ></div>
                    </div>
                  </div>
                </td>
                <td class="form-group"><label class="control-label"><input type="checkbox" name="audio[<?php echo $row ?>][cut]" value="1" <?php echo ($audio['cut'] ? 'checked="checked"' : false) ?> /> <?php echo tt('Protect') ?></label></td>
                <td class="form-group <?php echo isset($error['audio'][$row]['title']) ? 'has-error' : false ?>">
                  <?php foreach ($audio['title'] as $language_id => $title) { ?>
                    <?php if ($language_id != $this_language_id) { ?>
                      <div class="language-version" onclick="$('#audioDescription<?php echo $language_id ?>-<?php echo $row ?>').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div>
                    <?php } ?>
                    <div id="audioDescription<?php echo $language_id ?>-<?php echo $row ?>" <?php echo ($language_id != $this_language_id && empty($title) ? 'style="display:none"' : false) ?>>
                      <input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="audio[<?php echo $row ?>][title][<?php echo $language_id ?>]" class="form-control" id="inputAudioTitle<?php echo $row ?>l<?php echo $language_id ?>" placeholder="<?php echo tt('Title') ?>" value="<?php echo $title ?>" />
                      <?php if (isset($error['audio'][$row]['title'][$language_id])) { ?>
                        <div class="text-danger"><?php echo $error['audio'][$row]['title'][$language_id] ?></div>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </td>
                <td class="form-group">
                  <input type="hidden" name="audio[<?php echo $row ?>][sort_order]" value="<?php echo $row ?>"  id="inputAudioSortOrder<?php echo $row ?>" />
                  <input type="hidden" name="audio[<?php echo $row ?>][product_audio_id]" value="<?php echo $audio['product_audio_id'] ?>" id="productAudioId<?php echo $row ?>" />
                  <div onclick="removeAudio(<?php echo $row ?>)" class="btn btn-danger">
                    <i class="glyphicon glyphicon-trash"></i>
                    <?php echo tt('Remove') ?>
                  </div>
                </td>
              </tr>
              <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"></td>
                  <td class="column-action">
                    <span onclick="addAudio()" class="btn btn-success">
                      <i class="glyphicon glyphicon-plus"></i>
                      <?php echo tt('Add audio') ?>
                    </span>
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
          <div class="tab-pane fade" id="prices">
            <?php if (isset($error['price']['common'])) { ?>
              <div class="alert alert-dismissible alert-danger">
                <?php echo $error['price']['common'] ?>
              </div>
            <?php } ?>
            <div class="row">
              <div class="col-lg-2">
                <label class="control-label" for="inputCurrencyId"><?php echo tt('Currency') ?></label>
                <div class="form-group <?php echo isset($error['price']['currency_id']) ? 'has-error' : false ?>">
                  <select name="currency_id" class="form-control <?php echo isset($error['price']['currency_id']) ? 'has-error' : false ?>" id="inputCurrencyId">
                    <?php foreach ($currencies as $id => $currency_code) { ?>
                      <option value="<?php echo $id ?>" <?php echo $currency_id == $id ? 'selected="selected"' : false ?>><?php echo $currency_code ?></option>
                    <?php } ?>
                  </select>
                  <?php if (isset($error['price']['currency_id'])) { ?>
                    <div class="text-danger"><?php echo $error['price']['currency_id'] ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group <?php echo isset($error['price']['withdraw_address']) ? 'has-error' : false ?>">
                  <label class="control-label" for="inputWithdrawAddress"><?php echo tt('Withdraw address') ?></label>
                  <input type="text" name="withdraw_address" class="form-control" id="inputWithdrawAddress" placeholder="" value="<?php echo $withdraw_address ?>" />
                  <?php if (isset($error['price']['withdraw_address'])) { ?>
                    <div class="text-danger"><?php echo $error['price']['withdraw_address'] ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <div class="row <?php echo isset($error['price']['regular_exclusive_price']) ? 'has-error' : false ?>">
              <div class="col-lg-3">
                <div class="form-group <?php echo isset($error['price']['regular_price']) ? 'has-error' : false ?>">
                  <label class="control-label" for="inputTitleRegularPrice"><?php echo tt('Regular Price') ?></label>
                  <input type="text" name="regular_price" class="form-control" id="inputTitleRegularPrice" placeholder="0.00" value="<?php echo $regular_price ?>" />
                  <?php if (isset($error['price']['regular_price'])) { ?>
                    <div class="text-danger"><?php echo $error['price']['regular_price'] ?></div>
                  <?php } ?>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group <?php echo isset($error['price']['exclusive_price']) ? 'has-error' : false ?>">
                  <label class="control-label" for="inputTitleExclusivePrice"><?php echo tt('Exclusive Price') ?></label>
                  <input type="text" name="exclusive_price" class="form-control" id="inputTitleExclusivePrice" placeholder="0.00" value="<?php echo $exclusive_price ?>" />
                  <?php if (isset($error['price']['exclusive_price'])) { ?>
                    <div class="text-danger"><?php echo $error['price']['exclusive_price'] ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <?php if (isset($error['price']['regular_exclusive_price'])) { ?>
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group has-error">
                    <div class="text-danger"><?php echo $error['price']['regular_exclusive_price'] ?></div>
                  </div>
                </div>
              </div>
            <?php } ?>

            <div class="row">&nbsp;</div>

            <fieldset>
              <legend><?php echo tt('Special offers') ?></legend>
              <table class="table table-striped table-hover" id="productSpecial">
                <thead>
                  <tr>
                    <th><?php echo tt('Regular price') ?></th>
                    <th><?php echo tt('Exclusive price') ?></th>
                    <th><?php echo tt('Offer start') ?></th>
                    <th><?php echo tt('Offer end') ?></th>
                    <th class="column-action"><?php echo tt('Action') ?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($specials as $row => $special) { ?>
                    <tr id="productSpecialTr<?php echo $row ?>">
                      <td class="form-group <?php echo isset($error['special'][$row]['regular_price']) || isset($error['special'][$row]['regular_exclusive_price']) ? 'has-error' : false ?>">
                        <input type="text" name="special[<?php echo $row ?>][regular_price]" class="form-control" id="specialRegularPrice<?php echo $row ?>" placeholder="<?php echo tt('0.00') ?>" value="<?php echo $special['regular_price'] ?>" />
                        <?php if (isset($error['special'][$row]['regular_price'])) { ?>
                          <div class="text-danger"><?php echo $error['special'][$row]['regular_price'] ?></div>
                        <?php } ?>

                        <?php if (isset($error['special'][$row]['regular_exclusive_price'])) { ?>
                          <div class="text-danger"><?php echo $error['special'][$row]['regular_exclusive_price'] ?></div>
                        <?php } ?>
                      </td>
                      <td class="form-group <?php echo isset($error['special'][$row]['exclusive_price']) || isset($error['special'][$row]['regular_exclusive_price']) ? 'has-error' : false ?>">
                        <input type="text" name="special[<?php echo $row ?>][exclusive_price]" class="form-control" id="specialExclusivePrice<?php echo $row ?>" placeholder="<?php echo tt('0.00') ?>" value="<?php echo $special['exclusive_price'] ?>" />
                        <?php if (isset($error['special'][$row]['exclusive_price'])) { ?>
                          <div class="text-danger"><?php echo $error['special'][$row]['exclusive_price'] ?></div>
                        <?php } ?>
                      </td>
                      <td class="form-group <?php echo isset($error['special'][$row]['date_start']) ? 'has-error' : false ?>">
                        <input type="text" name="special[<?php echo $row ?>][date_start]" class="form-control" id="specialDateStart<?php echo $row ?>" data-date="<?php echo $date_today ?>" data-date-format="yyyy-mm-dd" placeholder="<?php echo $date_today ?>" value="<?php echo $special['date_start'] ?>" />
                        <?php if (isset($error['special'][$row]['date_start'])) { ?>
                          <div class="text-danger"><?php echo $error['special'][$row]['date_start'] ?></div>
                        <?php } ?>
                      </td>
                      <td class="form-group <?php echo isset($error['special'][$row]['date_end']) ? 'has-error' : false ?>">
                        <input type="text" name="special[<?php echo $row ?>][date_end]" class="form-control" id="specialDateEnd<?php echo $row ?>" data-date="<?php echo $date_tomorrow ?>" data-date-format="yyyy-mm-dd" placeholder="<?php echo $date_tomorrow ?>" value="<?php echo $special['date_end'] ?>" />
                        <?php if (isset($error['special'][$row]['date_end'])) { ?>
                          <div class="text-danger"><?php echo $error['special'][$row]['date_end'] ?></div>
                        <?php } ?>
                      </td>
                      <td>
                        <input type="hidden" name="special[<?php echo $row ?>][sort_order]" value="<?php echo $row ?>" />
                        <span onclick="removeSpecial(<?php echo $row ?>)" class="btn btn-danger">
                          <i class="glyphicon glyphicon-trash"></i>
                          <?php echo tt('Remove') ?>
                        </span>
                        <script type="text/javascript"><!--
                          $('#specialDateStart<?php echo $row ?>,#specialDateEnd<?php echo $row ?>').datepicker().on('changeDate', function(){
                            $(this).datepicker('hide');
                          });
                        //--></script>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4"></td>
                    <td>
                      <span onclick="addSpecial()" class="btn btn-success">
                        <i class="glyphicon glyphicon-plus"></i>
                        <?php echo tt('Add special') ?>
                      </span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </fieldset>
          </div>

          <div class="tab-pane fade" id="license">
            <?php if (isset($error['license']['common'])) { ?>
            <div class="alert alert-dismissible alert-danger">
              <?php echo $error['license']['common'] ?>
            </div>
            <?php } ?>
            <?php foreach ($licenses as $license) { ?>
              <fieldset>
                <legend><?php echo $license['name'] ?></legend>
                  <?php foreach ($license['conditions'] as $condition_id => $condition) { ?>
                    <div class="checkbox <?php echo ($condition['optional'] ? false : 'disabled') ?>">
                      <label>
                        <input type="checkbox" name="license_conditions[<?php echo $condition['license_condition_id'] ?>]" value="1" <?php echo ($condition['optional'] ? false : 'disabled="disabled"') ?> <?php echo ($condition['checked'] ? 'checked="checked"' : false) ?> />
                        <?php echo $condition['text'] ?>
                      </label>
                      <?php if (!$condition['optional']) { ?>
                        <input type="hidden" name="license_conditions[<?php echo $condition['license_condition_id'] ?>]" value="1" />
                      <?php } ?>
                    </div>
                <?php } ?>
              </fieldset>
            <?php } ?>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript"><!--

  <!-- Common -->
  var fs_disabled = new Array();

  <!-- Hints -->

  function hideHints() {
    $('#productDescriptionHints, #productPackageHints, #productDemoHints, #productImageHints, #productVideoHints, #productAudioHints, #productPriceHints, #productLicenseHints').addClass('hide');
  }

  // Init
  hideHints();
  $('#productDescriptionHints').removeClass('hide');

  $('a[href=#general]').click(function () {
    hideHints();
    $('#productDescriptionHints').removeClass('hide');
  });

  $('a[href=#files]').click(function () {
    hideHints();
    $('#productPackageHints').removeClass('hide');
  });

  $('a[href=#demos]').click(function () {
    hideHints();
    $('#productDemoHints').removeClass('hide');
  });

  $('a[href=#images]').click(function () {
    hideHints();
    $('#productImageHints').removeClass('hide');
  });

  $('a[href=#videos]').click(function () {
    hideHints();
    $('#productVideoHints').removeClass('hide');
  });

  $('a[href=#audios]').click(function () {
    hideHints();
    $('#productAudioHints').removeClass('hide');
  });

  $('a[href=#prices]').click(function () {
    hideHints();
    $('#productPriceHints').removeClass('hide');
  });

  $('a[href=#license]').click(function () {
    hideHints();
    $('#productLicenseHints').removeClass('hide');
  });

  <!-- Submit form -->
  $('#submitForm').click(function() {

    if ($(this).hasClass('disabled')) {
      return false;
    }

    $('.product-package, .product-image, .product-audio, .product-video').val('');
    $('#productForm').submit();

  });

  <!-- File upload -->
  $('#inputPackage').change(function(){

    $('.product-image, .product-audio, .product-video').val('');

    var formData = new FormData($('#productForm').get(0));

    $.ajax({
        url: 'ajax/upload/package',
        type: 'POST',
                      xhr: function() {
                          var myXhr = $.ajaxSettings.xhr();
                          if(myXhr.upload){
                              myXhr.upload.addEventListener(
                                  'progress',
                                  function (e) {
                                    if (e.lengthComputable) {
                                      $('#packageProgress').attr({style: 'width:' + Math.round((e.loaded / e.total) * 100) + '%'});
                                    }
                                  },
                                  false);
                          }
                          return myXhr;
                      },
        beforeSend: function(e) {

          if (!fs_disabled.length) {
            $('#submitForm').addClass('disabled').prepend('<i class="glyphicon glyphicon-hourglass"></i> ');
          }

          fs_disabled.push('package');

          $('#packageControlSum, #inputPackageButton').addClass('hide');
          $('#packageUploadProgress').removeClass('hide');
          $('#productFileError, #aFiles .text-danger, #productFileSuccess').remove();
        },
        success: function (e) {

          fs_disabled.shift();

          if (!fs_disabled.length) {
            $('#submitForm i').remove();
            $('#submitForm').removeClass('disabled');
          }

          $('#packageUploadProgress').addClass('hide');
          $('#inputPackageButton').removeClass('hide');

          if (e['error_message']) {
            $('#aFiles').append(' <span class="text-danger">*</span>');
            $('#files').prepend('<div id="productFileError" class="alert alert-dismissible alert-danger">' + e['error_message'] + '</div>');
          } else if (e['success_message']) {
            $('#files').prepend('<div id="productFileSuccess" class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">×</button> ' + e['success_message'] + '</div>');
            $('#productFileTmpId').val(e['product_file_id']);
            $('#productFileMd5').html(e['hash_md5']);
            $('#productFileSha1').html(e['hash_sha1']);
            $('#packageControlSum, #inputPackageButton').removeClass('hide');
            $('#inputPackageButton legend').html('<?php echo tt("Update your package") ?>');
            $('#inputPackageButton span').html('<?php echo tt("Update file") ?>');
            $('#files .alert-danger').remove();
          } else {
            alert('Internal server error! Please, try again later.');
          }
        },
        error: function (e) {
          alert('Internal server error! Please, try again later.');
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
  });

  <!-- Image upload -->
  function imageUpload(r) {

    $('.product-package').val('');

    var formData = new FormData($('#productForm').get(0));

    $.ajax({
        url: 'ajax/upload/image?row=' + r,
        type: 'POST',
                      xhr: function() {
                          var myXhr = $.ajaxSettings.xhr();
                          if(myXhr.upload){
                              myXhr.upload.addEventListener(
                                  'progress',
                                  function (e) {
                                    if (e.lengthComputable) {
                                      $('#imageProgress' + r).attr({style: 'width:' + Math.round((e.loaded / e.total) * 100) + '%'});
                                    }
                                  },
                                  false);
                          }
                          return myXhr;
                      },
        beforeSend: function(e) {

          if (!fs_disabled.length) {
            $('#submitForm').addClass('disabled').prepend('<i class="glyphicon glyphicon-hourglass"></i> ');
          }

          fs_disabled.push('image-' + r);

          $('#imagePicture' + r).addClass('hide');
          $('#imageUpload' + r).removeClass('hide');

          $('#images .alert-danger, #images .alert-success, #aImages .text-danger').remove();
        },
        success: function (e) {

          fs_disabled.shift();

          if (!fs_disabled.length) {
            $('#submitForm i').remove();
            $('#submitForm').removeClass('disabled');
          }

          $('#imagePicture' + r).removeClass('hide');
          $('#imageUpload' + r).addClass('hide');


          if (e['error_message']) {

            $('#aImages .text-danger, #images .alert-danger').remove();

            $('#aImages').append(' <span class="text-danger">*</span>');
            $('#images').prepend('<div class="alert alert-dismissible alert-danger">' + e['error_message'] + '</div>');
          } else if (e['success_message']) {

            $('#aImages .text-danger, #images .alert-danger, #imagePicture' + r + ' span').remove();

            $('#images .alert.alert-dismissible.alert-success').remove();
            $('#images').prepend('<div class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">×</button> ' + e['success_message'] + '</div>');
            $('#productImageId' + r).val(e['product_image_id']);
            $('#imagePicture' + r + ' img').removeClass('hide').attr('src', e['url']);
            $('#imagePicture' + r).removeClass('btn btn-success');
            $('#inputImageWatermark' + r).attr('disabled', false);
          } else {
            alert('Internal server error! Please, try again later.');
          }
        },
        error: function (e) {
          alert('Internal server error! Please, try again later.');
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
  }

  <!-- Audio upload -->
  function audioUpload(r) {

    $('.product-package, .product-image').val('');

    var formData = new FormData($('#productForm').get(0));

    $.ajax({
        url: 'ajax/upload/audio?row=' + r,
        type: 'POST',
                      xhr: function() {
                          var myXhr = $.ajaxSettings.xhr();
                          if(myXhr.upload){
                              myXhr.upload.addEventListener(
                                  'progress',
                                  function (e) {
                                    if (e.lengthComputable) {
                                      $('#audioProgress' + r).attr({style: 'width:' + Math.round((e.loaded / e.total) * 100) + '%'});
                                    }
                                  },
                                  false);
                          }
                          return myXhr;
                      },
        beforeSend: function(e) {

          if (!fs_disabled.length) {
            $('#submitForm').addClass('disabled').prepend('<i class="glyphicon glyphicon-hourglass"></i> ');
          }

          fs_disabled.push('audio-' + r);

          $('#audioTrack' + r).addClass('hide');
          $('#audioUpload' + r).removeClass('hide');

          $('#audios .alert-danger, #audios .alert-success, #aAudios .text-danger').remove();
        },
        success: function (e) {

          fs_disabled.shift();

          if (!fs_disabled.length) {
            $('#submitForm i').remove();
            $('#submitForm').removeClass('disabled');
          }

          $('#audioTrack' + r).removeClass('hide');
          $('#audioUpload' + r).addClass('hide');

          if (e['error_message']) {

            $('#aAudios .text-danger, #audios .alert-danger').remove();

            $('#aAudios').append(' <span class="text-danger">*</span>');
            $('#audios').prepend('<div class="alert alert-dismissible alert-danger">' + e['error_message'] + '</div>');
          } else if (e['success_message']) {
            $('#aAudios .text-danger, #audios .alert-danger, #inputAudio' + r + ', #audioTrack' + r + ' span').remove();
            $('#audios .alert.alert-dismissible.alert-success').remove();
            $('#audios').prepend('<div class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">×</button> ' + e['success_message'] + '</div>');
            $('#productAudioId' + r).val(e['product_audio_id']);
            $('#audioOGG' + r).attr('src', e['ogg']);
            $('#audioMP3' + r).attr('src', e['mp3']);
            $('#audioTrack' + r + ' audio').removeClass('hide').load();
            $('#audioTrack' + r).removeClass('btn-file btn btn-success');
          } else {
            alert('Internal server error! Please, try again later.');
          }
        },
        error: function (e) {
          alert('Internal server error! Please, try again later.');
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
  }

  <!-- Video upload -->
  function videoUpload(r) {

    $('.product-package, .product-image').val('');

    var formData = new FormData($('#productForm').get(0));

    $.ajax({
        url: 'ajax/upload/video?row=' + r,
        type: 'POST',
                      xhr: function() {
                          var myXhr = $.ajaxSettings.xhr();
                          if(myXhr.upload){
                              myXhr.upload.addEventListener(
                                  'progress',
                                  function (e) {
                                    if (e.lengthComputable) {
                                      $('#videoProgress' + r).attr({style: 'width:' + Math.round((e.loaded / e.total) * 100) + '%'});
                                    }
                                  },
                                  false);
                          }
                          return myXhr;
                      },
        beforeSend: function(e) {

          if (!fs_disabled.length) {
            $('#submitForm').addClass('disabled').prepend('<i class="glyphicon glyphicon-hourglass"></i> ');
          }

          fs_disabled.push('video-' + r);

          $('#videoTrack' + r).addClass('hide');
          $('#videoUpload' + r).removeClass('hide');

          $('#videos .alert-danger, #videos .alert-success, #videos .alert-warning, #videos .alert-info, #aVideos .text-danger').remove();

          $('#videos').prepend('<div class="alert alert-dismissible alert-info"><?php echo tt('This may take some time. Please wait.') ?></div>');
        },
        success: function (e) {

          fs_disabled.shift();

          if (!fs_disabled.length) {
            $('#submitForm i').remove();
            $('#submitForm').removeClass('disabled');
          }

          $('#videoTrack' + r).removeClass('hide');
          $('#videoUpload' + r).addClass('hide');

          if (e['error_message']) {

            $('#aVideos .text-danger, #videos .alert-danger, #videos .alert-info, #videos .alert-warning').remove();

            $('#aVideos').append(' <span class="text-danger">*</span>');
            $('#videos').prepend('<div class="alert alert-dismissible alert-danger">' + e['error_message'] + '</div>');
          } else if (e['success_message']) {
            $('#aVideos .text-danger, #videos .alert-warning,  #videos .alert-info, #videos .alert-danger, #inputVideo' + r + ', #videoTrack' + r + ' span').remove();
            $('#videos .alert.alert-dismissible.alert-success').remove();
            $('#videos').prepend('<div class="alert alert-dismissible alert-success"><button type="button" class="close" data-dismiss="alert">×</button> ' + e['success_message'] + '</div>');
            $('#productVideoId' + r).val(e['product_video_id']);
            $('#videoOGG' + r).attr('src', e['ogg']);
            $('#videoMP4' + r).attr('src', e['mp4']);
            $('#videoTrack' + r + ' video').removeClass('hide').load();
            $('#videoTrack' + r).removeClass('btn-file btn btn-success');
          } else {
            alert('Internal server error! Please, try again later.');
          }
        },
        error: function (e) {
          alert('Internal server error! Please, try again later.');
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
  }

  <!-- Demo -->
  var demo_r = <?php echo $demo_max_row ?>;
  var demo_l = <?php echo $demo_total_rows ?>;

  <?php if ($demo_total_rows >= QUOTA_DEMO_PER_PRODUCT) { ?>
    $('#productDemo tfoot').hide();
  <?php } ?>

  function removeDemo(demo_r_id) {

    if ($('#inputDemoMain' + demo_r_id).is(':checked')) {
      recheck = true;
    } else {
      recheck = false;
    }

    $('#productDemoTr' + demo_r_id).remove();
    $('#productDemo tfoot').show();

    if (recheck) {
      $('#productDemo tbody tr:first-child input[name=main_demo]').prop('checked', true);
    }

    demo_l--;
  }

  function addDemo() {

    if (demo_l >= <?php echo QUOTA_DEMO_PER_PRODUCT ?>) {
      return false;
    }

    demo_r++;
    demo_l++;

    html  = '<tr id="productDemoTr' + demo_r + '">';
    if (demo_l == 1) {
      html += '<td class="form-group"><input checked="checked" type="radio" name="main_demo" id="inputDemoMain' + demo_r + '" value="' + demo_r + '" /></td>';
    } else {
      html += '<td class="form-group"><input type="radio" name="main_demo" id="inputDemoMain' + demo_r + '" value="' + demo_r + '" /></td>';
    }
    html += '<td class="form-group"><input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_URL_MAX_LENGTH ?>)" type="text" name="demo[' + demo_r + '][url]" class="form-control" id="inputDemoUrl' + demo_r + '" placeholder="<?php echo tt("URL address") ?>" value="" /></td>';
    html += '<td class="form-group"><?php foreach ($languages as $language_id => $language) { ?><?php if ($language_id != $this_language_id) { ?><div class="language-version" onclick="$(\'#demoTitle<?php echo $language_id ?>-' + demo_r + '\').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div> <?php } ?><div id="demoTitle<?php echo $language_id ?>-' + demo_r + '" <?php echo ($language_id != $this_language_id ? 'style="display:none"' : false) ?>><input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="demo[' + demo_r + '][title][<?php echo $language["language_id"] ?>]" class="form-control" id="inputDemoTitle' + demo_r + 'l<?php echo $language["language_id"] ?>" placeholder="<?php echo tt("Title") ?>" value="" /></div><?php } ?></td>';
    html += '<td class="form-group"><input type="hidden" name="demo[' + demo_r + '][sort_order]" value="' + demo_r + '" /><span onclick="removeDemo(' + demo_r + ')" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> <?php echo tt("Remove") ?></span></td>';
    html += '</tr>';

    $('#productDemo tbody').append(html, '\n');

    if (demo_l >= <?php echo QUOTA_DEMO_PER_PRODUCT ?>) {
      $('#productDemo tfoot').hide();
    }
  }


  <!-- Image -->
  var image_r = <?php echo $image_max_row ?>;
  var image_l = <?php echo $image_total_rows ?>;

  <?php if ($image_total_rows >= QUOTA_IMAGES_PER_PRODUCT) { ?>
    $('#productImage tfoot').hide();
  <?php } ?>

  function removeImage(image_r_id) {

    if ($('#inputImageMain' + image_r_id).is(':checked')) {
      recheck = true;
    } else {
      recheck = false;
    }

    $('#productImageTr' + image_r_id).remove();
    $('#productImage tfoot').show();

    if (recheck) {
      $('#productImage tbody tr:first-child input[name=main_image]').prop('checked', true);
    }

    image_l--;
  }

  function addImage() {

    if (image_l >= <?php echo QUOTA_IMAGES_PER_PRODUCT ?>) {
      return false;
    }

    image_r++;
    image_l++;

    html  = '<tr id="productImageTr' + image_r + '">';
    if (image_l == 1) {
      html += '<td class="form-group"><input checked="checked" type="radio" name="main_image" id="inputImageMain' + image_r + '" value="' + image_r + '" /></td>';
    } else {
      html += '<td class="form-group"><input type="radio" name="main_image" id="inputImageMain' + image_r + '" value="' + image_r + '" /></td>';
    }

    html += '<td class="form-group">';
    html += '<div class="btn-file btn btn-success" id="imagePicture' + image_r + '">';
    html += '  <span><i class="glyphicon glyphicon-upload"></i> <?php echo tt("Upload image") ?></span>';
    html += '  <img src="" alt="" class="hide" />';
    html += '  <input type="file" name="image[' + image_r + ']" id="inputImage' + image_r + '" value="" onchange="imageUpload(' + image_r + ')" class="product-image" />';
    html += '</div>';
    html += '<div class="hide" id="imageUpload' + image_r + '">';
    html += '  <div class="progress progress-striped active image-upload" >';
    html += '    <div class="progress-bar progress-bar-success" id="imageProgress' + image_r + '" ></div>';
    html += '  </div>';
    html += '</div>';
    html += '</td>';

    html += '<td class="form-group"><label class="control-label"><input type="checkbox" name="image[' + image_r + '][watermark]" id="inputImageWatermark' + image_r + '" value="1" /> <?php echo tt("Protect") ?></label></td>';
    html += '<td class="form-group"><?php foreach ($languages as $language_id => $language) { ?><?php if ($language_id != $this_language_id) { ?><div class="language-version" onclick="$(\'#imageTitle<?php echo $language_id ?>-' + image_r + '\').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div> <?php } ?><div id="imageTitle<?php echo $language_id ?>-' + image_r + '" <?php echo ($language_id != $this_language_id ? 'style="display:none"' : false) ?>><input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="image[' + image_r + '][title][<?php echo $language["language_id"] ?>]" class="form-control" id="inputImageTitle' + image_r + 'l<?php echo $language["language_id"] ?>" placeholder="<?php echo tt("Title") ?>" value="" /></div><?php } ?></td>';
    html += '<td class="form-group">';
    html += '  <input type="hidden" name="image[' + image_r + '][product_image_id]" value="" id="productImageId' + image_r + '" />';
    html += '  <input type="hidden" name="image[' + image_r + '][sort_order]" value="' + image_r + '" /><span onclick="removeImage(' + image_r + ')" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> <?php echo tt("Remove") ?></span>';
    html += '</td>';
    html += '</tr>';

    $('#productImage tbody').append(html, '\n');

    if (image_l >= <?php echo QUOTA_IMAGES_PER_PRODUCT ?>) {
      $('#productImage tfoot').hide();
    }
  }

  <!-- Audio -->
  var audio_r = <?php echo $audio_max_row ?>;
  var audio_l = <?php echo $audio_total_rows ?>;

  <?php if ($audio_total_rows >= QUOTA_AUDIO_PER_PRODUCT) { ?>
    $('#productAudio tfoot').hide();
  <?php } ?>

  function removeAudio(audio_r_id) {
    $('#productAudioTr' + audio_r_id).remove();
    $('#productAudio tfoot').show();

    audio_l--;
  }

  function addAudio() {

    if (audio_l >= <?php echo QUOTA_AUDIO_PER_PRODUCT ?>) {
      return false;
    }

    audio_r++;
    audio_l++;

    html  = '<tr id="productAudioTr' + audio_r + '">';
    html += '<td class="form-group">';
    html += '<div class="btn-file btn btn-success" id="audioTrack' + audio_r + '">';
    html += '  <span><i class="glyphicon glyphicon-upload"></i> <?php echo tt("Upload audio") ?></span>';
    html += '  <audio controls="controls" preload="none" class="hide">';
    html += '    <source id="audioOGG' + audio_r + '" src="" type="audio/ogg" />';
    html += '    <source id="audioMP3' + audio_r + '" src="" type="audio/mpeg" />';
    html += '    <?php echo tt('Your browser does not support the audio element.') ?>';
    html += '  </audio>';
    html += '  <input type="file" name="audio[' + audio_r + ']" id="inputAudio' + audio_r + '" value="" onchange="audioUpload(' + audio_r + ')" class="product-audio" />';
    html += '</div>';
    html += '<div class="hide" id="audioUpload' + audio_r + '">';
    html += '  <div class="progress progress-striped active audio-upload" >';
    html += '    <div class="progress-bar progress-bar-success" id="audioProgress' + audio_r + '" ></div>';
    html += '  </div>';
    html += '</div>';
    html += '</td>';

    html += '<td class="form-group"><label class="control-label"><input type="checkbox" name="audio[' + audio_r + '][cut]" /> <?php echo tt("Protect") ?></label></td>';
    html += '<td class="form-group"><?php foreach ($languages as $language_id => $language) { ?><?php if ($language_id != $this_language_id) { ?><div class="language-version" onclick="$(\'#audioTitle<?php echo $language_id ?>-' + audio_r + '\').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div> <?php } ?><div id="audioTitle<?php echo $language_id ?>-' + audio_r + '" <?php echo ($language_id != $this_language_id ? 'style="display:none"' : false) ?>><input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="audio[' + audio_r + '][title][<?php echo $language["language_id"] ?>]" class="form-control" id="inputAudioTitle' + audio_r + 'l<?php echo $language["language_id"] ?>" placeholder="<?php echo tt("Title") ?>" value="" /></div><?php } ?></td>';
    html += '<td class="form-group">';
    html += '  <input type="hidden" name="audio[' + audio_r + '][product_audio_id]" value="" id="productAudioId' + audio_r + '" />';
    html += '  <input type="hidden" name="audio[' + audio_r + '][sort_order]" value="' + audio_r + '" /><span onclick="removeAudio(' + audio_r + ')" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> <?php echo tt("Remove") ?></span>';
    html += '</td>';
    html += '</tr>';

    $('#productAudio tbody').append(html, '\n');

    if (audio_l >= <?php echo QUOTA_AUDIO_PER_PRODUCT ?>) {
      $('#productAudio tfoot').hide();
    }
  }


  <!-- Video -->
  var video_r = <?php echo $video_max_row ?>;
  var video_l = <?php echo $video_total_rows ?>;

  <?php if ($video_total_rows >= QUOTA_VIDEO_PER_PRODUCT) { ?>
    $('#productVideo tfoot').hide();
  <?php } ?>

  function removeVideo(video_r_id) {
    $('#productVideoTr' + video_r_id).remove();
    $('#productVideo tfoot').show();

    video_l--;
  }

  function addVideo() {

    if (video_l >= <?php echo QUOTA_VIDEO_PER_PRODUCT ?>) {
      return false;
    }

    video_r++;
    video_l++;

    html  = '<tr id="productVideoTr' + video_r + '">';
    html += '<td class="form-group">';
    html += '<div class="btn-file btn btn-success" id="videoTrack' + video_r + '">';
    html += '  <span><i class="glyphicon glyphicon-upload"></i> <?php echo tt("Upload video") ?></span>';
    html += '  <video controls="controls" preload="none" width="280" class="hide">';
    html += '    <source id="videoOGG' + video_r + '" src="" type="video/ogg" />';
    html += '    <source id="videoMP4' + video_r + '" src="" type="video/mp4" />';
    html += '    <?php echo tt('Your browser does not support the video element.') ?>';
    html += '  </video>';
    html += '  <input type="file" name="video[' + video_r + ']" id="inputVideo' + video_r + '" value="" onchange="videoUpload(' + video_r + ')" class="product-video" />';
    html += '</div>';
    html += '<div class="hide" id="videoUpload' + video_r + '">';
    html += '  <div class="progress progress-striped active video-upload" >';
    html += '    <div class="progress-bar progress-bar-success" id="videoProgress' + video_r + '" ></div>';
    html += '  </div>';
    html += '</div>';
    html += '</td>';

    html += '<td class="form-group"><label class="control-label"><input type="checkbox" name="video[' + video_r + '][reduce]" /> <?php echo tt("Protect") ?></label></td>';
    html += '<td class="form-group"><?php foreach ($languages as $language_id => $language) { ?><?php if ($language_id != $this_language_id) { ?><div class="language-version" onclick="$(\'#videoTitle<?php echo $language_id ?>-' + video_r + '\').toggle();"><?php echo sprintf(tt('%s version'), $languages[$language_id]['name']) ?></div> <?php } ?><div id="videoTitle<?php echo $language_id ?>-' + video_r + '" <?php echo ($language_id != $this_language_id ? 'style="display:none"' : false) ?>><input onkeyup="lengthFilter(this, <?php echo VALIDATOR_PRODUCT_TITLE_MAX_LENGTH ?>)" type="text" name="video[' + video_r + '][title][<?php echo $language["language_id"] ?>]" class="form-control" id="inputVideoTitle' + video_r + 'l<?php echo $language["language_id"] ?>" placeholder="<?php echo tt("Title") ?>" value="" /></div><?php } ?></td>';
    html += '<td class="form-group">';
    html += '  <input type="hidden" name="video[' + video_r + '][product_video_id]" value="" id="productVideoId' + video_r + '" />';
    html += '  <input type="hidden" name="video[' + video_r + '][sort_order]" value="' + video_r + '" /><span onclick="removeVideo(' + video_r + ')" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> <?php echo tt("Remove") ?></span>';
    html += '</td>';
    html += '</tr>';

    $('#productVideo tbody').append(html, '\n');

    if (video_l >= <?php echo QUOTA_VIDEO_PER_PRODUCT ?>) {
      $('#productVideo tfoot').hide();
    }
  }


  <!-- Special -->
  var special_r = <?php echo $special_max_row ?>;
  var special_l = <?php echo $special_total_rows ?>;

  <?php if ($special_total_rows >= QUOTA_SPECIALS_PER_PRODUCT) { ?>
    $('#productSpecial tfoot').hide();
  <?php } ?>

  function removeSpecial(special_r_id) {
    $('#productSpecialTr' + special_r_id).remove();
    $('#productSpecial tfoot').show();

    special_l--;
  }

  function addSpecial() {

    if (special_l >= <?php echo QUOTA_SPECIALS_PER_PRODUCT ?>) {
      return false;
    }

    special_r++;
    special_l++;

    html  = '<tr id="productSpecialTr' + special_r + '">';
    html +=   '<td class="form-group"><input type="text" name="special[' + special_r + '][regular_price]" class="form-control" id="specialRegularPrice' + special_r + '" placeholder="0.00" value="" /></td>';
    html +=   '<td class="form-group"><input type="text" name="special[' + special_r + '][exclusive_price]" class="form-control" id="specialExclusivePrice' + special_r + '" placeholder="0.00" value="" /></td>';
    html +=   '<td class="form-group"><input type="text" name="special[' + special_r + '][date_start]" data-date="<?php echo $date_today ?>" data-date-format="yyyy-mm-dd" class="form-control" id="specialDateStart' + special_r + '" placeholder="<?php echo $date_today ?>" value="" /></td>';
    html +=   '<td class="form-group"><input type="text" name="special[' + special_r + '][date_end]" data-date="<?php echo $date_today ?>" data-date-format="yyyy-mm-dd" class="form-control" id="specialDateEnd' + special_r + '" placeholder="<?php echo $date_tomorrow ?>" value="" /></td>';
    html +=   '<td class="form-group"><input type="hidden" name="special[' + special_r + '][sort_order]" value="' + special_r + '" /><span onclick="removeSpecial(' + special_r + ')" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i> <?php echo tt("Remove") ?></span></td>';
    html += '</tr>';

    $('#productSpecial tbody').append(html, '\n');

    $('#specialDateStart' + special_r + ',#specialDateEnd' + special_r).datepicker().on('changeDate', function(){
      $(this).datepicker('hide');
    });

    if (special_l >= <?php echo QUOTA_SPECIALS_PER_PRODUCT ?>) {
      $('#productSpecial tfoot').hide();
    }
  }
//--></script>
<?php echo $footer ?>
