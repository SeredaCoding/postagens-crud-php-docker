FROM php:8.1-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY ./src /var/www/html

# Ativar rewrite e definir permissões
RUN a2enmod rewrite
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80