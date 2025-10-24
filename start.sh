#!/bin/sh
# Start Laravel app on Render

# Inštalácia Composeru
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Inštalácia závislostí
composer install --no-dev --optimize-autoloader

# Generovanie a čistenie cache
php artisan key:generate || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan migrate --force || true

# Nastavenie oprávnení
chmod -R 775 storage bootstrap/cache
chown -R root:root storage bootstrap/cache

# Spustenie Laravel servera
echo "Starting Laravel development server..."
php artisan serve --host=0.0.0.0 --port=10000
