<?php

/**
 * LICENSE
 *
 * This source file is subject to the GNU General Public License, Version 3
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @package    PHP Library for Electrum JSONRPC API
 * @copyright  Copyright (c) 2016 Eugene Lifescale http://github.com/shaman/php-electrum-library
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License, Version 3
 */

class Electrum {

    private $_host;
    private $_port;
    private $_id;

    /**
     * @param string $host
     * @param int    $port
     * @param int    $id
     */
    public function __construct($host, $port, $id = 0) {

        $this->_host = $host;
        $this->_port = $port;
        $this->_id   = $id;
    }

    /**
     * @param  string $method
     * @param  array  $params key => value
     *
     * @return array response
     */
    public function __call($method, array $params = array()) {

        $request = json_encode(array(
            'method' => $method,
            'params' => $params,
            'id'     => $this->_id++
        ));

        $curl    = curl_init($this->_host . ':' . $this->_port);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => str_replace(array('[{', '}]'), array('{', '}'), $request)
        );

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return $error;
        } else {
            return $response;
        }
    }
}
