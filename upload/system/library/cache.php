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

        $storage         = DIR_STORAGE . $user_id . DIR_SEPARATOR . $name . '.' . STORAGE_IMAGE_EXTENSION;
        $cache           = DIR_IMAGE . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '-' . (int) $best_fit . '-' . $width . '-' . $height . '.' . STORAGE_IMAGE_EXTENSION;
        $watermark_black = DIR_IMAGE . 'common' . DIR_SEPARATOR . 'watermark-black.png';
        $watermark_white = DIR_IMAGE . 'common' . DIR_SEPARATOR . 'watermark-white.png';
        $cached_url      = URL_BASE . 'image' . DIR_SEPARATOR . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '-' . (int) $best_fit . '-' . $width . '-' . $height . '.' . STORAGE_IMAGE_EXTENSION;

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

                $average = new Imagick($storage);
                $average->resizeImage(1, 1, Imagick::FILTER_POINT, 0);

                $pixel = $average->getImagePixelColor(1, 1);
                $color = $pixel->getColor();

                $brightness = (0.299 * $color['r'] + 0.587 * $color['g'] + 0.114 * $color['b']) * 100 / 255;

                if ($brightness < 10) {
                    $image->watermark($watermark_white);
                } else {
                    $image->watermark($watermark_black);
                }

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
    * @param string $extension MP3 or OGA
    * @param bool $overwrite
    * @param int $limit_seconds
    * @return string Cached Audio URL
    */
    public function audio($name, $user_id, $extension, $overwrite = false, $limit_seconds = 0) {

        $extension   = strtolower($extension);
        $storage     = DIR_STORAGE . $user_id . DIR_SEPARATOR . $name . '.' . STORAGE_AUDIO_EXTENSION;
        $cache       = DIR_AUDIO . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '.' . $extension;
        $cached_url  = URL_BASE . 'audio' . DIR_SEPARATOR . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '.' . $extension;

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
            $this->_ffmpeg->convert($storage, $cache, $limit_seconds);
        }

        return $cached_url;
    }

    /**
    * Video caching
    *
    * @param mixed $name
    * @param int $user_id
    * @param string $extension MP4 or OGV
    * @param bool $overwrite
    * @param int $quality
    * @return string Cached Video URL
    */
    public function video($name, $user_id, $extension, $overwrite = false, $quality = 0) {

        $extension   = strtolower($extension);
        $storage     = DIR_STORAGE . $user_id . DIR_SEPARATOR . $name . '.' . STORAGE_VIDEO_EXTENSION;
        $cache       = DIR_VIDEO . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '.' . $extension;
        $cached_url  = URL_BASE . 'video' . DIR_SEPARATOR . 'cache' . DIR_SEPARATOR . $user_id . DIR_SEPARATOR . $name . '.' . $extension;

        // Force reset
        if ($overwrite && file_exists($overwrite)) {
            unlink($cache);
        }

        // If video is cached
        if (file_exists($cache)) {

            return $cached_url;

        // If video not cached
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
            $this->_ffmpeg->convert($storage, $cache, 0, $quality > 0 ? $quality : PRODUCT_VIDEO_QUALITY);

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
        $this->_removeDirectory(DIR_VIDEO . 'cache' . DIR_SEPARATOR . $user_id);
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
