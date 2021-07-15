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

class Debugger
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
    protected $file;

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
        $this->file = getenv($this->env);
        $this->lockFile = $this->file.'.lock';
        $this->active = (bool)$this->file;
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
