<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

final class HttpClientTest extends TestCase
{
    public function testGuzzleError()
    {
        $expected = [
            'success' => false,
            'error' => [
                'code' => 'GUZZLE_ERROR',
                'message' => 'cURL error 6: Could not resolve host: broken_uri (see https://curl.haxx.se/libcurl/c/libcurl-errors.html)',
            ]
        ];

        $client = new Client([
            'endpoint' => 'broken_uri'
        ]);

        $this->assertEquals($expected, $client->getChallenge());
    }
}
