FROM php:8.2-fpm

# Установка PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Рабочая директория
WORKDIR /var/www/html
