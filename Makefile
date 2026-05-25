install:
	cd app && composer install && npm install

up:
	docker compose up -d --build --remove-orphans

down:
	docker compose down

test:
	docker exec trabalho_fsa_php_eng-app-1 sh -c "cd /var/www/html && php artisan test"

test-unit:
	docker exec trabalho_fsa_php_eng-app-1 sh -c "cd /var/www/html && php artisan test --testsuite=Unit"

test-integration:
	docker exec trabalho_fsa_php_eng-app-1 sh -c "cd /var/www/html && php artisan test --testsuite=Integration"
