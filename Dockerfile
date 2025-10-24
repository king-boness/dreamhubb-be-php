# Používame Alpine ako základ
FROM alpine:3.20

# Inštalácia PHP + PostgreSQL + potrebných nástrojov + SSL certifikátov
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
    ca-certificates

# Symbolický link na php
RUN ln -s /usr/bin/php82 /usr/bin/php

# Pracovný adresár
WORKDIR /app

# Skopírovanie projektu
COPY . .

# Spúšťací skript
RUN chmod +x /app/start.sh

# 🟢 TOTO JE DÔLEŽITÉ — spustí Laravel server pri štarte kontajnera
CMD ["/bin/sh", "/app/start.sh"]
