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

class Cache extends SystemDriver
{
    /**
     *
     */
    protected $env = 'VT_CLIENT_CACHE';

    /**
     *
     */
    protected $cache;
}
