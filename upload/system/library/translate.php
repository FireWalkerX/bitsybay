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

final class Translate {

    /**
     * @param string $text
     * @param string $source
     * @param string $target
     *
     * @return string|false translatedText
     */
    function myMemory($text, $source, $target) {

        $url = 'http://mymemory.translated.net/api/get?q=' . rawurlencode($text) . '&langpair=' . $source . '|' . $target;

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        curl_close($handle);

        $response = json_decode($response, true);

        if ($response['responseStatus'] == 200 && isset($response['responseData']['translatedText'])) {
            return $response['responseData']['translatedText'];
        } else {
            return false;
        }
    }
}
