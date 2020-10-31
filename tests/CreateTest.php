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
                    'account_id' => $createdElements['Accounts']['id'],
                    'bill_street' => 'test',
                    'ship_street' => 'test',
                    'productid' => $createdElements['Products']['id'],

                    'LineItems' => [
                        []
                    ],
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
                    'discontinued' => 1,
                ];
            case 'ServiceContracts':
                return [
                    'subject' => 'Test Service Contract',
                ];
            case 'Assets':
                return [
                    'assetname' => 'Test Asset',
                    'account' => $createdElements['Accounts']['id'],
                    'product' => $createdElements['Products']['id'],
                    'serialnumber' => '123456',
                    'datesold' => date('Y-m-d'),
                    'dateinservice' => date('Y-m-d'),
                    'assetstatus' => 'New',
                ];
            case 'Currency':
                return [
                    'defaultid' => '1',
                    'deleted' => '1',
                ];
            case 'DocumentFolders':
                return [
                    'foldername' => 'Test Folder',
                    'createdby' => '18x1'
                ];
            case 'LineItem':
                return [
                    'productid' => $createdElements['Products']['id'],
                    'quantity' => 1
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

        $count = 0;

        foreach ($types as $type) {
            if (in_array($type, ['ServiceContracts', 'Groups', 'DocumentFolders', 'CompanyDetails'])) {
                continue;
            }
            $stamp = time();
            $result = $client->create($type, self::defaultValues($type, $createdElements));
            echo $count++." ".$type.' '.(time()-$stamp)."\n";
            if (empty($result['success'])) {
                var_dump($type, $result);
                file_put_contents(__DIR__.'/result.log', $result['error']['message'], FILE_APPEND);
                die();
            }
            if ($count > 9) {
                //die();
            }
            var_dump($result);

            $createdElements[$type] = $result['result'];
            //$this->assertEquals($expected, $actual);
        }
    }
}
