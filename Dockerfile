FROM php:8.2-cli

# MySQL driver
RUN docker-php-ext-install pdo_mysql mysqli

WORKDIR /app
COPY . .

EXPOSE 8000

CMD php -S 0.0.0.0:8000
