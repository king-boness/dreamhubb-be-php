#!/bin/sh

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

composer install --no-dev --optimize-autoloader

php artisan key:generate || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan migrate --force || true

chmod -R 775 storage bootstrap/cache
chown -R root:root storage bootstrap/cache

php artisan serve --host=0.0.0.0 --port=10000
