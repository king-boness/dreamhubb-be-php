FROM alpine:3.20

# Inštaluj PHP 8.2 a všetky potrebné rozšírenia pre Laravel + Composer
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
    unzip

# Nastav alias, aby "php" spúšťalo PHP 8.2
RUN ln -s /usr/bin/php82 /usr/bin/php

# Nastav pracovný priečinok
WORKDIR /app

# Skopíruj všetky súbory projektu
COPY . .

# Urob start.sh spustiteľný
RUN chmod +x /app/start.sh

# Spúšťací príkaz
CMD ["/bin/sh", "-lc", "./start.sh"]
