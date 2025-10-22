# Používame PHP s Apache
FROM php:8.2-apache

# Povoliť mod_rewrite
RUN a2enmod rewrite

# Nastaviť DocumentRoot na public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Povoliť .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Nastaviť pracovný adresár
WORKDIR /var/www/html

# Inštalácia systémových balíkov a PHP rozšírení
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    libssl-dev \
    libzip-dev \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pgsql zip

# Skopírovať projekt
COPY . .

# Inštalácia Composeru a závislostí
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && composer dump-autoload -o

# Vytvoriť .env zo vzoru, ak chýba (Render injektne premenné pri štarte)
RUN cp .env.example .env || true

# Laravel cache a key fixy (nech nepadajú počas buildu)
RUN php artisan key:generate --force || true \
    && php artisan config:clear || true \
    && php artisan cache:clear || true \
    && php artisan route:clear || true \
    && php artisan view:clear || true \
    && php artisan optimize:clear || true

# Povolenia
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponovať port a spustiť Apache
EXPOSE 80
CMD ["apache2-foreground"]
