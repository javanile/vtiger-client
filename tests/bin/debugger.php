<?php

require_once __DIR__.'/../../vendor/autoload.php';

$env = parse_ini_file('.env');

$client = new \Javanile\VtigerClient\VtigerClient($env['vt_url']);

$response = $client->login($env['vt_username'], $env['vt_accesskey']);
var_dump($response);

$describe = $client->describe('Accounts', 1);
#file_put_contents(__DIR__.'/describe.log', json_encode($describe, JSON_PRETTY_PRINT));
var_dump($describe);

#$retrieve = $client->retrieve('11x38995', 1);
#file_put_contents(__DIR__.'/retrieve.log', json_encode($retrieve, JSON_PRETTY_PRINT));

#$listType = $client->listTypes();
#file_put_contents(__DIR__.'/listTypes.json', json_encode($listType, JSON_PRETTY_PRINT));


