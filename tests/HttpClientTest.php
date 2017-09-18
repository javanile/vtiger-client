<?php

namespace Javanile\HttpClient\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;
use Javanile\HttpClient\HttpClient;

Producer::addPsr4([
    'Javanile\HttpClient\\' => __DIR__.'/../src',
    'Javanile\HttpClient\\Tests\\' => __DIR__,
]);

final class HttpClientTest extends TestCase
{
    public function testCreateAnInstance()
    {
        $object = new HttpClient();
        $this->assertInstanceOf('Javanile\HttpClient\HttpClient', $object);

        $output = "Hello World!";
        $this->assertRegexp('/World/i', $output);
    }
}
