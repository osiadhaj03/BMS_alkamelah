#!/bin/bash

# Fix composer.lock conflict on server
echo "Resetting composer.lock to repository version..."
git checkout composer.lock

echo "Pulling latest changes..."
git pull origin main

echo "Installing dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "Running migrations..."
php artisan migrate --force

echo "Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Done! Server is ready."
