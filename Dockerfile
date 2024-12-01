FROM php:8.1-apache

# Install mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable mod_rewrite (optional, if needed for frameworks like Laravel or Symfony)
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html/
