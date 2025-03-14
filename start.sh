#!/bin/bash
set -e

echo "Starting initialization process..."

# Mapping des variables Railway vers nos variables
export MYSQLHOST=${MYSQL_URL:-$MYSQLHOST}
export MYSQLPORT=${MYSQL_PORT:-$MYSQLPORT}
export MYSQLDATABASE=${MYSQL_DATABASE:-$MYSQLDATABASE}
export MYSQLUSER=${MYSQL_USER:-$MYSQLUSER}
export MYSQLPASSWORD=${MYSQL_ROOT_PASSWORD:-$MYSQLPASSWORD}

# Affichage des variables d'environnement (sans les mots de passe)
echo "Environment configuration:"
echo "RAILWAY_ENVIRONMENT: $RAILWAY_ENVIRONMENT"
echo "MYSQLHOST: $MYSQLHOST"
echo "MYSQLPORT: $MYSQLPORT"
echo "MYSQLDATABASE: $MYSQLDATABASE"
echo "MYSQLUSER: $MYSQLUSER"

# Vérification des variables d'environnement
if [ -z "$MYSQLHOST" ] || [ -z "$MYSQLPORT" ] || [ -z "$MYSQLDATABASE" ] || [ -z "$MYSQLUSER" ]; then
    echo "Error: Required environment variables are not set"
    echo "MYSQLHOST: ${MYSQLHOST:-not set}"
    echo "MYSQLPORT: ${MYSQLPORT:-not set}"
    echo "MYSQLDATABASE: ${MYSQLDATABASE:-not set}"
    echo "MYSQLUSER: ${MYSQLUSER:-not set}"
    exit 1
fi

# Attendre que MySQL soit prêt (avec plus de tentatives)
echo "Waiting for MySQL to be ready..."
max_tries=60
count=0
while [ $count -lt $max_tries ]; do
    if nc -z -w1 $MYSQLHOST ${MYSQLPORT:-3306}; then
        echo "MySQL is available!"
        break
    fi
    echo "Attempt $((count+1))/$max_tries: MySQL is not ready yet..."
    count=$((count+1))
    sleep 2
done

if [ $count -eq $max_tries ]; then
    echo "Error: MySQL did not become available in time"
    exit 1
fi

# Test plus détaillé de la connexion MySQL
echo "Testing MySQL connection..."
php -r "
try {
    \$attempts = 0;
    \$maxAttempts = 5;
    \$connected = false;
    
    while (!\$connected && \$attempts < \$maxAttempts) {
        try {
            \$dbh = new PDO(
                'mysql:host=${MYSQLHOST};port=${MYSQLPORT};dbname=${MYSQLDATABASE}',
                '${MYSQLUSER}',
                '${MYSQLPASSWORD}',
                array(PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
            echo \"Successfully connected to MySQL (attempt \" . (\$attempts + 1) . \")\n\";
            \$connected = true;
        } catch (PDOException \$e) {
            \$attempts++;
            echo \"Connection attempt \" . \$attempts . \" failed: \" . \$e->getMessage() . \"\n\";
            if (\$attempts < \$maxAttempts) {
                echo \"Waiting 2 seconds before retry...\n\";
                sleep(2);
            }
        }
    }
    if (!\$connected) {
        exit(1);
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . \"\n\";
    exit(1);
}"

if [ $? -ne 0 ]; then
    echo "Error: Could not establish MySQL connection"
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

# Configuration PHP
echo "Configuring PHP..."
if [ "$RAILWAY_ENVIRONMENT" != "production" ]; then
    echo "Enabling PHP debug mode..."
    echo "display_errors = On" >> /usr/local/etc/php/php.ini
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini
    echo "log_errors = On" >> /usr/local/etc/php/php.ini
    echo "error_log = /dev/stderr" >> /usr/local/etc/php/php.ini
fi

# Vérification de la configuration Apache
echo "Verifying Apache configuration..."
apache2ctl -t

echo "Initialization complete. Starting Apache..."
exec apache2-foreground
