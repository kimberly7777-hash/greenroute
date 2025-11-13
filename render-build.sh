#!/usr/bin/env bash
# exit on error
set -o errexit

# Install PHP PostgreSQL extension
apt-get update
apt-get install -y php-pgsql php8.3-pgsql

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "Build complete!"
