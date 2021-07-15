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

class Profiler extends Debugger
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

        $this->profiler[$method]['begin'] = microtime(true);
    }

    /**
     *
     */
    public function end($method, $return)
    {
        if (empty($this->active)) {
            return $return;
        }

        $time = time();
        while (file_exists($this->lockFile)) {
            if ((time() - $time) > 300) {
                throw new \Exception("Problem with lock file '{$this->lockFile}', seems it exists for a long time.");
            }
            usleep(100000);
        }

        touch($this->lockFile);
        $past = (array) json_decode(file_get_contents($this->file), true);
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
        $this->profiler[$this->tag][$method]['end'] = microtime(true);
        $this->profiler[$this->tag][$method]['count']++;
        $this->profiler[$this->tag][$method]['last'] = $this->profiler[$this->tag][$method]['end'] - $this->profiler[$this->tag][$method]['begin'];
        $this->profiler[$this->tag][$method]['total'] += $this->profiler[$this->tag][$method]['last'];
        $this->profiler[$this->tag][$method]['average'] = $this->profiler[$this->tag][$method]['total'] / $this->profiler[$this->tag][$method]['count'];
        if ($this->profiler[$this->tag][$method]['last'] > $this->profiler[$this->tag][$method]['max']) {
            $this->profiler[$this->tag][$method]['max'] = $this->profiler[$this->tag][$method]['last'];
        }
        if ($this->profiler[$this->tag][$method]['last'] < $this->profiler[$this->tag][$method]['min']) {
            $this->profiler[$this->tag][$method]['min'] = $this->profiler[$this->tag][$method]['last'];
        }
        file_put_contents($this->file, json_encode($this->profiler, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        unlink($this->lockFile);

        return $return;
    }
}
