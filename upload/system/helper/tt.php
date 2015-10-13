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

/**
* Prepare text string
*
* Convert all applicable characters to HTML entities
*
* @param string $string Raw string
* @param array $custom_translation key => value array
* @return string Prepared string
*/
function tt($string, array $custom_translation = array()) {

    // Get custom translation
    if ($custom_translation) {
        return htmlentities(isset($custom_translation[$string]) ? $custom_translation[$string] : $string);
    }

    // Get global translation
    global $_TRANSLATION;

    return htmlentities(isset($_TRANSLATION[$string]) ? $_TRANSLATION[$string] : $string);
}
