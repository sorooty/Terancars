FROM php:8.1-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql

# Activation des modules Apache nécessaires
RUN a2enmod rewrite

# Activation explicite du module PHP dans Apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie des fichiers du projet
COPY . /var/www/html/

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/public/images

# Configuration d'Apache pour permettre .htaccess
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-php.conf \
    && a2enconf docker-php

# Exposition du port Apache
EXPOSE 80

# Démarrage d'Apache
CMD ["apache2-foreground"]
