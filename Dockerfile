FROM alpine:3.18

# Inštaluj PHP + závislosti
RUN apk add --no-cache php php-cli php-mbstring php-xml php-pgsql unzip curl git

# Skopíruj celý projekt (vrátane start.sh)
WORKDIR /app
COPY . .

# Sprístupni start.sh
RUN chmod +x /app/start.sh

# Spúšťací príkaz
CMD ["/bin/sh", "-lc", "./start.sh"]
