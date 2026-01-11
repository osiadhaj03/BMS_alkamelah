#!/bin/bash

# Fix composer.lock conflict on server
echo "Resetting composer.lock to repository version..."
git checkout composer.lock

echo "Pulling latest changes..."
git pull origin main

echo "Installing dependencies without update..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "Done! Server is ready."
