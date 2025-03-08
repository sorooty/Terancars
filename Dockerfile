FROM php:8.1-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Copie des fichiers du projet
COPY . /var/www/html/

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configuration d'Apache
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Exposition du port 80
EXPOSE 80

# Démarrage d'Apache
CMD ["apache2-foreground"]
