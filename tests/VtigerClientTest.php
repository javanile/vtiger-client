<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;
use PHPUnit\Framework\TestCase;

final class VtigerClientTest extends TestCase
{
    public function testGetChallengeWithoutUsername()
    {
        $expected = [
            'success' => false,
            'error'   => [
                'code'    => 'ACCESS_DENIED',
                'message' => 'No username given',
            ],
        ];

        $actual = (new Client([
            'endpoint' => getenv('VT_ENDPOINT'),
        ]))->getChallenge();

        $this->assertEquals($expected, $actual);
    }

    public function testGetChallengeSuccess()
    {
        $time = time();

        $expected = [
            'success' => true,
            'result'  => [
                'token'      => null,
                'serverTime' => $time,
                'expireTime' => $time + 300,
            ],
        ];

        $actual = (new Client([
            'endpoint'  => getenv('VT_ENDPOINT'),
            'username'  => getenv('VT_USERNAME'),
            'accessKey' => getenv('VT_ACCESS_KEY'),
        ]))->getChallenge();

        $expected['result']['token'] = $actual['result']['token'];

        $this->assertEquals($expected, $actual);
    }

    public function testLoginSuccess()
    {
        $expected = [
            'success' => true,
            'result'  => [
                'sessionName'   => null,
                'userId'        => '19x1',
                'version'       => '0.22',
                'vtigerVersion' => '7.1.0',
            ],
        ];

        $actual = (new Client([
            'endpoint'  => getenv('VT_ENDPOINT'),
            'username'  => getenv('VT_USERNAME'),
            'accessKey' => getenv('VT_ACCESS_KEY'),
        ]))->login();

        if (empty($actual['result']['sessionName'])) {
            var_dump($actual);
        }

        $expected['result']['sessionName'] = $actual['result']['sessionName'];

        $this->assertEquals($expected, $actual);
    }

    public function testListTypes()
    {
        $client = new Client(getenv('VT_ENDPOINT'));

        $client->login(getenv('VT_USERNAME'), getenv('VT_ACCESS_KEY'));

        $expected =  json_decode(file_get_contents(__DIR__.'/fixtures/listTypes.json'), true);

        $actual = $client->listTypes();
        #file_put_contents(__DIR__.'/fixtures/listTypes.json', json_encode($actual, JSON_PRETTY_PRINT));

        $this->assertEquals($expected, $actual);
    }

    public function testDescribe()
    {
        $client = new Client(getenv('VT_ENDPOINT'));

        $client->login(getenv('VT_USERNAME'), getenv('VT_ACCESS_KEY'));

        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/describeFaq.json'), true);

        $actual = $client->describe('Faq');
        #file_put_contents(__DIR__.'/fixtures/describeFaq.json', json_encode($actual, JSON_PRETTY_PRINT));

        $this->assertEquals($expected, $actual);
    }

    public function testListUsers()
    {
        $client = new Client(getenv('VT_ENDPOINT'));

        $client->login(getenv('VT_USERNAME'), getenv('VT_ACCESS_KEY'));

        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/listUsers.json'), true);

        $actual = $client->listUsers();
        #file_put_contents(__DIR__.'/fixtures/listUsers.json', json_encode($actual, JSON_PRETTY_PRINT));

        $this->assertEquals($expected, $actual);
    }

    /*
            public function testCreateAnInstance()
        {
            $client = new VtigerClient([
                'endpoint'  => 'http://vtigercrm.javanile.org',
                'username'  => 'admin',
                'accessKey' => getenv('VTIGER_ACCESS_KEY'),
            ]);
    
            $this->assertInstanceOf('Javanile\VtigerClient\VtigerClient', $client);
    
            $response = $client->doGetChallenge();
            $challengeToken = $client->getChallengeToken();
            $this->assertEquals(13, strlen($challengeToken));
    
            $response = $client->doLogin();
            $sessionName = $client->getSessionName();
            $this->assertEquals(21, strlen($sessionName));
    
            $response = $client->doListTypes();
            $types = $client->getTypes();
            $this->assertTrue(is_array($types));
    
            Producer::log($response);
        }
    */
}
