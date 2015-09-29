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
 ?>

<?php echo $header; ?>
<div class="row">
  <div class="col-lg-12 home">
    <?php if ($user_is_logged) { ?>
      <div class="page-header text-center">
        <h2><?php echo $total_products ?> <?php echo tt('by') ?> <?php echo $total_sellers ?> <?php echo tt('for') ?> <?php echo $total_buyers ?></h2>
      </div>
      <?php echo $module_search ?>
    <?php } else { ?>
      <div class="bs-component welcome">
        <div class="jumbotron">
          <div class="col-lg-7">
            <h3><?php echo tt('Looking for a Marketplace to buy or sell digital creative in Bitcoin?') ?></h3>
            <?php echo tt('Then here you are:') ?>
            <ul>
              <li><?php echo $total_products ?> <?php echo tt('by') ?> <?php echo $total_sellers ?> <?php echo tt('for') ?> <?php echo $total_buyers ?></li>
              <li><?php echo tt('Trade from any country without bank fees and other restrictions') ?></li>
              <li><?php echo tt('Royalty Free and Exclusive licenses') ?></li>
              <li><?php echo sprintf(tt('%s Mb free disk space for all new sellers and +%s Mb for every next sale'), QUOTA_FILE_SIZE_BY_DEFAULT, QUOTA_BONUS_SIZE_PER_ORDER) ?></li>
              <li><?php echo tt('0% seller fee up to 2016, 11% later') ?></li>
              <li><?php echo tt('0% seller fee for project contributors forever') ?></li>
            </ul>
          </div>
          <div class="col-lg-4 col-lg-offset-1">
            <div class="bs-component">
              <form class="form-horizontal" action="<?php echo $login_action ?>" method="POST">
                <fieldset>
                  <legend><?php echo tt('Already have an account?') ?></legend>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <input type="text" name="login" class="form-control" id="inputLogin" placeholder="<?php echo tt('Email or username') ?>" value="">
                      <?php if (isset($error['login'])) { ?>
                        <div class="text-danger"><?php echo $error['login'] ?></div>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <input type="password" name="password" class="form-control" id="inputPassword" placeholder="<?php echo tt('Password') ?>" value="">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-lg-12">
                      <button type="submit" class="btn btn-primary sign-in-button"><?php echo tt('Sign In') ?></button>
                      <div class="col-lg-offset-2">
                       &nbsp;&nbsp; or <a href="<?php echo $href_account_create ?>"><?php echo tt('Join Us') ?></a>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="bs-component latest">
      <?php echo $module_latest; ?>
    </div>
  </div>
</div>
<?php echo $footer; ?>
