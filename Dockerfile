FROM php:8.2-apache

# Copie tout ton projet dans le dossier serveur Apache
COPY . /var/www/html/

# Active le module Rewrite pour les URLs propres si nécessaire
RUN a2enmod rewrite

# Expose le port Apache
EXPOSE 80

# Lance Apache en avant-plan
CMD ["apache2-foreground"]
