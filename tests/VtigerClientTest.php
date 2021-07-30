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
        //file_put_contents(__DIR__.'/fixtures/listTypes.json', json_encode($actual, JSON_PRETTY_PRINT));

        $this->assertEquals($expected, $actual);
    }

    public function testDescribe()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        foreach (['Accounts', 'Faq'] as $type) {
            $stubFile = __DIR__.'/fixtures/describe'.$type.'.json';
            $expected = json_decode(file_get_contents($stubFile), true);
            $actual = $client->describe($type);
            //file_put_contents($stubFile, json_encode($actual, JSON_PRETTY_PRINT));
            $this->assertEquals($expected, $actual);
        }
    }

    public function testDescribeWithDepth1()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $depth = 1;
        $stubFile = __DIR__.'/fixtures/describeFieldsContactsWithDepth'.$depth.'.json';
        $describe = $client->describe('Contacts', $depth);
        $expected = json_decode(file_get_contents($stubFile), true);
        $actual = [];
        foreach ($describe['result']['fields'] as $field) {
            //echo "$field[name]\n";
            $actual[] = $field['name'];
        }
        sort($actual);
        //file_put_contents($stubFile, json_encode($actual, JSON_PRETTY_PRINT));
        $this->assertEquals($expected, $actual);
    }

    public function testDescribeWithAllDepth()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        foreach (['Contacts', 'Quotes'] as $module) {
            for ($depth = 1; $depth <= 1; $depth++) {
                $stubFile = __DIR__.'/fixtures/describe'.$module.'WithDepth'.$depth.'.json';
                $stubFieldsFile = __DIR__.'/fixtures/describeFields'.$module.'WithDepth'.$depth.'.json';
                $expected = json_decode(file_get_contents($stubFile), true);
                $expectedFields = json_decode(file_get_contents($stubFieldsFile), true);
                $actual = $client->describe($module, $depth);
                $actualFields = [];
                foreach ($actual['result']['fields'] as $field) {
                    $actualFields[] = $field['name'];
                }
                sort($actualFields);
                //file_put_contents($stubFile, json_encode($actual, JSON_PRETTY_PRINT));
                //file_put_contents($stubFieldsFile, json_encode($actualFields, JSON_PRETTY_PRINT));
                $this->assertEquals($expected, $actual);
                $this->assertEquals($expectedFields, $actualFields);
            }
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

    public function testReviseEveryTypes()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $createdElements = [];

        $types = $client->getTypes();
        $ignoredTypes = [
            'PurchaseOrder',
            'Invoice',
            'SalesOrder',
            'Quotes',
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
                $reviseElement = ['id' => $resultCreate['result']['id']];
                foreach ($resultCreate['result'] as $field => $value) {
                    if (in_array($field, ['id']) || preg_match('/(_no|id|_start)$/', $field)) {
                        continue;
                    }
                    $reviseElement[$field] = $value.' (UPDATED)';
                    break;
                }
                $resultRevise = $client->revise($type, $reviseElement);
                if (empty($resultRevise['success'])) {
                    var_dump($type);
                    var_dump($reviseElement);
                    var_dump($resultRevise);
                    //file_put_contents(__DIR__.'/log.log', $resultRevise['error']['message']);
                }
                $this->assertTrue($resultRevise['success']);

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
                    //file_put_contents(__DIR__.'/log.log', $resultUpdate['error']['message']);
                }
                $this->assertTrue($resultUpdate['success']);
            }
        }
    }

    /*
    public function testRetrieveNotFound()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        //$describe = $client->describe('Contacts');
        //var_dump($describe);

        $actual = $client->retrieve('12x30000');

        var_dump($actual);
    }
    */

    /*
    public function testRetrieveWithDepthZero()
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

        $actual = $client->retrieve($faq['id'], 0);
    }
    */

    public function testRetrieveWithDepthOne()
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

        $actual = $client->retrieve($faq['id'], 1);

        $this->assertEquals($product['id'], $actual['result']['product_id__id']);
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

        for ($depth = 1; $depth <= 2; $depth++) {
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
            $expected['result']['modifiedby__accesskey'] = $actual['result']['modifiedby__accesskey'];
            $expected['result']['createdtime'] = $actual['result']['createdtime'];
            $expected['result']['modifiedtime'] = $actual['result']['modifiedtime'];
            if (isset($expected['result']['product_id__modifiedby__accesskey'])) {
                $expected['result']['product_id__modifiedby__accesskey'] = $actual['result']['product_id__modifiedby__accesskey'];
            }
            if (isset($expected['result']['product_id__assigned_user_id__accesskey'])) {
                $expected['result']['product_id__assigned_user_id__accesskey'] = $actual['result']['product_id__assigned_user_id__accesskey'];
            }
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

    public function testSyncWithDepth()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $product = $client->create('Products', [
            'productname' => 'Test Product',
            'discontinued' => 1,
        ])['result'];

        $faq1 = $client->create('Faq', [
            'faqstatus' => 'draft',
            'question' => 'this is a FAQ 1',
            'faq_answer' => 'this is a FAQ answer',
            'assigned_user_id' => 1,
        ])['result'];

        $faq2 = $client->create('Faq', [
            'faqstatus' => 'draft',
            'question' => 'this is a FAQ 1',
            'faq_answer' => 'this is a FAQ answer',
            'assigned_user_id' => 1,
            'product_id' => $product['id'],
        ])['result'];

        for ($depth = 1; $depth <= 2; $depth++) {
            $stubFile = __DIR__ . '/fixtures/syncFaqWithDepth'.$depth.'.json';
            $expected = json_decode(file_get_contents($stubFile), true);
            $actual = $client->sync("Faq", time() - 60, 'application', $depth);
            //file_put_contents($stubFile, json_encode($actual, JSON_PRETTY_PRINT));
            //$actual = $client->sync("Faq", new \DateTime('@'.(time() - 60)));
            $expected['result']['updated'] = $actual['result']['updated'];
            $expected['result']['deleted'] = $actual['result']['deleted'];
            $expected['result']['lastModifiedTime'] = $actual['result']['lastModifiedTime'];
            $expected['result']['more'] = $actual['result']['more'];
            $this->assertEquals($expected, $actual);
        }
    }

    /*
    public function testQueryWithJoin()
    {
        $client = new Client(self::$endpoint);
        $client->login(self::$username, self::$accessKey);

        $product = $client->create('Products', [
            'productname' => 'Test Product',
            'discontinued' => 1,
        ])['result'];

        $faq = $client->create('Faq', [
            'faqstatus' => 'draft',
            'question' => 'this is a FAQ 1',
            'faq_answer' => 'this is a FAQ answer',
            'assigned_user_id' => 1,
            'product_id' => $product['id'],
        ])['result'];

        $actual = $client->query('SELECT id FROM Faq LIMIT 1');
    }
    */
}
