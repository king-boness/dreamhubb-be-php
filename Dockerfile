# Používame PHP s Apache
FROM php:8.2-apache

# Povoliť mod_rewrite pre Laravel routes
RUN a2enmod rewrite

# Nastaviť DocumentRoot na public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Povoliť .htaccess vo verejnom adresári
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

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

# Vyčistenie Laravel cache pred spustením
RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan optimize:clear || true

# Nastavíme správne oprávnenia
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Spustíme Apache
EXPOSE 80
CMD ["apache2-foreground"]
