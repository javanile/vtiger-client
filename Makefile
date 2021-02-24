#!make

clean:
	@docker-compose run --rm vtiger rm -f /var/lib/vtiger/logs/vtiger-client.log

install:
	@docker-compose run --rm composer install

update:
	@docker-compose run --rm composer update

up:
	@docker-compose up -d

down:
	@docker-compose down -v

debugger:
	@docker-compose run --rm vtiger php tests/bin/debugger.php

## -------
## Testing
## -------
test: down up
	@while [ -f .vtiger.lock ]; do sleep 2; done
	@docker-compose run --rm phpunit tests

test-create-purchase-order:
	@docker-compose run --rm phpunit tests --filter ::testCreatePurchaseOrder

test-create-and-update-line-item:
	@docker-compose run --rm phpunit tests --filter ::testCreateAndUpdateLineItem

test-create-every-types: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testCreateEveryTypes

test-revise-every-types: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testReviseEveryTypes

test-update-every-types: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testUpdateEveryTypes

test-describe: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testDescribe

test-describe-with-depth-1: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testDescribeWithDepth1

test-describe-with-all-depth: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testDescribeWithAllDepth

test-retrieve-with-depth: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testRetrieveWithDepth

test-sync-with-depth: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testSyncWithDepth

test-operation-mapper: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter OperationMapperTest

test-stop-on-failure:
	@docker-compose run --rm phpunit tests --stop-on-failure
