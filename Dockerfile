# Použijeme PHP 8.2 s Apache
FROM php:8.2-apache

# Nastavíme pracovný adresár
WORKDIR /var/www/html

# Nainštalujeme systémové balíčky a rozšírenia PHP
RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Skopírujeme celý projekt (vrátane artisan, app, routes, atď.)
COPY . .

# Nainštalujeme Composer a balíčky
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Ak chýba .env, vytvoríme ho z .env.example
RUN cp .env.example .env || true

# Vygenerujeme Laravel APP key
RUN php artisan key:generate || true

# Nastavíme oprávnenia
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Nastavíme port
EXPOSE 10000

# Spustíme aplikáciu
CMD php artisan serve --host 0.0.0.0 --port 10000
