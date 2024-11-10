#!/bin/bash


set -e
echo "stoped server if started"
symfony server:stop
echo "Installing dependencies..."
composer install
echo "Clearing cache..."
php bin/console cache:clear
composer install -vvv
npm install
echo "Running database migrations..."
php bin/console cache:clear --verbose
symfony server:start -d
npm run build
php bin/console cache:clear
php bin/console make:migration
php bin/console doctrine:migrations:migrate
echo "Starting Symfony server..."