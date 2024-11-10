#!/bin/bash

set -e
echo "stoped server if started"
symfony server:stop
echo "Installing dependencies..."
composer install
echo "Clearing cache..."
php bin/console cache:clear
symfony server:start -d
composer install -vvv
npm install
npm run dev
echo "Running database migrations..."
php bin/console cache:clear --verbose
php bin/console cache:clear
php bin/console make:migration
php bin/console doctrine:migrations:migrate
echo "Starting Symfony server..."