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

final class Translate {

    private $_error  = false;
    private $_status = false;

    /**
     * @param string $text
     * @param string $source
     * @param string $target
     *
     * @return string|false translatedText
     */
    public function string($text, $source, $target) {
        return $text;
    }

    /**
     * @return string|false
     */
    public function getError() {
        return $this->_error;
    }

    /**
     * @return string|false
     */
    public function getStatus() {
        return $this->_status;
    }
}
