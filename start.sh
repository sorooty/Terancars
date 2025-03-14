#!/bin/bash

# Attendre que MySQL soit prêt
if [ ! -z "$MYSQLHOST" ]; then
    echo "Waiting for MySQL to be ready..."
    while ! nc -z $MYSQLHOST ${MYSQLPORT:-3306}; do
        sleep 1
    done
    echo "MySQL is ready!"
fi

# Mettre à jour les permissions si nécessaire
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/public/images

# Démarrer Apache en arrière-plan
apache2-foreground
