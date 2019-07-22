#!/bin/bash
echo "waiting for create all services ..."
docker-compose up -d
echo "waiting for services to set up ..."
sleep 20

echo "migrating and seeding database ..."
docker-compose exec app php artisan migrate --seed
echo "database migrated."

echo "starting supervisor ..."
docker-compose exec app supervisord
echo "supervisor daemon started."

echo "running tests..."
docker-compose exec app php vendor/bin/phpunit