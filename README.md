# vtiger-client

[![StyleCI](https://github.styleci.io/repos/103863537/shield?branch=master)](https://github.styleci.io/repos/103863537)
[![Build Status](https://travis-ci.com/javanile/vtiger-client.svg?branch=master)](https://travis-ci.com/javanile/vtiger-client)
[![codecov](https://codecov.io/gh/javanile/vtiger-client/branch/master/graph/badge.svg)](https://codecov.io/gh/javanile/vtiger-client)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ffb974752a804645978286bc99759a09)](https://www.codacy.com/app/francescobianco/vtiger-client?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=javanile/vtiger-client&amp;utm_campaign=Badge_Grade)
[![Latest Stable Version](https://poser.pugx.org/javanile/vtiger-client/v)](//packagist.org/packages/javanile/vtiger-client) [![Total Downloads](https://poser.pugx.org/javanile/vtiger-client/downloads)](//packagist.org/packages/javanile/vtiger-client) [![Latest Unstable Version](https://poser.pugx.org/javanile/vtiger-client/v/unstable)](//packagist.org/packages/javanile/vtiger-client) [![License](https://poser.pugx.org/javanile/vtiger-client/license)](//packagist.org/packages/javanile/vtiger-client)

> **LOOKING FOR FAST DEMO! Visit --> [https://github.com/javanile/vtiger-demo]() <--**

## Get started

```bash
composer require javanile/vtiger-client
```

```php
<?php
use Javanile\VtigerClient\VtigerClient;

$client = new VtigerClient('http://my-vtiger-host');

$client->login('<<username>>', '<<accessKey>>');

$cliet->create('Leads', [
   'email' => '<<lead@email>>' 
]);
```

## Test

Before test
```bash
docker-compose run --rm composer install
```

Test all
```bash
docker-compose run --rm phpunit tests
```

Test driven development
```bash
docker-compose run --rm phpunit tests --stop-on-failure
```

Run single test method
```bash
docker-compose run --rm phpunit tests --filter '/::testMethod/'
```
