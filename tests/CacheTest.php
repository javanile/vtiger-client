<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\OperationMapper;
use Javanile\VtigerClient\System\Cache;
use Javanile\VtigerClient\VtigerClient as Client;

final class CacheTest extends TestCase
{
    public function testCache()
    {
        putenv('VT_CLIENT_CACHE=a');

        $cacheDir = sys_get_temp_dir().'/vtiger-client-cache';
        $this->assertDirectoryNotExists($cacheDir);

        $args = [];
        $cache = new Cache($args);

        $itemKey = 'key';
        $itemValue = 'value';
        $ttl = 10;
        $cache->saveItem($itemKey, $itemValue, $ttl);

        $this->assertDirectoryExists($cacheDir);
    }
}
