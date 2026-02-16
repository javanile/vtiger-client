#!make

clean-logs:
	@docker-compose run --rm vtiger rm -f /var/lib/vtiger/logs/vtiger-client.log
	@docker-compose run --rm vtiger rm -f /var/lib/vtiger/logs/vtiger-client.json

update:
	@docker compose run --rm composer update

up:
	@docker compose up -d

start:
	@docker compose up -d

down:
	@docker compose down -v

debugger:
	@docker compose run --rm vtiger php tests/bin/debugger.php


## -----
## Build
## -----

clean:
	@rm -fr bin/vtc.phar

build: bin/vtc.phar

bin/vtc.phar:
	@[ -d vendor ] && mv vendor vendor.tmp || true
	@[ -f composer.lock ] && mv composer.lock composer.lock.tmp || true
	@docker compose run --rm -u $$(id -u) vtiger composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader
	@docker compose run --rm box compile
	@[ -f composer.lock.tmp ] && mv -f composer.lock.tmp composer.lock || true
	@[ -d vendor.tmp ] && mv vendor.tmp vendor || true

bind:
	@sudo rm -fr /usr/local/bin/vtc
	@sudo ln bin/vtc.phar /usr/local/bin/vtc
	@sudo chmod +x /usr/local/bin/vtc

install: clean build bind

## -------
## Quality
## -------

autofix:
	@docker compose run --rm --no-deps vtiger bash contrib/autofix.sh

## -------
## Develop
## -------

dev-clean:
	@docker compose run --rm vtiger rm -fr debug tmp vendor vendor.tmp

dev-install:
	@docker compose run --rm -u $$(id -u) vtiger composer install

dev-update:
	@docker compose run --rm -u $$(id -u) vtiger composer update

dev-debug:
	@docker compose exec vtiger debug --polling

## -------
## Testing
## -------

test: clean down up test-all

test-all: up
	@while [ -f .vtiger.lock ]; do sleep 2; done
	@docker compose run --rm phpunit tests --stop-on-failure

test-create-purchase-order:
	@docker compose run --rm phpunit tests --filter ::testCreatePurchaseOrder

test-create-and-update-line-item: clean
	@docker compose run --rm phpunit tests --filter ::testCreateAndUpdateLineItem

test-create-every-types: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testCreateEveryTypes

test-revise-every-types: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testReviseEveryTypes

test-update-every-types: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testUpdateEveryTypes

test-describe: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testDescribe

test-describe-cache: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testDescribeCache

test-describe-with-depth-1: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testDescribeWithDepth1

test-describe-with-all-depth: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testDescribeWithAllDepth

test-retrieve-with-depth-zero: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testRetrieveWithDepthZero

test-retrieve-with-depth: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testRetrieveWithDepth

test-retrieve-with-depth-one: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testRetrieveWithDepthOne

test-retrieve-not-found: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testRetrieveNotFound

test-sync-with-depth: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testSyncWithDepth

test-operation-mapper: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter OperationMapperTest

test-list-types: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testListTypes

test-query-with-join: clean
	@docker compose run --rm phpunit tests --stop-on-failure --filter ::testQueryWithJoin

test-cli:
	@docker compose run --rm phpunit tests --stop-on-failure --filter CliTest::

test-users: up
	@docker compose run --rm phpunit tests --stop-on-failure --filter UsersTest::

test-cache:
	@docker compose run --rm phpunit tests --stop-on-failure --filter CacheTest::

test-stop-on-failure:
	@docker compose run --rm phpunit tests --stop-on-failure

