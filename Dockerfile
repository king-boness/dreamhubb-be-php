FROM alpine:3.18

# Inštaluj PHP a všetky potrebné moduly pre Laravel aj Composer
RUN apk add --no-cache \
    php \
    php-cli \
    php-mbstring \
    php-xml \
    php-pgsql \
    php-phar \
    php-openssl \
    php-tokenizer \
    php-json \
    php-session \
    unzip \
    curl \
    git

# Nastav pracovný priečinok
WORKDIR /app

# Skopíruj všetky súbory (vrátane start.sh)
COPY . .

# Urob skript spustiteľný
RUN chmod +x /app/start.sh

# Spúšťací príkaz
CMD ["/bin/sh", "-lc", "./start.sh"]
