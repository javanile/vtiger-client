<?php

require_once __DIR__.'/../../vendor/autoload.php';

$env = parse_ini_file('.env');

$client = new \Javanile\VtigerClient\VtigerClient($env['vt_url']);

$client->login($env['vt_username'], $env['vt_accesskey']);

$retrieve = $client->retrieve('11x38995');

var_dump($retrieve);



