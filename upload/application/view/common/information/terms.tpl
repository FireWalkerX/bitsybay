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
        <h1 id="forms"><?php echo tt('Terms of Service') ?></h1>
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
          <h2><?php echo tt('If you’re a buyer') ?></h2>
          <div style="padding-top:20px">
            <div class="glyphicon glyphicon-shopping-cart" style="color:#CCC;float:left;font-size:5em"></div>
            <div style="margin-left:6em">
              <ul>
                <li><?php echo tt('All commercial (non-public) submissions to this site are subject to') ?> <a href="licenses"><?php echo tt('Licensing Policy') ?></a>;</li>
                <li><?php echo tt('Full responsibility for all commercial content of this site remains with the author’s of such content') ?>;</li>
                <li><?php echo tt('The author reserves the right to change, add to or delete all or part of the commercial content without prior notice or to discontinue publication of all or part of the commercial content temporarily or permanently') ?>;</li>
                <li><?php echo tt('We do not provide any support or warranties for purchased content and do not refund any payments.') ?></li>
              </ul>
            </div>
          </div>
          <h2><?php echo tt('If you’re a seller') ?></h2>
          <div style="padding-top:20px">
            <div class="glyphicon glyphicon-bitcoin" style="color:#CCC;float:left;font-size:5em"></div>
            <div style="margin-left:6em">
              <ul>
                <li><?php echo tt('You represent and warrant that you are the owner of, or that you have all rights to all your submissions') ?>;</li>
                <li><?php echo tt('You responsible for any copyright violations') ?>;</li>
                <li><?php echo tt('If you provide any content that is fraudulent, untrue, inaccurate, incomplete, not current, or illegal to') ?> <?php echo tt(HOST_COUNTRY) ?> <?php echo tt('laws, we reserve the right to suspend or terminate your account without notice and to refuse any and all current and future use of the website') ?>;</li>
                <li><?php echo sprintf(tt('We charge a %s%% fee on the selling price except'), FEE_PER_ORDER) ?> <a href="team"><?php echo tt('project contributors') ?></a>.</li>
              </ul>
            </div>
          </div>
          <h2><?php echo tt('Data Privacy') ?></h2>
          <div style="padding-top:20px">
            <div class="glyphicon glyphicon-user" style="color:#CCC;float:left;font-size:5em"></div>
            <div style="margin-left:6em">
              <ul>
                <li><?php echo tt('We save and use your personal information only for providing services of this website and for contacting you') ?>;</li>
                <li><?php echo tt('We do not give your personal information to third parties') ?>;</li>
                <li><?php echo tt('We add cookies in various places on our website. They serve only to ensure correct login, saving language settings and for providing affiliate program') ?>;</li>
                <li><?php echo sprintf(tt('For further information or questions regarding data protection at %s, please'), PROJECT_NAME) ?> <a href="contact"><?php echo tt('contact us') ?></a>.</li>
              </ul>
            </div>
          </div>
          <h2><?php echo tt('General Terms') ?></h2>
          <div style="padding-top:20px">
            <div class="glyphicon glyphicon-hand-right" style="color:#CCC;float:left;font-size:5em"></div>
            <div style="margin-left:6em">
              <ul>
                <li><?php echo tt('We reserve the right to update or modify these Terms and Conditions at any time without prior notice') ?>;</li>
                <li><?php echo tt('Should you believe that any material or content published on this website, infringe on copyright, or is illegal, or that of another, please') ?> <a href="contact"><?php echo tt('contact us') ?></a>;</li>
                <li><?php echo tt('We make no representations that this website will meet your requirements') ?>;</li>
                <li><?php echo tt('This Service / Software is provided "as is", without warranty of any kind, express or implied') ?>;</li>
                <li><?php echo tt('If you do not agree to this Terms & Conditions, do not use this website') ?>.</li>
              </ul>
            </div>
          </div>
        </article>
      </section>
    </div>
  </div>
<?php echo $footer ?>
