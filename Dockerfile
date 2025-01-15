FROM php:8.2-apache-buster
RUN usermod -u 1000 www-data
RUN a2enmod rewrite
RUN apt-get update \
    && apt-get install -y gnupg2 zlib1g-dev libzip-dev zlib1g-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libxml2-dev
RUN docker-php-ext-install zip mysqli pdo pdo_mysql && docker-php-ext-enable mysqli pdo pdo_mysql sodium
RUN docker-php-ext-configure gd
RUN docker-php-ext-install gd
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf