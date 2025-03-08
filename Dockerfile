FROM php:8.2-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql

# Activation du module Apache rewrite (optionnel mais conseillé)
RUN a2enmod rewrite

# Copie des fichiers du projet vers Apache
COPY . /var/www/html/

# Exposition du port HTTP
EXPOSE 80

# Démarrage d'Apache
CMD ["apache2-foreground"]
