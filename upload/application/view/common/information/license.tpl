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
            <h1 id="forms"><?php echo tt('Licensing Policy') ?></h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php echo $module_breadcrumbs ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-9">
        <section>
            <article>
                <h2></h2>
                <h3><?php echo tt('Definitions') ?></h3>
                <p>
                    <ul>
                        <li><?php echo tt('The Item means copyrighted product offered for sale') ?>;</li>
                        <li><?php echo tt('Seller means a person or business that registered on this website and sells the Item') ?>;</li>
                        <li><?php echo tt('Buyer means a person or business that registered on this website and buys the Item') ?>;</li>
                        <li><?php echo tt('Copyright means legal right that grants the creator of an original work absolute and exclusive rights to its use and distribution') ?>.</li>
                    </ul>
                </p>
                <?php foreach ($licenses as $license) { ?>
                    <h2><?php echo $license['name'] ?></h2>
                    <div class="well">
                        <p><?php echo $license['description'] ?></p>
                    </div>
                    <?php foreach ($license['conditions'] as $condition) { ?>
                        <div style="margin-left:20px">
                            <span class="glyphicon glyphicon-circle-arrow-right"></span> <?php echo $condition ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </article>
        </section>
    </div>
</div>
<?php echo $footer ?>
