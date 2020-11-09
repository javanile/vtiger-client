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
test-create:
	@docker-compose run --rm phpunit tests --filter CreateTest::testCreate

test-create-purchase-order:
	@docker-compose run --rm phpunit tests --filter ::testCreatePurchaseOrder

test-update-every-types: clean
	@docker-compose run --rm phpunit tests --stop-on-failure --filter ::testUpdateEveryTypes

test-stop-on-failure:
	@docker-compose run --rm phpunit tests --stop-on-failure
