<?php

namespace Javanile\VtigerClient\Tests;

use Javanile\VtigerClient\VtigerClient as Client;

final class CreateTest extends TestCase
{
    public static function defaultValues($type, $createdElements)
    {
        switch ($type) {
            case 'Accounts':
                return [
                    'accountname' => 'Test Account #'.time(),
                ];
            case 'Campaigns':
                return [
                    'campaignname' => 'Test Campaign',
                    'closingdate' => date('Y-m-d'),
                ];
            case 'Vendors':
                return [
                    'vendorname' => 'Test Vendor',
                ];
            case 'Faq':
                return [
                    'question' => 'Test Faq',
                    'faq_answer' => 'Test Faq',
                    'faqstatus' => 'New',
                ];
            case 'Quotes':
                return [
                    'subject' => 'Test Quote',
                    'quotestage' => 'New',
                    'account_id' => 1
                ];
            case 'SMSNotifier':
                return [
                    'message' => 'Test SMSNotifier',
                ];
            case 'Services':
                return [
                    'servicename' => 'Test Service',
                ];
            case 'ModComments':
                return [
                    'commentcontent' => 'Test Comment',
                    'related_to' => $createdElements['Accounts']['id'],
                ];
            case 'Project':
                return [
                    'projectname' => 'Test Project',
                ];
            case 'ProjectMilestone':
                return [
                    'projectmilestonename' => 'Test Milestone',
                    'projectid' => $createdElements['Project']['id'],
                ];
            case 'ProjectTask':
                return [
                    'projecttaskname' => 'Test Task',
                    'projectid' => $createdElements['Project']['id'],
                ];
            case 'Products':
                return [
                    'productname' => 'Test Product',
                ];
            case 'ServiceContracts':
                return [
                    'subject' => 'Test Service Contract',
                ];
            case 'Assets':
                return [
                    'product' => $createdElements['Products']['id'],
                    'serialnumber' => '123456',
                ];
            default:
                return [];
        }
    }

    public function testCreate()
    {
        $client = new Client(self::$endpoint);
        $result = $client->login(self::$username, self::$accessKey);

        $types = $client->getTypes();

        $createdElements = [];

        foreach ($types as $type) {
            $result = $client->create($type, self::defaultValues($type, $createdElements));
            if (empty($result['success'])) {
                var_dump($type, $result);
                die();
            }

            $createdElements[$type] = $result['result'];
            //$this->assertEquals($expected, $actual);
        }
    }
}
