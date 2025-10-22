# Použijeme PHP 8.2 s Apache
FROM php:8.2-apache

# Nastavíme pracovný adresár
WORKDIR /var/www/html

# Nainštalujeme systémové balíčky a PHP rozšírenia
RUN apt-get update && apt-get install -y unzip git libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Skopírujeme celý projekt do kontajnera
COPY . .

# Inštalácia Composeru a závislostí
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Skopírujeme príklad .env, ak neexistuje
RUN cp .env.example .env || true

# Vygenerujeme APP key
RUN php artisan key:generate || true

# Nastavíme oprávnenia
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Otvoríme port
EXPOSE 10000

# Vymažeme cache pri štarte, aby Render ENV premenné fungovali
CMD php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan serve --host 0.0.0.0 --port 10000
