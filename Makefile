#!make

install:
	@docker-compose run --rm composer install

## -------
## Testing
## -------
test-create:
	@docker-compose run --rm phpunit tests --filter CreateTest::testCreate
