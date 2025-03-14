#!/bin/bash

# Attendre que MySQL soit prêt
echo "Attente de la disponibilité de MySQL..."
while ! nc -z $MYSQLHOST $MYSQLPORT; do
  sleep 1
done
echo "MySQL est prêt !"

# Mettre à jour les permissions si nécessaire
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/public/images

# Démarrer Apache en arrière-plan
apache2-foreground
