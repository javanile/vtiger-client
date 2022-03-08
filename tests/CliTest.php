<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\OperationMapper;
use Javanile\VtigerClient\VtigerClient as Client;

final class CliTest extends TestCase
{
    protected $cwd;

    public function setUp()
    {
        $this->cwd = getcwd();
        if (!is_dir($this->cwd.'/tmp')) {
            mkdir($this->cwd.'/tmp', 0777, true);
        }
    }

    public function testNoArgs()
    {
        $output = trim(`cd tmp && php ../bin/vtc | head -1`);
        $this->assertEquals('Usage: vtc [COMMAND] [ARG]...', $output);
    }

    public function testInit()
    {
        $configFile = 'tmp/vtiger.config.json';
        if (file_exists($configFile)) {
            unlink($configFile);
        }
        $output = trim(`cd tmp && php ../bin/vtc init | head -1`);
        $this->assertEquals("File '{$this->cwd}/tmp/vtiger.config.json' was created.", $output);
        $this->assertFileExists($configFile);
    }

    public function testPing()
    {
        $output = json_decode(`cd tmp && php ../bin/vtc ping`, true);
        $this->assertTrue($output['success']);
    }

    public function testQuery()
    {
        $output = json_decode(`cd tmp && php ../bin/vtc query "SELECT * FROM Contacts LIMIT 1"`, true);
        $this->assertTrue($output['success']);
    }

    public function testSyntaxError()
    {
        $output = trim(`php ./bin/vtc unknown`);
        $this->assertEquals('Syntax error.', $output);
    }
}
