<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\OperationMapper;
use Javanile\VtigerClient\VtigerClient as Client;

final class CliTest extends TestCase
{
    public function testNoArgs()
    {
        $output = `php ./bin/vtc`;
        $this->assertEquals(10, $output);
    }

    public function testInit()
    {
        $output = `php ./bin/vtc init`;
        $this->assertEquals(10, $output);
    }

    public function testPing()
    {
        $output = `php ./bin/vtc ping`;
        $this->assertEquals(10, $output);
    }
}
