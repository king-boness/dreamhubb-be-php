# Použijeme oficiálny PHP 8.2 image s Apache
FROM php:8.2-apache

# Nastavime pracovný adresár
WORKDIR /var/www/html

# Zapneme Apache mod_rewrite pre Laravel routes
RUN a2enmod rewrite

# Nastavenie DocumentRoot priamo na /public (bez premenných)
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/laravel.conf && \
    a2enmod rewrite && a2ensite laravel && a2dissite 000-default

# Inštalácia systémových knižníc (vrátane SSL a PostgreSQL)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    libssl-dev \
    libzip-dev \
    ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo_pgsql pgsql zip

# 🔒 Dôležité — Refresh certifikátov pre SSL pripojenie (Render Postgres potrebuje)
RUN update-ca-certificates && chmod 644 /etc/ssl/certs/ca-certificates.crt

# Skopíruj projektové súbory
COPY . .

# Inštalácia Composeru a Laravel závislostí
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && composer dump-autoload -o

# Nastavenie práv
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Nastavenie Laravelu
RUN php artisan config:clear || true \
    && php artisan cache:clear || true

# Port (Render očakáva 80)
EXPOSE 80

# Spustenie Apache
CMD ["apache2-foreground"]
