<div align="center">


<a href="https://vtc.javanile.org">
<img src="https://raw.githubusercontent.com/javanile/vtiger-client/refs/heads/test/.github/assets/images/banner.png" />
</a>


</div>

---

# vtiger-client

[![StyleCI](https://github.styleci.io/repos/103863537/shield?branch=master)](https://github.styleci.io/repos/103863537)
[![codecov](https://codecov.io/gh/javanile/vtiger-client/branch/master/graph/badge.svg)](https://codecov.io/gh/javanile/vtiger-client)
[![Latest Stable Version](https://poser.pugx.org/javanile/vtiger-client/v)](//packagist.org/packages/javanile/vtiger-client) [![Total Downloads](https://poser.pugx.org/javanile/vtiger-client/downloads)](//packagist.org/packages/javanile/vtiger-client) [![Latest Unstable Version](https://poser.pugx.org/javanile/vtiger-client/v/unstable)](//packagist.org/packages/javanile/vtiger-client) [![License](https://poser.pugx.org/javanile/vtiger-client/license)](//packagist.org/packages/javanile/vtiger-client)

> **LOOKING FOR FAST DEMO! Visit --> [https://github.com/javanile/vtiger-demo]() <--**

## Get Started

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

### Command-line usage

#### Intall

```shell
curl -sLo vtc https://github.com/javanile/vtiger-client/releases/download/0.1.0/vtc.phar
chmod +x vtc
sudo mv vtc /usr/local/bin/ 
vtc
```

#### Usage

```shell
vtc query "SELECT * FROM Contacts"
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
