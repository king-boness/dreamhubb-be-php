#!/bin/sh

apk update

# Inštalácia Composeru
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Inštalácia PHP knižníc
composer install --no-dev --optimize-autoloader

php artisan key:generate || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan migrate --force || true

php artisan serve --host=0.0.0.0 --port=10000
