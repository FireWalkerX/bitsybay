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
      <h1 id="forms"><?php echo tt('Account recovery') ?></h1>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <?php echo $module_breadcrumbs ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-6">
    <?php echo $alert_success ?>
    <?php echo $alert_danger ?>
    <div class="well bs-component">
      <form class="form-horizontal" action="<?php echo $action ?>" method="POST">
        <fieldset>
          <legend><?php echo tt("What’s your registered email?") ?></legend>
          <div class="form-group<?php if (isset($error['email'])) { ?> has-error<?php } ?>">
            <div class="col-lg-12">
              <input type="text" name="email" class="form-control" id="inputEmail" placeholder="<?php echo tt('Email') ?>" value="<?php echo $email ?>">
              <?php if (isset($error['email'])) { ?>
                <div class="text-danger"><?php echo $error['email'] ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-10">
              <button type="submit" class="btn btn-primary"><?php echo tt('Continue') ?></button>
            </div>
          </div>
        </fieldset>
      </form>
    </div>
  </div>
  <div class="col-lg-5 col-lg-offset-1">
    <div class="bs-component">
      <h3><?php echo tt('I’ve remember my password!') ?></h3>
      <ul>
        <li><?php echo tt('Nice, visit the') ?> <a href="<?php echo $href_account_account_login ?>"><?php echo tt('Login page') ?></a></li>
        <li><?php echo tt('If you don’t have an Account, please visit the') ?> <a href="<?php echo $href_account_account_create ?>"><?php echo tt('Registration page') ?></a></li>
      </ul>
      <h4><?php echo tt('Help resources') ?></h4>
      <ul>
        <li><?php echo tt('Visit the') ?> <a href="<?php echo $href_common_information_faq ?>"><?php echo tt('F.A.Q page') ?></a></li>
        <li><?php echo tt('To get specific answers') ?> <a href="<?php echo $href_common_contact ?>"><?php echo tt('Contact Us') ?></a></li>
      </ul>
    </div>
  </div>
</div>
<?php echo $footer ?>
