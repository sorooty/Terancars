#!/bin/bash
set -e

echo "Starting initialization process..."

# Mapping des variables Railway vers nos variables
export MYSQLHOST=${MYSQL_URL:-$MYSQLHOST}
export MYSQLPORT=${MYSQL_PORT:-3306}
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
if [ -z "$MYSQLHOST" ] || [ -z "$MYSQLDATABASE" ] || [ -z "$MYSQLUSER" ]; then
    echo "Error: Required environment variables are not set"
    echo "MYSQLHOST: ${MYSQLHOST:-not set}"
    echo "MYSQLDATABASE: ${MYSQLDATABASE:-not set}"
    echo "MYSQLUSER: ${MYSQLUSER:-not set}"
    exit 1
fi

# Attendre que MySQL soit prêt (avec plus de tentatives)
echo "Waiting for MySQL to be ready..."
max_tries=90  # Augmentation du nombre de tentatives
count=0
while [ $count -lt $max_tries ]; do
    if mysql -h"$MYSQLHOST" -P"${MYSQLPORT}" -u"$MYSQLUSER" -p"$MYSQLPASSWORD" -e "SELECT 1" >/dev/null 2>&1; then
        echo "MySQL is available!"
        break
    fi
    echo "Attempt $((count+1))/$max_tries: MySQL is not ready yet..."
    count=$((count+1))
    sleep 2
done

if [ $count -eq $max_tries ]; then
    echo "Error: MySQL did not become available in time"
    echo "Trying to get more information about MySQL status..."
    ping -c 3 "$MYSQLHOST" || echo "Cannot ping MySQL host"
    nc -zv "$MYSQLHOST" "$MYSQLPORT" || echo "Cannot connect to MySQL port"
    exit 1
fi

# Test plus détaillé de la connexion MySQL avec SSL désactivé
echo "Testing MySQL connection..."
php -r "
try {
    \$attempts = 0;
    \$maxAttempts = 5;
    \$connected = false;
    
    while (!\$connected && \$attempts < \$maxAttempts) {
        try {
            \$options = array(
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::MYSQL_ATTR_SSL_CA => false
            );
            
            \$dsn = 'mysql:host=${MYSQLHOST};port=${MYSQLPORT};dbname=${MYSQLDATABASE}';
            echo \"Attempting connection with DSN: {\$dsn}\n\";
            
            \$dbh = new PDO(\$dsn, '${MYSQLUSER}', '${MYSQLPASSWORD}', \$options);
            echo \"Successfully connected to MySQL (attempt \" . (\$attempts + 1) . \")\n\";
            \$connected = true;
            
            // Vérifier la version de MySQL
            \$version = \$dbh->query('SELECT VERSION()')->fetchColumn();
            echo \"MySQL Version: {\$version}\n\";
            
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
