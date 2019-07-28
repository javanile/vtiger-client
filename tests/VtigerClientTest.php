<?php

namespace Javanile\VtigerClient\Tests;

use PHPUnit\Framework\TestCase;
use Javanile\VtigerClient\VtigerClient as Client;

final class VtigerClientTest extends TestCase
{
    public function testGetChallengeWithoutUsername()
    {
        $expected = [
            'success' => false,
            'error' => [
                'code' => 'ACCESS_DENIED',
                'message' => 'No username given',
            ]
        ];

        $actual = (new Client([
            'endpoint' => getenv('VT_ENDPOINT'),
        ]))->getChallenge();

        $this->assertEquals($expected, $actual);
    }

    public function testGetChallengeSuccess()
    {
        $time = time();

        $expected = [
            'success' => true,
            'result' => [
                'token' => null,
                'serverTime' => $time,
                'expireTime' => $time + 300,
            ]
        ];

        $actual = (new Client([
            'endpoint' => getenv('VT_ENDPOINT'),
            'username' => getenv('VT_USERNAME'),
            'accessKey' => getenv('VT_accessKey'),
        ]))->getChallenge();

        $expected['result']['token'] = $actual['result']['token'];

        $this->assertEquals($expected, $actual);
    }

    public function testLoginSuccess()
    {
        $expected = [
            'success' => true,
            'result' => [
                'sessionName' => null,
                'userId' => '19x1',
                'version' => '0.22',
                'vtigerVersion' => '7.1.0',
            ]
        ];

        $actual = (new Client([
            'endpoint' => getenv('VT_ENDPOINT'),
            'username' => getenv('VT_USERNAME'),
            'accessKey' => getenv('VT_ACCESS_KEY'),
        ]))->login();

        $expected['result']['sessionName'] = $actual['result']['sessionName'];

        $this->assertEquals($expected, $actual);
    }

    public function testListTypes()
    {
        $client = new Client(getenv('VT_ENDPOINT'));

        $client->login(getenv('VT_USERNAME'), getenv('VT_ACCESS_KEY'));

        $expected = [
            'success' => true,
            'result' => [
                'types' => [
                    'Campaigns', 'Vendors', 'Faq', 'Quotes', 'PurchaseOrder',
                    'SalesOrder', 'Invoice', 'PriceBooks', 'Calendar', 'Leads',
                    'Accounts', 'Contacts', 'Potentials', 'Products', 'Documents',
                    'Emails', 'HelpDesk', 'Events', 'Users', 'PBXManager',
                    'ServiceContracts', 'Services', 'ModComments', 'SMSNotifier',
                    'Assets', 'ProjectMilestone', 'ProjectTask', 'Project', 'Groups',
                    'Currency', 'DocumentFolders', 'CompanyDetails', 'LineItem',
                    'Tax', 'ProductTaxes',
                ],
                'information' => Array (),
            ]
        ];

        $actual = $client->listTypes();

        $this->assertEquals($expected, $actual);
    }

/*
        public function testCreateAnInstance()
    {
        $client = new VtigerClient([
            'endpoint'  => 'http://vtigercrm.javanile.org',
            'username'  => 'admin',
            'accessKey' => getenv('VTIGER_ACCESS_KEY'),
        ]);

        $this->assertInstanceOf('Javanile\VtigerClient\VtigerClient', $client);

        $response = $client->doGetChallenge();
        $challengeToken = $client->getChallengeToken();
        $this->assertEquals(13, strlen($challengeToken));

        $response = $client->doLogin();
        $sessionName = $client->getSessionName();
        $this->assertEquals(21, strlen($sessionName));

        $response = $client->doListTypes();
        $types = $client->getTypes();
        $this->assertTrue(is_array($types));

        Producer::log($response);
    }
*/
}
