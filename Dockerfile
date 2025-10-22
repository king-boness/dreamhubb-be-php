# Používame PHP s Apache
FROM php:8.2-apache

# Nastavíme pracovný adresár
WORKDIR /var/www/html

# Inštalujeme systémové balíčky a PHP rozšírenia
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Skopírujeme všetky súbory projektu
COPY . .

# Inštalácia Composeru a PHP závislostí
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

# Nastavíme Apache DocumentRoot na public/
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Povolenie mod_rewrite pre Laravel routing
RUN a2enmod rewrite

# Nastavíme správne oprávnenia
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponujeme port a spustíme Apache
EXPOSE 80
CMD ["apache2-foreground"]
