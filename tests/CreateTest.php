<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

final class CreateTest extends TestCase
{

    public function testCreate()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $createdElements = [];

        $types = $client->getTypes();
        $ignoredTypes = [
            'ServiceContracts',
            'Groups',
            'DocumentFolders',
            'CompanyDetails',
            'PBXManager',
            'Users',
            'ProductTaxes'
        ];

        foreach ($types as $type) {
            if (in_array($type, $ignoredTypes)) {
                continue;
            }
            $result = $client->create($type, self::defaultValues($type, $createdElements));
            $this->assertTrue($result['success']);
            if (empty($result['success'])) {
                $createdElements[$type] = $result['result'];
            }
        }
    }
}
