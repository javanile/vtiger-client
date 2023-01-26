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

class Profiler extends SystemDriver
{
    /**
     *
     */
    protected $env = [
        'VT_CLIENT_PROFILE' => 'file'
    ];

    /**
     *
     */
    protected $args = [
        'profile' => 'file'
    ];

    /**
     * @var array|false|string
     */
    protected $lockFile;

    /**
     * Logger constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        parent::__construct($args);

        $this->lockFile =  $this->path.'.lock';
    }

    /**
     *
     */
    public function begin($method)
    {
        if ($this->active) {
            return microtime(true);
        }
    }

    /**
     *
     */
    public function end($method, $beginTime, $return)
    {
        if (empty($this->active)) {
            return $return;
        }

        $until = time() + 15;
        while (file_exists($this->lockFile) && time() < $until) {
            usleep(100000);
        }

        touch($this->lockFile);
        $profiler = [];
        $accuracy = $until - time();
        if (file_exists($this->path)) {
            $profiler = (array)json_decode(file_get_contents($this->path), true);
        }
        if (empty($profiler[$this->tag][$method])) {
            $profiler[$this->tag][$method] = [
                'begin' => 0,
                'end' => 0,
                'count' => 0,
                'last' => 0,
                'total' => 0,
                'average' => 0,
                'max' => 0,
                'min' => 65535,
                'accuracy' => 65535,
            ];
        }
        $profiler[$this->tag][$method]['begin'] = $beginTime;
        $profiler[$this->tag][$method]['end'] = microtime(true);
        $profiler[$this->tag][$method]['count']++;
        $profiler[$this->tag][$method]['last'] = round($profiler[$this->tag][$method]['end'] - $profiler[$this->tag][$method]['begin'], 3);
        $profiler[$this->tag][$method]['total'] = round($profiler[$this->tag][$method]['total'] + $profiler[$this->tag][$method]['last'], 3);
        $profiler[$this->tag][$method]['average'] = round($profiler[$this->tag][$method]['total'] / $profiler[$this->tag][$method]['count'], 3);
        if ($profiler[$this->tag][$method]['last'] > $profiler[$this->tag][$method]['max']) {
            $profiler[$this->tag][$method]['max'] = $profiler[$this->tag][$method]['last'];
        }
        if ($profiler[$this->tag][$method]['last'] < $profiler[$this->tag][$method]['min']) {
            $profiler[$this->tag][$method]['min'] = $profiler[$this->tag][$method]['last'];
        }
        if ($accuracy < $profiler[$this->tag][$method]['accuracy']) {
            $profiler[$this->tag][$method]['accuracy'] = $accuracy;
        }
        file_put_contents($this->path, json_encode($profiler, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        unlink($this->lockFile);

        return $return;
    }
}
