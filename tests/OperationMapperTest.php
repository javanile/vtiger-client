<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\OperationMapper;
use Javanile\VtigerClient\VtigerClient as Client;

final class OperationMapperTest extends TestCase
{
    public function testChangeOperationsMap()
    {
        $expected = [
            'getchallenge' => 'getchallenge',
            'login' => 'login',
            'listtypes' => 'listtypes',
            'describe' => 'describe',
            'create' => 'custom_create',
            'retrieve' => 'custom_retrieve',
            'revise' => 'revise',
            'update' => 'update',
            'delete' => 'delete',
            'query' => 'custom_query',
            'sync' => 'sync',
        ];

        $operationMapper = new OperationMapper([
            'endpoint' => self::$endpoint,
            'operationsMap' => [
                'create' => 'custom_create',
                'retrieve' => 'custom_retrieve',
                'query' => 'custom_query',
            ]
        ]);

        $actual = $operationMapper->getOperations();

        $this->assertEquals($expected, $actual);
    }
}
