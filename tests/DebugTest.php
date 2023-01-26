<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\ElementValidator;
use Javanile\VtigerClient\System\Logger;
use Javanile\VtigerClient\VtigerClient as Client;

final class DebugTest extends TestCase
{
    public function testDebugTrueNoEnv()
    {
        putenv('VT_CLIENT_LOG=');
        $client = new Client(self::defaultArgs(['debug' => true]));
        $client->describe('Contacts');
        $client->describe('NotExistingModule');
    }

    public function testDebugTrueWithEnv()
    {
        return;
        $client = new Client([
            'debug' => true,
            'endpoint' => 'broken_uri'
        ]);

        putenv('VT_CLIENT_DEBUG=');

        $this->assertEquals($expected, $client->getChallenge());
    }

    public function testDebugFalseNoEnv()
    {
        return;
        $client = new Client([
            'debug' => true,
            'endpoint' => 'broken_uri'
        ]);

        putenv('VT_CLIENT_DEBUG=');

        $this->assertEquals($expected, $client->getChallenge());
    }

    public function testDebugFalseWithEnv()
    {
        return;
        $client = new Client([
            'debug' => true,
            'endpoint' => 'broken_uri'
        ]);

        putenv('VT_CLIENT_DEBUG=');

        $this->assertEquals($expected, $client->getChallenge());
    }
}
