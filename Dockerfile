FROM alpine:3.18

RUN apk add --no-cache php php-cli php-mbstring php-xml php-pgsql unzip curl git

WORKDIR /app
COPY . .
RUN chmod +x /app/start.sh

CMD ["/bin/sh", "-lc", "./start.sh"]
