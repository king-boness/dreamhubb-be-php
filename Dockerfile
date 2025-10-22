# Použijeme PHP 8.2 s Apache
FROM php:8.2-apache

# Nastavíme pracovný adresár
WORKDIR /var/www/html

# Skopírujeme composer.json a nainštalujeme composer
COPY composer.json composer.lock ./
RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Skopírujeme zvyšok projektu
COPY . .

# Vytvoríme .env, ak neexistuje
RUN cp .env.example .env || true

# Vygenerujeme Laravel APP key (ak ešte nie je)
RUN php artisan key:generate || true

# Cache pre zlepšenie výkonu
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache || true

# Nastavíme oprávnenia pre Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Nastavíme port, ktorý Render očakáva
EXPOSE 10000

# Spustíme aplikáciu cez php artisan serve
CMD php artisan serve --host 0.0.0.0 --port 10000
