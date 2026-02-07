FROM php:8.1-apache

# Install required packages and enable mysqli & pdo_mysql
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    libzip-dev \
    zip \
    unzip \
 && docker-php-ext-install mysqli pdo pdo_mysql \
 && a2enmod rewrite \
 && apt-get clean && rm -rf /var/lib/apt/lists/*
