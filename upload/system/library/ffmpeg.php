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
     * Convert file to OGG
     *
     * @param string $source Path to file
     * @param string $target Path to file
     * @param bool $overwrite TRUE|FALSE
     * @param int $bit_rate kbps
     *
     * @return bool TRUE|FALSE response
     */
    public function convertToOGG($source, $target, $overwrite = false, $bit_rate = 320) {

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
            if ($overwrite) unlink($target);

            // Error
            else {
                $this->_error = 'Target file already exist!';

                return false;
            }
        }

        // Execute
        exec(
            sprintf(
                '%s -i %s -ab %sk -f ogg %s',
                $this->_ffmpeg,
                $source,
                $bit_rate,
                $target
            ),
            $output,
            $return
        );

        return true;
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
