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

class ValidatorUpload {

    /**
    * Validate file
    *
    * @param array $file
    * @param int $max_file_size MB
    * @param string $allowed_file_extensions
    * @return bool TRUE if valid ot FALSE if else
    */
    static public function fileValid($file, $max_file_size, $allowed_file_extensions) {
        
        // Dependencies test
        if (!isset($file['tmp_name']) || !isset($file['name'])) {
            return false;

        // Check for array keys existing
        } else if (empty($file['tmp_name']) || empty($file['name'])) {
            return false;

        // Test for allowed extension
        } else if (mb_strtolower($allowed_file_extensions) != @pathinfo($file['name'], PATHINFO_EXTENSION)) {
            return false;

        // Test for maximum file size
        } else if ($max_file_size < @filesize($file['tmp_name']) / 1000000) {
            return false;
            
        // ClamAV scanning for viruses
        } else if (CL_VIRUS == cl_scanfile($file['tmp_name'])) {
            return false;
        }

        // Success
        return true;
    }

    /**
     * Validate package
     *
     * @param array $package
     * @param int $max_file_size MB
     * @param array $allowed_file_extensions
     * @return bool TRUE if valid ot FALSE if else
     */
    static public function packageValid($package, $max_file_size, array $allowed_file_extensions = array('zip')) {

        foreach ($allowed_file_extensions as $extension) {
            if (self::fileValid($package, $max_file_size, $extension)) {

                // Manipulation test
                $zip = new ZipArchive();
                if (true === $zip->open($package['tmp_name'], ZipArchive::CHECKCONS)) {

                    $zip->close();
                    return true;
                }

                $zip->close();

                break;
            }
        }

        return false;
    }


    /**
    * Validate image
    *
    * @param array $image
    * @param int $max_file_size MB
    * @param int $min_width PX
    * @param int $min_height PX
    * @param array $allowed_file_extensions
    * @return bool TRUE if valid ot FALSE if else
    */
    static public function imageValid($image, $max_file_size, $min_width, $min_height, array $allowed_file_extensions = array('jpg', 'jpeg', 'png')) {

        foreach ($allowed_file_extensions as $extension) {
            if (self::fileValid($image, $max_file_size, $extension)) {

                // Manipulation test
                if (!$image_size = @getimagesize($image['tmp_name'])) {
                    return false;
                }

                // Image size test
                if (!isset($image_size[0]) || !isset($image_size[1]) ||
                    empty($image_size[0]) ||  empty($image_size[1]) ||
                    $image_size[0] < $min_width || $image_size[1] < $min_height) {

                    return false;
                }

                return true;
            }
        }
        
        return false;
    }

    /**
    * Validate image
    *
    * @param array $audio
    * @param int $max_file_size MB
    * @param array $allowed_file_extensions
    * @return bool TRUE if valid ot FALSE if else
    */
    static public function audioValid($audio, $max_file_size, array $allowed_file_extensions = array('mp3', 'ogg', 'waw', 'wawe', 'mka', 'wma', 'mp4', 'm4a')) {
        
        foreach ($allowed_file_extensions as $extension) {
            if (self::fileValid($audio, $max_file_size, $extension)) {

                return true;
            }
        }
        
        return false;
    }

    /**
    * Validate video
    *
    * @param array $video
    * @param int $max_file_size MB
    * @param array $allowed_file_extensions
    * @return bool TRUE if valid ot FALSE if else
    */
    static public function videoValid($video, $max_file_size, array $allowed_file_extensions = array('mov', 'mpeg4', 'avi', 'wmv', 'mpegps', 'flv', '3gpp', 'webm')) {

        foreach ($allowed_file_extensions as $extension) {
            if (self::fileValid($video, $max_file_size, $extension)) {

                return true;
            }
        }

        return false;
    }
}
