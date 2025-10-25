# ==========================
# 1️⃣ BUILD STAGE (Composer)
# ==========================
FROM composer:2 AS build

WORKDIR /app

# Skopíruj celý projekt hneď na začiatku
COPY . .

# Inštalácia závislostí (bez dev)
RUN composer install --no-dev --optimize-autoloader

# ==========================
# 2️⃣ RUNTIME STAGE (PHP)
# ==========================
FROM alpine:3.20

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
    openssl \
    libssl3 \
    libpq

RUN update-ca-certificates
RUN ln -s /usr/bin/php82 /usr/bin/php

WORKDIR /app

# Skopíruj všetko (vrátane vendor z build stage)
COPY --from=build /app /app

RUN chmod +x /app/start.sh || true

CMD ["sh", "-c", "php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=10000"]
