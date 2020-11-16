#!make

clean:
	@docker-compose run --rm vtiger rm -f /var/lib/vtiger/logs/vtiger-client.log

install:
	@docker-compose run --rm composer install

update:
	@docker-compose run --rm composer update

## -------
## Testing
## -------
test:
	@docker-compose run --rm phpunit tests

test-create-purchase-order:
	@docker-compose run --rm phpunit tests --filter ::testCreatePurchaseOrder

test-create-and-update-line-item:
	@docker-compose run --rm phpunit tests --filter ::testCreateAndUpdateLineItem

test-create-every-types: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testUpdateEveryTypes

test-update-every-types: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testUpdateEveryTypes

test-describe-with-depth: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testDescribeWithDepth

test-stop-on-failure:
	@docker-compose run --rm phpunit tests --stop-on-failure
