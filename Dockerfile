FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get update -y \
    && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        zip \
        unzip \
        git \
        netcat-traditional \
        vim \
        procps \
    && rm -rf /var/lib/apt/lists/*

# Configuration et installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql

# Installation de l'extension OPcache pour de meilleures performances
RUN docker-php-ext-install opcache \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini

# Configuration PHP
RUN { \
        echo 'memory_limit = 256M'; \
        echo 'upload_max_filesize = 64M'; \
        echo 'post_max_size = 64M'; \
        echo 'max_execution_time = 600'; \
        echo 'max_input_vars = 3000'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Configuration d'Apache
RUN a2enmod rewrite headers
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers du projet
COPY . .

# Configuration du script de démarrage
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Création du répertoire pour les images
RUN mkdir -p /var/www/html/public/images

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/public/images

# Variables d'environnement par défaut
ENV RAILWAY_ENVIRONMENT=production \
    MYSQLHOST=localhost \
    MYSQLPORT=3306 \
    MYSQLDATABASE=terancar \
    MYSQLUSER=root \
    MYSQLPASSWORD= \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=1

# Exposition du port
EXPOSE 80

# Utilisation du script de démarrage
CMD ["/start.sh"]
