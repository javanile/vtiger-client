<?php

namespace Javanile\HttpClient\Tests;

use Javanile\HttpClient\HttpClient;
use Javanile\Producer;
use PHPUnit\Framework\TestCase;

Producer::addPsr4([
    'Javanile\HttpClient\\'        => __DIR__.'/../src',
    'Javanile\HttpClient\\Tests\\' => __DIR__,
]);

final class HttpClientTest extends TestCase
{
    public function testCreateAnInstance()
    {

        $client = new HttpClient('json.org', 80);
        $client->setDebug(true);

        $this->assertInstanceOf('Javanile\HttpClient\HttpClient', $client);

        $client->get('/example.html');

        $body = $client->getContent();

        Producer::log($client->getLogs());
        Producer::log($body);
    }
}
