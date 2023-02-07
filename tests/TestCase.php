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

    public static function defaultArgs($override = [])
    {
        return array_merge([
            'endpoint' => self::$endpoint,
            'username' => self::$username,
            'accessKey' => self::$accessKey,
        ], $override);
    }

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
                    'parent_id' => $createdElements['Quotes']['id'],
                    'productid' => $createdElements['Products']['id'],
                    'quantity' => 1
                ];
            case 'PBXManager':
                return [
                    'customernumber' => 'Test PBX',
                ];
            case 'Events':
                return [
                    'subject' => 'Test Event',
                    'date_start' => date('Y-m-d'),
                    'time_start' => '00:00',
                    'due_date' => date('Y-m-d'),
                    'time_end' => '01:00',
                    'duration_hours' => 0,
                    'eventstatus' => 'New',
                    'activitytype' => 'Call',
                ];
            case 'Users':
                return [
                    'user_name' => 'testuser',
                    'user_password' => 'testpass',
                    'confirm_password' => 'testpass',
                    'last_name' => 'Test User',
                    'roleid' => 'H1'
                ];
            case 'HelpDesk':
                return [
                    'ticket_title' => 'Test Ticket',
                    'ticketpriorities' => 1,
                    'ticketstatus' => 'New',
                ];
            case 'Emails':
                return [
                    'subject' => 'Test Email',
                    'from_email' => 'test@email.com',
                    'date_start' => date('Y-m-d'),
                    'saved_toid' => 1,
                ];
            case 'Documents':
                return [
                    'notes_title' => 'Test Document',
                ];
            case 'Potentials':
                return [
                    'potentialname' => 'Test Potential',
                    'sales_stage' => 'New',
                    'closingdate' => date('Y-m-d'),
                ];
            case 'Contacts':
                return [
                    'lastname' => 'Test Contact',
                ];
            case 'Leads':
                return [
                    'lastname' => 'Test Lead',
                ];
            case 'Calendar':
                return [
                    'subject' => 'Test Calendar',
                    'date_start' => date('Y-m-d'),
                    'time_start' => '00:00',
                    'due_date' => date('Y-m-d'),
                    'taskstatus' => 'New',
                ];
            case 'PriceBooks':
                return [
                    'bookname' => 'Test Price Book',
                    'currency_id' => $createdElements['Currency']['id'],
                ];
            case 'Invoice':
                return [
                    'subject' => 'Test Invoice',
                    'account_id' => $createdElements['Accounts']['id'],
                    'bill_street' => 'test',
                    'ship_street' => 'test',
                    'productid' => $createdElements['Products']['id'],
                ];
            case 'SalesOrder':
                return [
                    'subject' => 'Test Sales Order',
                    'sostatus' => 'New',
                    'account_id' => $createdElements['Accounts']['id'],
                    'bill_street' => 'test',
                    'ship_street' => 'test',
                    'invoicestatus' => 'New',
                    'productid' => $createdElements['Products']['id'],
                ];
            case 'PurchaseOrder':
                return [
                    'subject' => 'Test Purchase Order',
                    'vendor_id' => $createdElements['Vendors']['id'],
                    'postatus' => 'New',
                    'bill_street' => 'test',
                    'ship_street' => 'test',
                    'productid' => $createdElements['Products']['id'],
                ];
            case 'ProductTaxes':
                return [
                    'productid' => $createdElements['Products']['id'],
                    'taxid' => 1,
                ];
            default:
                return [];
        }
    }

    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
