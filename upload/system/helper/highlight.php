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

function highlight_license_condition($string, $allow_string, $disallow_string) {

    return str_replace(
        array(
            $disallow_string,
            $allow_string,
        ),
        array(
            '<span class="highlight-license-condition disallow">' . $disallow_string . '</span>',
            '<span class="highlight-license-condition allow">' . $allow_string . '</span>',
        ),
        $string);
}