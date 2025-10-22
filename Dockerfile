# Použijeme PHP 8.2 s Apache
FROM php:8.2-apache

# Nastavíme pracovný adresár
WORKDIR /var/www/html

# Nainštalujeme systémové balíčky a PHP rozšírenia
RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

# Skopírujeme celý projekt do kontajnera
COPY . .

# Nainštalujeme Composer a Laravel závislosti
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Skopírujeme príklad .env, ak ešte neexistuje
RUN cp .env.example .env || true

# Vygenerujeme Laravel APP key
RUN php artisan key:generate || true

# Nastavíme oprávnenia pre Laravel storage a cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Otvoríme port 10000 (Render používa dynamický, ale toto pomáha lokálne)
EXPOSE 10000

# ⚠️ Dôležité: na štarte zrušíme cache, aby Render ENV premenné fungovali
CMD php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan serve --host 0.0.0.0 --port 10000
