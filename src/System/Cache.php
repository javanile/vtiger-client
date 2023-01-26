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

class Cache extends SystemDriver
{
    /**
     *
     */
    protected $env = [
        'VT_CLIENT_CACHE' => 'path'
    ];

    /**
     *
     */
    protected $args = [
        'cache' => 'active',
        'cacheDir' => 'path',
    ];

    /**
     *
     */
    protected $cache = [];

    /**
     *
     */
    public function getItem($key)
    {
        if (empty($this->cache[$key]) && !$this->hasItem($key)) {
            return null;
        }

        if (empty($this->cache[$key]['ttl'])) {
            return $this->cache[$key]['value'];
        }

        if ($this->cache[$key]['ttl'] > time()) {
            return $this->cache[$key]['value'];
        }

        return null;
    }

    /**
     * @param $key
     * @return void
     */
    public function hasItem($key)
    {
        return false;

        $cacheFile = $this->getItemFile($key);
        if (!file_exists($cacheFile)) {
            return false;
        }

        $cacheEntry = json_decode(file_get_contents($cacheFile), true);
        if (empty($cacheEntry['key']) || $cacheEntry['key'] != $key) {
            return false;
        }

        $this->cache[$key] = $cacheEntry;

        if (empty($cacheEntry['ttl']) || $cacheEntry['ttl'] > time()) {
            return true;
        }

        return false;
    }

    /**
     *
     */
    public function saveItem($key, $value, $ttl)
    {
        $cacheFile = self::file($key);
        $cacheDir = dirname($cacheFile);

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
            file_put_contents($cacheDir.'/.htaccess', 'order deny,allow'."\n".'deny from all'."\n");
        }

        $cacheEntry = [
            'key' => $key,
            'value' => $value,
            'ttl' => time() + $ttl,
        ];

        $this->cache[$key] = $cacheEntry;

        $entrySize = file_put_contents($cacheFile, json_encode($cacheEntry));

        return $entrySize > 10;
    }

    /**
     *
     */
    protected function getItemFile($key)
    {
        $cacheDir = get_global_var('cache_dir');
        $cacheHash = md5($key);

        return rtrim($cacheDir, '/').'/'.$this->tag.'/'.$cacheHash[0].'/'.$cacheHash.'.json';
    }
}
