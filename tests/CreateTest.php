<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

final class CreateTest extends TestCase
{
    public function testCreate()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/createFaq.json'), true);

        $actual = $client->create('Faq', [
            'faqstatus' => 'draft',
            'question' => 'this is a FAQ',
            'faq_answer' => 'this is a FAQ answer',
            'assigned_user_id' => 1,
        ]);

        //file_put_contents(__DIR__.'/fixtures/createFaq.json', json_encode($actual, JSON_PRETTY_PRINT));

        $expected['result']['createdtime'] = $actual['result']['createdtime'];
        $expected['result']['modifiedtime'] = $actual['result']['modifiedtime'];
        $expected['result']['faq_no'] = $actual['result']['faq_no'];
        $expected['result']['id'] = $actual['result']['id'];

        $this->assertEquals($expected, $actual);
    }
}
