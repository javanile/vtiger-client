<?php

/**
 * File description.
 *
 * PHP version 5
 *
 * @category   WebServiceClient
 *
 * @author     Francesco Bianco
 * @copyright
 * @license    MIT
 */

namespace Javanile\VtigerClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Functions
{
    /**
     * @param $flag
     * @param mixed $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param $id
     *
     * @return string|null
     */
    public static function isElementId($id)
    {
        return preg_match('/[0-9]+x[0-9]+/', $id);
    }

    /**
     * @param $id
     *
     * @return string|null
     */
    public static function getTypeIdPrefix($id)
    {
        $id = explode('x', trim($id));
        $id = trim($id[0]);

        return $id ? $id : null;
    }

    /**
     * @param $user_password
     * @param $user_name
     * @param string $crypt_type
     *
     * @return string
     */
    protected static function encryptPassword($user_password, $user_name, $crypt_type = '')
    {
        $salt = substr($user_name, 0, 2);

        if ($crypt_type == '') {
            $crypt_type = 'PHP5.3MD5';
        }

        if ($crypt_type == 'MD5') {
            $salt = '$1$' . $salt . '$';
        } elseif ($crypt_type == 'BLOWFISH') {
            $salt = '$2$' . $salt . '$';
        } elseif ($crypt_type == 'PHP5.3MD5') {
            $salt = '$1$' . str_pad($salt, 9, '0');
        }

        $encrypted_password = crypt($user_password, $salt);

        return $encrypted_password;
    }
}
