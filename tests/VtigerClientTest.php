<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

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

        $client = new Client(['endpoint' => self::$endpoint]);
        $actual = $client->getChallenge();

        $expected['error']['xdebug_message'] = $actual['error']['xdebug_message'];

        $this->assertEquals($expected, $actual);
    }

    public function testGetChallengeSuccess()
    {
        $client = new Client([
            'username'  => self::$username,
            'accessKey' => self::$accessKey,
            'endpoint'  => self::$endpoint,
        ]);

        $expected = [
            'success' => true,
            'result'  => [
                'token'      => null,
                'serverTime' => null,
                'expireTime' => null,
            ],
        ];

        $actual = $client->getChallenge();

        $expected['result']['token'] = $actual['result']['token'];
        $expected['result']['serverTime'] = $actual['result']['serverTime'];
        $expected['result']['expireTime'] = $actual['result']['expireTime'];

        $this->assertEquals($expected['result']['token'], $client->getToken());
        $this->assertEquals($expected, $actual);
    }

    public function testLoginSuccess()
    {
        $client = new Client([
            'username'  => self::$username,
            'accessKey' => self::$accessKey,
            'endpoint'  => self::$endpoint,
        ]);

        $expected = [
            'success' => true,
            'result'  => [
                'sessionName'   => null,
                'userId'        => '19x1',
                'version'       => '0.22',
                'vtigerVersion' => null,
            ],
        ];

        $actual = $client->login();

        //if (empty($actual['result']['sessionName'])) {
        //    var_dump($actual);
        //}

        $expected['result']['sessionName'] = $actual['result']['sessionName'];
        $expected['result']['vtigerVersion'] = $actual['result']['vtigerVersion'];

        $this->assertEquals($expected['result']['sessionName'], $client->getSessionName());
        $this->assertEquals($expected, $actual);
    }

    public function testListTypes()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $expected =  json_decode(file_get_contents(__DIR__.'/fixtures/listTypes.json'), true);

        $actual = $client->listTypes();
        #file_put_contents(__DIR__.'/fixtures/listTypes.json', json_encode($actual, JSON_PRETTY_PRINT));

        $this->assertEquals($expected, $actual);
    }

    public function testDescribe()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/describeFaq.json'), true);

        $actual = $client->describe('Faq');
        #file_put_contents(__DIR__.'/fixtures/describeFaq.json', json_encode($actual, JSON_PRETTY_PRINT));

        $this->assertEquals($expected, $actual);
    }

    public function testDescribeWithDepth()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        for ($depth = 1; $depth <= 3; $depth++) {
            $stubFile = __DIR__.'/fixtures/describeFaqWithDepth'.$depth.'.json';
            $expected = json_decode(file_get_contents($stubFile), true);
            $actual = $client->describe('Faq', $depth);
            //file_put_contents($stubFile, json_encode($actual, JSON_PRETTY_PRINT));
            $this->assertEquals($expected, $actual);
        }
    }

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

    public function testCreatePurchaseOrder()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);
        $product = $client->create('Products', ['productname' => 'Test Product', 'discontinued' => 1])['result'];
        $vendor = $client->create('Vendors', ['vendorname' => 'Test Vendor'])['result'];
        $element = json_decode('{
            "subject":"test",
            "assigned_user_id":"19x1",
            "bill_street":"test",
            "postatus":"New",
            "productid": "'.$product['id'].'",
            "ship_street":"test",
            "vendor_id":"'.$vendor['id'].'",
            "quantity":1,
            "LineItems":[{"productid":"'.$product['id'].'","quantity":1,"sequence_no":1}]
        }', true);

        #$element['LineItems'] = [[]];

        $purchaseOrderResponse = $client->create('PurchaseOrder', $element);
        $this->assertTrue($purchaseOrderResponse['success']);
    }

    public function testCreateAndUpdateLineItem()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);
        $account = $client->create('Accounts', ['accountname' => 'Test Account #'.time()])['result'];
        $product = $client->create('Products', ['productname' => 'Test Product', 'discontinued' => 1])['result'];
        $quotes = $client->create('Quotes', [
            'subject' => 'Test Quote',
            'quotestage' => 'New',
            'account_id' => $account['id'],
            'bill_street' => 'test',
            'ship_street' => 'test',
            'productid' => $product['id'],
        ])['result'];

        $createdResponse = $client->create('LineItem', [
            'parent_id' => $quotes['id'],
            'productid' => $product['id'],
            'quantity' => 1,
        ]);

        $this->assertTrue($createdResponse['success']);
        /*
        $updatedResponse = $client->update('LineItem', [
            'id' => $createdResponse['result']['id'],
            'parent_id' => $quotes['id'],
            'productid' => $product['id'],
            'quantity' => 2,
        ]);
        //var_dump($updatedResponse);
        //file_put_contents(__DIR__.'/a.log', $updatedResponse['error']['message']);
        $this->assertTrue($updatedResponse['success']);
        */
    }

    public function testCreateEveryTypes()
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
            if (isset($result['success'])) {
                $createdElements[$type] = $result['result'];
            }
        }
    }

    public function testUpdateEveryTypes()
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
            'ProductTaxes',
            'LineItem',
            'Calendar',
        ];

        foreach ($types as $type) {
            if (in_array($type, $ignoredTypes)) {
                continue;
            }
            $newElement = self::defaultValues($type, $createdElements);
            $resultCreate = $client->create($type, $newElement);
            $this->assertTrue($resultCreate['success']);
            if (isset($resultCreate['success'])) {
                $createdElements[$type] = $resultCreate['result'];
                $newElement['id'] = $resultCreate['result']['id'];
                $resultUpdate = $client->update($type, $newElement);
                if (empty($resultUpdate['success'])) {
                    var_dump($type);
                    var_dump($newElement);
                    var_dump($resultUpdate);
                    file_put_contents(__DIR__.'/log.log', $resultUpdate['error']['message']);
                }
                $this->assertTrue($resultUpdate['success']);
            }
        }
    }

    public function testRetrieveWithDepth()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $product = $client->create('Products', [
            'productname' => 'Test Product',
            'discontinued' => 1,
        ])['result'];

        $faq = $client->create('Faq', [
            'question' => 'Test',
            'faq_answer' => 'Test',
            'faqstatus' => 'New',
            'product_id' => $product['id'],
        ])['result'];

        for ($depth = 1; $depth <= 3; $depth++) {
            $stubFile = __DIR__.'/fixtures/retrieveFaqWithDepth'.$depth.'.json';
            $expected = json_decode(file_get_contents($stubFile), true);
            $actual = $client->retrieve($faq['id'], $depth);
            $expected['result']['id'] = $actual['result']['id'];
            $expected['result']['faq_no'] = $actual['result']['faq_no'];
            $expected['result']['product_id'] = $actual['result']['product_id'];
            $expected['result']['product_id__id'] = $actual['result']['product_id__id'];
            $expected['result']['product_id__createdtime'] = $actual['result']['product_id__createdtime'];
            $expected['result']['product_id__modifiedtime'] = $actual['result']['product_id__modifiedtime'];
            $expected['result']['product_id__product_no'] = $actual['result']['product_id__product_no'];
            $expected['result']['createdtime'] = $actual['result']['createdtime'];
            $expected['result']['modifiedtime'] = $actual['result']['modifiedtime'];
            //file_put_contents($stubFile, json_encode($actual, JSON_PRETTY_PRINT));
            $this->assertEquals($expected, $actual);
        }
    }

    public function testListUsers()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);
        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/listUsers.json'), true);
        $actual = $client->listUsers();
        #file_put_contents(__DIR__.'/fixtures/listUsers.json', json_encode($actual, JSON_PRETTY_PRINT));
        $expected['result'][0]['accesskey'] = $actual['result'][0]['accesskey'];
        $this->assertEquals($expected, $actual);
    }

    public function testUpload()
    {
        $client = new Client(self::$endpoint);

        $client->login(self::$username, self::$accessKey);

        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/upload.json'), true);

        $actual = $client->upload([
            'notes_title' => 'Sample Document',
            'assigned_user_id' => 1,
            'filelocationtype' => 'I',
            'filestatus' => 1,
            'filename' => __DIR__.'/fixtures/sampleDocument.pdf',
        ]);

        //file_put_contents(__DIR__.'/fixtures/upload.json', json_encode($actual, JSON_PRETTY_PRINT));

        $expected['result']['createdtime'] = $actual['result']['createdtime'];
        $expected['result']['modifiedtime'] = $actual['result']['modifiedtime'];
        $expected['result']['note_no'] = $actual['result']['note_no'];
        $expected['result']['filesize'] = $actual['result']['filesize'];
        $expected['result']['id'] = $actual['result']['id'];

        $this->assertEquals($expected, $actual);
    }

    public function testSync()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);
        $expected = json_decode(file_get_contents(__DIR__.'/fixtures/sync.json'), true);
        $actual = $client->sync("Faq", time() - 60);
        //$actual = $client->sync("Faq", new \DateTime('@'.(time() - 60)));
        $expected['result']['updated'] = $actual['result']['updated'];
        $expected['result']['deleted'] = $actual['result']['deleted'];
        $expected['result']['lastModifiedTime'] = $actual['result']['lastModifiedTime'];
        $expected['result']['more'] = $actual['result']['more'];
        $this->assertEquals($expected, $actual);
    }
}
