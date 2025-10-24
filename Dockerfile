# Používame Alpine ako základ
FROM alpine:3.20

# Inštalácia PHP + PostgreSQL + nástrojov + SSL certifikátov
RUN apk add --no-cache \
    php82 \
    php82-cli \
    php82-common \
    php82-mbstring \
    php82-xml \
    php82-dom \
    php82-phar \
    php82-openssl \
    php82-tokenizer \
    php82-json \
    php82-session \
    php82-pdo \
    php82-pdo_pgsql \
    php82-pgsql \
    php82-curl \
    php82-zip \
    php82-fileinfo \
    php82-iconv \
    curl \
    git \
    unzip \
    ca-certificates \
    openssl

# Zaregistruj CA certifikáty
RUN update-ca-certificates

# Symbolický link na php
RUN ln -s /usr/bin/php82 /usr/bin/php

# Nastavenie pracovného adresára
WORKDIR /app

# Skopíruj composer.json a composer.lock (pre caching)
COPY composer.json composer.lock ./

# Inštaluj Composer (globálne)
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Inštaluj Laravel závislosti (bez dev balíkov)
RUN composer install --no-dev --optimize-autoloader

# Skopíruj zvyšok projektu
COPY . .

# Nastav práva pre štartovací skript
RUN chmod +x /app/start.sh

# Spúšťací príkaz (migrácie + Laravel server)
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000"]
