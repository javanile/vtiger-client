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
    protected $env = 'VT_DEBUGGER_FILE';

    /**
     *
     */
    protected $tag;

    /**
     * @var array|false|string
     */
    protected $path;

    /**
     * @var array|false|string
     */
    protected $lockFile;

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
        $this->path = getenv($this->env);
        $this->lockFile =  $this->path.'.lock';
        $this->active = (bool)$this->path;
        $this->tag = $this->setTag(isset($args['username']) ? $args['username'] : null, $args['endpoint']);
    }

    /**
     *
     */
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
