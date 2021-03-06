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

namespace Javanile\VtigerClient\System;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Response
{
    /**
     * Create success message.
     *
     * @return array
     */
    public static function success()
    {
        return [
            'success' => true,
        ];
    }

    /**
     * Create error message.
     *
     * @param $code
     * @param $message
     *
     * @return array
     */
    public static function error($code, $message)
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
