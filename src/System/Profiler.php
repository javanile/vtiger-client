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
    protected $env = 'VT_CLIENT_PROFILING_FILE';

    /**
     *
     */
    protected $profiler = [];

    /**
     *
     */
    public function begin($method)
    {
        if (empty($this->active)) {
            return;
        }

        if (empty($this->profiler[$this->tag][$method])) {
            $this->profiler[$this->tag][$method] = [
                'begin' => 0,
                'end' => 0,
                'count' => 0,
                'last' => 0,
                'total' => 0,
                'average' => 0,
                'max' => 0,
                'min' => 65535,
            ];
        }

        $this->profiler[$this->tag][$method]['begin'] = microtime(true);
    }

    /**
     *
     */
    public function end($method, $return)
    {
        if (empty($this->active)) {
            return $return;
        }

        $until = time() + 5;
        while (file_exists($this->lockFile) && time() < $until) {
            usleep(100000);
        }

        touch($this->lockFile);
        if (file_exists($this->path)) {
            $past = (array)json_decode(file_get_contents($this->file), true);
            if (isset($past[$this->tag][$method]['count'])) {
                $this->profiler[$this->tag][$method]['count'] += $past[$this->tag][$method]['count'];
            }
            if (isset($past[$this->tag][$method]['total'])) {
                $this->profiler[$this->tag][$method]['total'] += $past[$this->tag][$method]['total'];
            }
            if (isset($past[$this->tag][$method]['max'])) {
                $this->profiler[$this->tag][$method]['max'] += $past[$this->tag][$method]['max'];
            }
            if (isset($past[$this->tag][$method]['min'])) {
                $this->profiler[$this->tag][$method]['min'] += $past[$this->tag][$method]['min'];
            }
        }
        $this->profiler[$this->tag][$method]['end'] = microtime(true);
        $this->profiler[$this->tag][$method]['count']++;
        $this->profiler[$this->tag][$method]['last'] = round($this->profiler[$this->tag][$method]['end'] - $this->profiler[$this->tag][$method]['begin'], 3);
        $this->profiler[$this->tag][$method]['total'] += round($this->profiler[$this->tag][$method]['last'], 3);
        $this->profiler[$this->tag][$method]['average'] = round($this->profiler[$this->tag][$method]['total'] / $this->profiler[$this->tag][$method]['count'], 3);
        if ($this->profiler[$this->tag][$method]['last'] > $this->profiler[$this->tag][$method]['max']) {
            $this->profiler[$this->tag][$method]['max'] = $this->profiler[$this->tag][$method]['last'];
        }
        if ($this->profiler[$this->tag][$method]['last'] < $this->profiler[$this->tag][$method]['min']) {
            $this->profiler[$this->tag][$method]['min'] = $this->profiler[$this->tag][$method]['last'];
        }
        file_put_contents($this->path, json_encode($this->profiler, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        unlink($this->lockFile);

        return $return;
    }
}
