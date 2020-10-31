<?php

namespace Javanile\VtigerClient\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use PDO;

class TestCase extends PHPUnitTestCase
{
    protected static $username;

    protected static $accessKey;

    protected static $endpoint;

    public static function setUpBeforeClass()
    {
        self::$endpoint = getenv('VT_ENDPOINT');
        self::$username = $username = getenv('VT_USERNAME');

        $mysqlHost = getenv('MYSQL_HOST');
        $mysqlDatabase = getenv('MYSQL_DATABASE');
        $mysqlRootPassword = getenv('MYSQL_ROOT_PASSWORD');
        $db = new PDO("mysql:host={$mysqlHost};dbname={$mysqlDatabase}", 'root', $mysqlRootPassword);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT accesskey FROM vtiger_users WHERE user_name='{$username}'";
        self::$accessKey = $db->query($sql)->fetchObject()->accesskey;
    }
}
