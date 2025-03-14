FROM php:8.1-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    netcat \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Activation des modules Apache nécessaires
RUN a2enmod rewrite headers

# Configuration du DocumentRoot d'Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configuration PHP pour la production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = 128M/memory_limit = 256M/g' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/max_execution_time = 30/max_execution_time = 60/g' "$PHP_INI_DIR/php.ini"

# Variables d'environnement par défaut
ENV RAILWAY_ENVIRONMENT=production \
    MYSQLHOST=localhost \
    MYSQLPORT=3306 \
    MYSQLDATABASE=terancar \
    MYSQLUSER=root \
    MYSQLPASSWORD=

# Copie des fichiers du projet
COPY . /var/www/html/

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod -R 777 /var/www/html/public/images

# Copie et exécution du script de démarrage
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Exposition du port 80
EXPOSE 80

CMD ["/start.sh"]
