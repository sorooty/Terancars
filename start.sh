#!/bin/bash

# Mettre à jour les permissions si nécessaire (optionnel, à tester)
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/public/images

# Activer Apache et forcer le mode foreground pour éviter l'arrêt du conteneur
apache2-foreground
