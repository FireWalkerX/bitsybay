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

<?php echo $header ?>
  <div class="row">
    <div class="col-lg-12">
      <div class="page-header">
        <h1 id="forms"><?php echo tt('About Us') ?></h1>
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
      <section>
        <article>
          <p>
            <img src="image/common/logo-sm.png" alt="<?php echo PROJECT_NAME ?>" />
          </p>
          <p>
            <?php echo sprintf(tt('%s is a simple and minimalistic service to help you buy and or sell creative digital products with cryptocurrency like BitCoin.'), PROJECT_NAME) ?><br />
            <?php echo tt('It includes a marketplace for legal CMS extensions, illustrations, photos, themes and other creative assets from various authors.') ?>
          </p>
          <p>
            <?php echo tt('Buy or sell any available content fast and directly, from any country without compromises.') ?>
          </p>
          <h2><?php echo tt('Philosophy') ?></h2>
          <p> 
            <?php echo tt('We love independence. We are not corporation. We respect copyright.') ?><br /> 
            <?php echo tt('Project was made by and for creative peoples who love a freedom and looking for easy trading around the world.') ?><br /> 
            <?php echo tt('This project based on our open source engine, available for contributors and trademark-free. It makes world better.') ?>
           </p>
          <h2><?php echo tt('Engine') ?></h2>
          <p>
            <?php echo tt('Our Engine is 100% open source licensed under GNU GPL version 3.') ?><br />
            <?php echo tt('You can fork us on') ?> <a href="http://github.com/bitsybay" target="_blank" rel="nofollow">GitHub</a> <?php echo tt('to help improve this project or use this engine as an example project for your own BitCoin service.') ?>
          </p>
          <h2><?php echo tt('Other') ?></h2>
          <p>
            <?php echo tt('Service is currently in Beta mode, so if you find any bug, we would greatly appreciate it if you notify us.') ?><br />
            <?php echo tt('Also, if you have any questions, please visit the') ?> <a href="faq">F.A.Q.</a> <?php echo tt('or') ?> <a href="contact"><?php echo tt('Contact Us') ?></a> <?php echo tt('pages') ?>.
          </p>
          <p>&mdash;</p>
          <p><?php echo tt('With best regards,') ?><br />
            <a href="team"><?php echo sprintf(tt('%s Team'), PROJECT_NAME) ?></a><br />
          </p>
        </article>
      </section>
    </div>
  </div>
<?php echo $footer ?>
