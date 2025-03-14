FROM php:8.1-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo_mysql mysqli

# Activation des modules Apache nécessaires
RUN a2enmod rewrite headers php8.1

# Configuration pour forcer l’exécution de PHP dans Apache
RUN echo "<FilesMatch \.php$> \n\
    SetHandler application/x-httpd-php\n\
    </FilesMatch>" > /etc/apache2/conf-available/php.conf \
    && a2enconf php

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

# Définition du répertoire de travail
WORKDIR /var/www/html

# Exposition du port Apache
EXPOSE 80

# Copie et exécution du script de démarrage
COPY start.sh /start.sh
RUN chmod +x /start.sh
CMD ["/start.sh"]
