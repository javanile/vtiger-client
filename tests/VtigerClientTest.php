<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\Producer;
use PHPUnit\Framework\TestCase;
use Javanile\VtigerClient\VtigerClient;

Producer::addPsr4([
    'Javanile\VtigerClient\\' => __DIR__.'/../src',
    'Javanile\VtigerClient\\Tests\\' => __DIR__,
]);

final class VtigerClientTest extends TestCase
{
    public function testCreateAnInstance()
    {
        $client = new VtigerClient([
            'endpoint'  => 'http://vtigercrm.javanile.org',
            'username'  => 'admin',
            'accessKey' => 'asd',
        ]);

        $this->assertInstanceOf('Javanile\VtigerClient\VtigerClient', $client);

        $response = $client->doGetChallenge();

        $challengeToken = $client->getChallengeToken();

        $this->assertEquals(13, strlen($challengeToken));

        $response = $client->doLogin();

        Producer::log($response);

    }
}
