<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

final class ElementSanitizerTest extends TestCase
{
    public function testGetChallengeSuccess()
    {
        $client = new Client([
            'username'  => self::$username,
            'accessKey' => self::$accessKey,
            'endpoint'  => self::$endpoint,
        ]);

        $expected = [
            'success' => true,
            'result'  => [
                'token'      => null,
                'serverTime' => null,
                'expireTime' => null,
            ],
        ];

        $actual = $client->getChallenge();

        $expected['result']['token'] = $actual['result']['token'];
        $expected['result']['serverTime'] = $actual['result']['serverTime'];
        $expected['result']['expireTime'] = $actual['result']['expireTime'];

        $this->assertEquals($expected['result']['token'], $client->getToken());
        $this->assertEquals($expected, $actual);
    }
}
