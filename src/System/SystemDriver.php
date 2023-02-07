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

class SystemDriver
{
    /**
     *
     */
    protected $env;

    /**
     *
     */
    protected $tag;

    /**
     * @var array|false|string
     */
    protected $path;

    /**
     * @var bool
     */
    protected $active;

    /**
     * Logger constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        foreach ($this->env as $env => $attribute) {
            $this->{$attribute} = getenv($env);
        }

        $this->active = (bool)$this->path;
        $this->tag = $this->setTag($args['username'] ?? 'unknown', $args['endpoint'] ?? 'unknown');
    }

    /**
     * @param $username
     * @param $endpoint
     */
    public static function setTag($username, $endpoint)
    {
        $host = parse_url($endpoint, PHP_URL_HOST);

        return $username ? $username.'@'.$host : $host;
    }
}
