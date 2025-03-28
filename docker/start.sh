#!/bin/bash

# Remplace le port dans la configuration Apache
sed -i "s/\${PORT}/$PORT/g" /etc/apache2/sites-available/000-default.conf

# Démarre Apache en premier plan
apache2-foreground 