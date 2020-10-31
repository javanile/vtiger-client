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

class Factory
{
    /**
     *
     * @param $code
     * @param $message
     *
     * @return array
     */
    public static function createError($code, $message)
    {
        return [
            'success' => false,
            'error'   => [
                'code'    => $code,
                'message' => $message,
            ],
        ];
    }
}
