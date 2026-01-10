#!/bin/bash

echo "🚀 Starting deployment fix..."

# Navigate to project directory
cd /www/wwwroot/alkamelah1.anwaralolmaa.com || exit

# Backup composer.lock
if [ -f composer.lock ]; then
    cp composer.lock composer.lock.backup
    echo "✅ Backed up composer.lock"
fi

# Remove composer.lock
rm -f composer.lock
echo "✅ Removed composer.lock"

# Clear composer cache
composer clear-cache
echo "✅ Cleared composer cache"

# Install dependencies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
echo "✅ Installed dependencies"

# Run migrations
php artisan migrate --force
echo "✅ Ran migrations"

# Clear application cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "✅ Cleared application cache"

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimized application"

# Set permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
echo "✅ Set permissions"

echo "✅ Deployment completed successfully!"
