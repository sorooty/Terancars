#!/bin/bash
set -e

echo "Starting initialization process..."

# Vérification des variables d'environnement
echo "Checking environment variables..."
if [ -z "$MYSQLHOST" ] || [ -z "$MYSQLPORT" ] || [ -z "$MYSQLDATABASE" ] || [ -z "$MYSQLUSER" ]; then
    echo "Error: Required environment variables are not set"
    echo "MYSQLHOST: ${MYSQLHOST:-not set}"
    echo "MYSQLPORT: ${MYSQLPORT:-not set}"
    echo "MYSQLDATABASE: ${MYSQLDATABASE:-not set}"
    echo "MYSQLUSER: ${MYSQLUSER:-not set}"
    exit 1
fi

# Attendre que MySQL soit prêt
echo "Waiting for MySQL to be ready..."
timeout 30 bash -c "until nc -z $MYSQLHOST ${MYSQLPORT:-3306}; do
    echo 'Waiting for MySQL...'
    sleep 1
done"
echo "MySQL is ready!"

# Vérification de la connexion à la base de données
echo "Testing database connection..."
if ! php -r "try {
    \$dbh = new PDO('mysql:host=${MYSQLHOST};port=${MYSQLPORT};dbname=${MYSQLDATABASE}', '${MYSQLUSER}', '${MYSQLPASSWORD}');
    echo 'Database connection successful\n';
} catch(PDOException \$e) {
    echo 'Connection failed: ' . \$e->getMessage() . '\n';
    exit(1);
}"; then
    echo "Error: Could not connect to database"
    exit 1
fi

# Configuration d'Apache
echo "Configuring Apache..."
sed -i "s|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g" /etc/apache2/sites-available/000-default.conf

# Mise à jour des permissions
echo "Updating permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
mkdir -p /var/www/html/public/images
chmod -R 777 /var/www/html/public/images

# Activation du mode de débogage PHP si nécessaire
if [ "$RAILWAY_ENVIRONMENT" != "production" ]; then
    echo "Enabling PHP debug mode..."
    echo "display_errors = On" >> /usr/local/etc/php/php.ini
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini
fi

echo "Initialization complete. Starting Apache..."
exec apache2-foreground
