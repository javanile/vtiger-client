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

class Logger
{
    /**
     * @var array|false|string
     */
    protected $file;

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
        $this->file = getenv('VT_CLIENT_LOG_FILE');
        $this->active = $this->file ? true : false;
    }

    /**
     * @param $object
     * @param $trace
     * @return mixed
     */
    public function log($object, $trace)
    {
        if ($this->active && $this->file) {
            $this->appendLogLine($object, $trace);
        }

        return $object;
    }

    /**
     *
     */
    protected function appendLogLine($object, $trace)
    {
        $line = date('Y-m-d H:i:s');
        $line .= ' '.json_encode($object, JSON_UNESCAPED_SLASHES);
        $line .= "\n";

        file_put_contents($this->file, $line, FILE_APPEND);
    }
}
