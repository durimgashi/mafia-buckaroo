FROM php:8.0-apache

WORKDIR /var/www/html

COPY . /var/www/html

RUN apt-get update && \
    apt-get install -y \
        zip \
        unzip \
        libzip-dev \
        && docker-php-ext-install zip pdo_mysql mysqli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-interaction --no-plugins --no-scripts

# Enable .htaccess and mod_rewrite
RUN a2enmod rewrite

EXPOSE 5555

CMD ["apache2-foreground"]
