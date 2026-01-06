#!/bin/bash

# Deployment script for Hostinger
echo "Starting deployment..."

# Install composer dependencies with platform requirements ignored
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Clear and cache config
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
php artisan migrate --force

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment completed successfully!"
