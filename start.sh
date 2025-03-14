#!/bin/bash
set -e

echo "Starting initialization process..."

# Utilisation directe des variables Railway
export MYSQL_URL="mysql://root:iIhwHjdUKGZynRaLZvhkRcZHIQvSKiRm@mysql.railway.internal:3306/railway"

# Affichage des variables d'environnement (sans les mots de passe)
echo "Environment configuration:"
echo "RAILWAY_ENVIRONMENT: $RAILWAY_ENVIRONMENT"
echo "MYSQL_URL: $MYSQL_URL"
echo "MYSQL_DATABASE: railway"
echo "MYSQL_HOST: mysql.railway.internal"

# Test de connexion MySQL simple avec PDO
echo "Testing MySQL connection..."
php -r "
try {
    \$options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    
    \$mysql_url = parse_url('${MYSQL_URL}');
    \$host = \$mysql_url['host'];
    \$port = isset(\$mysql_url['port']) ? \$mysql_url['port'] : 3306;
    \$dbname = ltrim(\$mysql_url['path'], '/');
    \$user = \$mysql_url['user'];
    \$pass = \$mysql_url['pass'];
    
    \$dsn = \"mysql:host={\$host};port={\$port};dbname={\$dbname}\";
    echo \"Attempting connection with host: {\$host}, port: {\$port}, database: {\$dbname}\n\";
    
    \$dbh = new PDO(\$dsn, \$user, \$pass, \$options);
    echo \"Successfully connected to MySQL\n\";
    
    // Vérifier la version de MySQL
    \$version = \$dbh->query('SELECT VERSION()')->fetchColumn();
    echo \"MySQL Version: {\$version}\n\";
    
} catch (PDOException \$e) {
    echo 'Connection failed: ' . \$e->getMessage() . \"\n\";
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
{
    echo "display_errors = On"
    echo "error_reporting = E_ALL"
    echo "log_errors = On"
    echo "error_log = /dev/stderr"
    echo "max_execution_time = 60"
    echo "memory_limit = 256M"
    echo "upload_max_filesize = 64M"
    echo "post_max_size = 64M"
} > /usr/local/etc/php/conf.d/custom-php.ini

# Vérification de la configuration Apache
echo "Verifying Apache configuration..."
apache2ctl -t

echo "Initialization complete. Starting Apache..."
exec apache2-foreground
