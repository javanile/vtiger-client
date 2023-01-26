<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

final class UsersTest extends TestCase
{
    public function testCreateUser()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/createUsers.json'), true);

        $seed = rand(1000,9999);
        $lastName = 'User'.$seed;
        $username = 'user-'.$seed;
        $password = 'Secret1234!';
        $actual = $client->create('Users', [
            'last_name' => $lastName,
            'user_name' => $username,
            'user_password' => $password,
            'confirm_password' => $password,
            'roleid' => 'H1',
            'email1' => $username.'@localhost.lan',
        ]);

        var_dump($actual);
        die();

        //file_put_contents(__DIR__.'/fixtures/createFaq.json', json_encode($actual, JSON_PRETTY_PRINT));

        /*
        $expected['result']['createdtime'] = $actual['result']['createdtime'];
        $expected['result']['modifiedtime'] = $actual['result']['modifiedtime'];
        $expected['result']['faq_no'] = $actual['result']['faq_no'];
        $expected['result']['id'] = $actual['result']['id'];
        */

        $this->assertEquals($expected, $actual);
    }
}
