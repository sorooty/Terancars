#!/bin/bash
set -e

echo "Starting initialization process..."

# Utilisation directe des variables Railway
export MYSQL_URL="${MYSQL_URL:-mysql://root:iIhwHjdUKGZynRaLZvhkRcZHIQvSKiRm@mysql.railway.internal:3306/railway}"

# Affichage des variables d'environnement (sans les mots de passe)
echo "Environment configuration:"
echo "RAILWAY_ENVIRONMENT: $RAILWAY_ENVIRONMENT"
echo "MYSQL_URL: $MYSQL_URL"
echo "MYSQL_DATABASE: railway"
echo "MYSQL_HOST: mysql.railway.internal"

# Configuration du fichier php.ini
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
    echo "date.timezone = 'Africa/Dakar'"
} > /usr/local/etc/php/conf.d/custom-php.ini

# Configuration d'Apache
echo "Configuring Apache..."
cat > /etc/apache2/sites-available/000-default.conf << 'EOL'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        
        # Activation du module de réécriture
        RewriteEngine On
        
        # Redirection de toutes les requêtes vers index.php
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </Directory>

    # Configuration des logs
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    
    # Activation des en-têtes
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Frame-Options "SAMEORIGIN"
</VirtualHost>
EOL

# Activation des modules Apache nécessaires
a2enmod rewrite headers

# Test de connexion MySQL
echo "Testing MySQL connection..."
php -r "
try {
    \$options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        PDO::MYSQL_ATTR_SSL_CA => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    );
    
    \$mysql_url = parse_url('${MYSQL_URL}');
    \$host = \$mysql_url['host'];
    \$port = isset(\$mysql_url['port']) ? \$mysql_url['port'] : 3306;
    \$dbname = ltrim(\$mysql_url['path'], '/');
    \$user = \$mysql_url['user'];
    \$pass = \$mysql_url['pass'];
    
    \$dsn = \"mysql:host={\$host};port={\$port};dbname={\$dbname};charset=utf8mb4\";
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

# Mise à jour des permissions
echo "Updating permissions..."
mkdir -p /var/www/html/public/images
chown -R www-data:www-data /var/www/html
find /var/www/html -type f -exec chmod 644 {} \;
find /var/www/html -type d -exec chmod 755 {} \;
chmod -R 777 /var/www/html/public/images

# Création du fichier .htaccess dans le dossier public
echo "Creating .htaccess file..."
cat > /var/www/html/public/.htaccess << 'EOL'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirection vers HTTPS si nécessaire
    RewriteCond %{HTTP:X-Forwarded-Proto} =http
    RewriteRule .* https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Ne pas rediriger les fichiers et dossiers existants
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    
    # Rediriger toutes les autres requêtes vers index.php
    RewriteRule ^ index.php [L]
</IfModule>
EOL

# Vérification de la configuration Apache
echo "Verifying Apache configuration..."
apache2ctl configtest

echo "Initialization complete. Starting Apache..."
exec apache2-foreground
