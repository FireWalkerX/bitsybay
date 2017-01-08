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

final class FFmpeg {

    /**
     * @var string ffmpeg path
     */
    private $_ffmpeg;

    /**
     * @var string File path
     */
    private $_error = false;

    /**
     * Init
     *
     * @param string $ffmpeg path
     */
    public function __construct($ffmpeg) {

        $this->_ffmpeg = $ffmpeg;
    }

    /**
     * Convert file
     *
     * @param string $source Path to file
     * @param string $target Path to file
     * @param int $t Limit seconds
     * @param int $quality Quality
     *
     * @return bool TRUE|FALSE response
     */
    public function convert($source, $target, $t = 0, $quality = 10) {

        // If source file is not exists or not readable
        if (!file_exists($source)) {

            $this->_error = 'Source file is not exists!';

            return false;
        }

        // If source file is not exists or not readable
        if (!is_readable($source)) {

            $this->_error = 'Source file is not readable!';

            return false;
        }

        // If target file is exists
        if (file_exists($target)) {

            // Overwrite
            unlink($target);
        }

        // Validate data types
        $t = $t  > 0 ? sprintf('-t %s', (int) $t) : '';

        // Execute
        exec(
            sprintf(
                '%s -i %s %s -q %s %s',
                $this->_ffmpeg,
                $source,
                $t,
                $quality,
                $target
            ),
            $output,
            $return
        );

        // If file is exist
        return (file_exists($target)) ? true : false;
    }

    /**
     * Get error message
     *
     * @return string Error message
     */
    public function getError() {

        return $this->_error;
    }
}
