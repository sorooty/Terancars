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
    && rm -rf /var/lib/apt/lists/*

# Configuration et installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql

# Configuration d'Apache
RUN a2enmod rewrite
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers du projet
COPY . .

# Configuration du script de démarrage
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Variables d'environnement par défaut
ENV RAILWAY_ENVIRONMENT=production \
    MYSQLHOST=localhost \
    MYSQLPORT=3306 \
    MYSQLDATABASE=terancar \
    MYSQLUSER=root \
    MYSQLPASSWORD=

# Exposition du port
EXPOSE 80

# Utilisation du script de démarrage
CMD ["/start.sh"]
