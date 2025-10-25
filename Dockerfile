# Stage 1: Build
FROM composer:2 AS build
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Runtime
FROM alpine:3.20

# Install PHP, PostgreSQL extensions, and PostgreSQL client
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
    libpq \
    postgresql16-client

# Update certificates
RUN update-ca-certificates

# Link PHP binary
RUN ln -s /usr/bin/php82 /usr/bin/php

# Set working directory
WORKDIR /app

# Copy the built app
COPY --from=build /app /app

# Make start script executable
RUN chmod +x /app/start.sh || true

CMD ["/app/start.sh"]
