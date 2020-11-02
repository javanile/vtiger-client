#!make

install:
	@docker-compose run --rm composer install

update:
	@docker-compose run --rm composer update

## -------
## Testing
## -------
test-create:
	@docker-compose run --rm phpunit tests --filter CreateTest::testCreate

test-stop-on-failure:
	@docker-compose run --rm phpunit tests --stop-on-failure
