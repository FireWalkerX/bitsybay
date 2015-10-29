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

final class Cache {

    private $_ffmpeg;

    public function __construct(Registry $registry) {
        $this->_ffmpeg = $registry->get('ffmpeg');
    }

    /**
    * Image caching
    *
    * Resize & cache image into the file system. Returns the template image if product image is not exists
    *
    * @param mixed $name
    * @param int $user_id
    * @param int $width Resizing width
    * @param int $height Resizing height
    * @param bool $watermarked
    * @param bool $overwrite
    * @param bool $best_fit
    * @return string Cached Image URL
    */
    public function image($name, $user_id, $width, $height, $watermarked = false, $overwrite = false, $best_fit = false) {

        $storage     = DIR_STORAGE . $user_id . DIR_SEPARATOR . $name . '.' . STORAGE_IMAGE_EXTENSION;
        $cache       = DIR_IMAGE . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '-' . (int) $best_fit . '-' . $width . '-' . $height . '.' . STORAGE_IMAGE_EXTENSION;
        $watermark   = DIR_IMAGE . 'common' . DIR_SEPARATOR . 'watermark.png';
        $cached_url  = URL_BASE . 'image' . DIR_SEPARATOR . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '-' . (int) $best_fit . '-' . $width . '-' . $height . '.' . STORAGE_IMAGE_EXTENSION;

        // Force reset
        if ($overwrite && file_exists($overwrite)) {
            unlink($cache);
        }

        // If image is cached
        if (file_exists($cache)) {

            return $cached_url;

        // If image not cached
        } else {

            // Create directories by path if not exists
            $directories = explode(DIR_SEPARATOR, $cache);
            $path = '';
            foreach ($directories as $directory) {
                $path .= DIR_SEPARATOR . $directory;
                if (!is_dir($path) && false === strpos($directory, '.')) {
                    mkdir($path, 0755);
                }
            }

            // Prepare new image
            $image = new Image($storage);
            $image->resize($width, $height, 1, false, $best_fit);

            if ($watermarked) {
                $image->watermark($watermark);
            }

            $image->save($cache);
        }

        return $cached_url;
    }

    /**
    * Audio caching
    *
    * @param mixed $name
    * @param int $user_id
    * @param int $bit_rate kbps
    * @param bool $overwrite
    * @param bool|int $start
    * @param bool|int $end
    * @return string Cached Audio URL
    */
    public function audio($name, $user_id, $bit_rate = 320, $overwrite = true, $start = false, $end = false) {

        $storage     = DIR_STORAGE . $user_id . DIR_SEPARATOR . $name . '.' . STORAGE_AUDIO_EXTENSION;
        $cache       = DIR_AUDIO . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '.mp3'; // todo
        $cached_url  = URL_BASE . 'audio' . DIR_SEPARATOR . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '.mp3'; // todo

        // Force reset
        if ($overwrite && file_exists($overwrite)) {
            unlink($cache);
        }

        // If audio is cached
        if (file_exists($cache)) {

            return $cached_url;

        // If audio not cached
        } else {

            // Create directories by path if not exists
            $directories = explode(DIR_SEPARATOR, $cache);
            $path = '';
            foreach ($directories as $directory) {
                $path .= DIR_SEPARATOR . $directory;
                if (!is_dir($path) && false === strpos($directory, '.')) {
                    mkdir($path, 0755);
                }
            }

            // Create new cached file
            $this->_ffmpeg->convertToMP3( // Safari and MS Explorer are #Sucks (I'm waiting for 100% ogg support)
                $storage,
                $cache,
                $overwrite,
                $bit_rate,
                $start,
                $end
            );
        }

        return $cached_url;
    }

    /**
    * Reset cache for specific user
    *
    * @param int|bool $user_id
    */
    public function clean($user_id = false) {
        $this->_removeDirectory(DIR_IMAGE . 'cache' . DIR_SEPARATOR . $user_id);
        $this->_removeDirectory(DIR_AUDIO . 'cache' . DIR_SEPARATOR . $user_id);
    }

    /**
    * Recursive directory removing
    *
    * @param string $path
    */
    private function _removeDirectory($path) {

        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($path . DIR_SEPARATOR . $object) == 'dir') {
                        $this->_removeDirectory($path . DIR_SEPARATOR . $object);
                    } else {
                        unlink($path . DIR_SEPARATOR . $object);
                    }
                }
            }

            reset($objects);
            rmdir($path);
        }
    }
}
