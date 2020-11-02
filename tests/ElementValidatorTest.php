<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\ElementValidator;
use Javanile\VtigerClient\VtigerClient as Client;

final class ElementValidatorTest extends TestCase
{
    public function testGetChallengeWithoutUsername()
    {
        $expected = [
            'success' => false,
            'error' => [
                'code' => 'EMPTY_ELEMENT',
                'message' => 'Empty element in create',
            ]
        ];

        $elementValidator = new ElementValidator([]);
        $actual = $elementValidator->create('Faq', []);

        $this->assertEquals($expected, $actual);
    }
}
