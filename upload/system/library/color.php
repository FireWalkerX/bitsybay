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

final class Color {

    /**
     * @var string File path
     */
    private $_file              = false;

    /**
     * @var array File info
     */
    private $_info              = array();

    /**
     * @var array Colors info
     */
    private $_colors            = array();

    /**
     * @var int Colors limit per image
     */
    private $_limit             = 5;

    /**
     * @var int Extracting granularity
     */
    private $_granularity       = 5;

    /**
     * @var int
     */
    private $_centering_x_left  = 4;

    /**
     * @var int
     */
    private $_centering_x_right = 4;

    /**
     * @var int
     */
    private $_centering_y_left  = 4;

    /**
     * @var int
     */
    private $_centering_y_right = 4;


    /**
     * Set image and file info extraction
     *
     * @param int $file
     * @return bool TRUE|FALSE
     */
    public function setImage($file) {

        if (file_exists($file) && is_file($file) && is_readable($file) && filesize($file) > 10 && $info = getimagesize($file)) {
            $this->_info = array(
                'width'  => $info[0],
                'height' => $info[1],
                'bits'   => $info['bits'],
                'mime'   => $info['mime']
            );

            if ($this->_info['mime'] == 'image/gif') {
                $this->_file = imagecreatefromgif($file);
            } elseif ($this->_info['mime'] == 'image/png') {
                $this->_file = imagecreatefrompng($file);
            } elseif ($this->_info['mime'] == 'image/jpeg') {
                $this->_file = imagecreatefromjpeg($file);
            }

            return true;

        } else {
            $this->_file = false;
            $this->_info = array();

            return false;
        }
    }

    /**
     * Set colors limit per image
     *
     * @param int $limit
     */
    public function setLimit($limit) {
        $this->_limit = (int) $limit;
    }

    /**
     * Set granularity
     *
     * @param int $granularity
     */
    public function setGranularity($granularity) {
        $this->_granularity = (int) $granularity;
    }

    /**
     * Set Centering X Left
     *
     * @param int $centering
     */
    public function setCenteringXLeft($centering) {
        $this->_centering_x_left = (float) $centering;
    }

    /**
     * Set Centering X Right
     *
     * @param int $centering
     */
    public function setCenteringXRight($centering) {
        $this->_centering_x_right = (float)$centering;
    }

    /**
     * Set Centering Y Left
     *
     * @param int $centering
     */
    public function setCenteringYLeft($centering) {
        $this->_centering_y_left = (float)$centering;
    }

    /**
     * Set Centering Y Right
     *
     * @param int $centering
     */
    public function setCenteringYRight($centering) {
        $this->_centering_y_right = (float)$centering;
    }

    /**
     * Get colors limit per image
     *
     * @return int
     */
    public function getLimit() {
        return $this->_limit;
    }

    /**
     * Get granularity
     *
     * @return int
     */
    public function getGranularity() {
        return $this->_granularity;
    }

    /**
     * Get centering X Left
     *
     * @return int
     */
    public function getCenteringXLeft() {
        return $this->_centering_x_left;
    }

    /**
     * Get centering X Right
     *
     * @return int
     */
    public function getCenteringXRight() {
        return $this->_centering_x_right;
    }

    /**
     * Get centering Y Left
     *
     * @return int
     */
    public function getCenteringYLeft() {
        return $this->_centering_y_left;
    }

    /**
     * Get centering Y Right
     *
     * @return int
     */
    public function getCenteringYRight() {
        return $this->_centering_y_right;
    }

    /**
     * Get extracted colors data
     *
     * @return array
     */
    public function getColors() {

        if (!$this->_file) {
            return false;
        }

        $this->_colors     = array();
        $this->_granularity = max(1, abs((int)$this->_granularity));

        for ($x = $this->_info['width']/$this->_centering_x_left; $x < $this->_info['width'] - $this->_info['width']/$this->_centering_x_right; $x += $this->_granularity) {
            for($y = $this->_info['height']/$this->_centering_y_left; $y < $this->_info['height'] - $this->_info['height']/$this->_centering_y_right; $y += $this->_granularity) {

                // Extract RGB
                $thisColor = imagecolorat($this->_file, $x, $y);
                $rgb       = imagecolorsforindex($this->_file, $thisColor);
                $red       = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green     = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue      = round(round(($rgb['blue'] / 0x33)) * 0x33);

                // Extract HEX
                $hex       = sprintf('%02X%02X%02X', $red, $green, $blue);
                $id        = '#' . $hex; // key compatibility

                // Extract HSV
                $hsv = $this->_rgbToHsv($rgb['red'], $rgb['green'], $rgb['blue']);

                // Prepare output
                if(array_key_exists($id, $this->_colors)) {
                    $this->_colors[$id]['frequency']++;
                } else {
                    $this->_colors[$id]['frequency'] = 1;
                }

                $this->_colors[$id]['hex']        = $hex;
                $this->_colors[$id]['hue']        = $hsv['hue'];
                $this->_colors[$id]['saturation'] = $hsv['saturation'];
                $this->_colors[$id]['value']      = $hsv['value'];
                $this->_colors[$id]['red']        = $rgb['red'];
                $this->_colors[$id]['green']      = $rgb['green'];
                $this->_colors[$id]['blue']       = $rgb['blue'];
            }
        }

        arsort($this->_colors);

        return array_slice($this->_colors, 0, $this->_limit);
    }

    /**
     * Convert RGB to HSV
     *
     * @param int $R
     * @param int $G
     * @param int $B
     *
     * @return array
     */
    private function _rgbToHsv($R, $G, $B) {

        $HSL = array();

        $var_R = ($R / 255);
        $var_G = ($G / 255);
        $var_B = ($B / 255);

        $var_Min = min($var_R, $var_G, $var_B);
        $var_Max = max($var_R, $var_G, $var_B);
        $del_Max = $var_Max - $var_Min;

        $V = $var_Max;

        if ($del_Max == 0) {
            $H = 0;
            $S = 0;
        } else {
            $S = $del_Max / $var_Max;

            $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

            if      ($var_R == $var_Max) $H = $del_B - $del_G;
            else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
            else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

            if ($H<0) $H++;
            if ($H>1) $H--;
        }

        $HSL['hue']        = $H;
        $HSL['saturation'] = $S;
        $HSL['value']      = $V;

        return $HSL;
    }
}
