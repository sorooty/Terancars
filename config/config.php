<?php
/**
 * Configuration globale de l'application TeranCar
 */

// Activation du rapport d'erreurs en mode développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration de base
define('SITE_NAME', 'Teran\'Cars');
define('SITE_URL', '/DaCar');

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'terancar');
define('DB_PORT', '3307');

// Connexion à la base de données
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Configuration du fuseau horaire
date_default_timezone_set('Africa/Dakar');

// Démarrage de la session
session_start();

// Fonctions utilitaires
function asset($path) {
    $path = trim($path, '/');
    
    // Images (dans public/images)
    if (strpos($path, 'images/') === 0) {
        return SITE_URL . '/public/' . $path;
    }
    
    // CSS, JS et autres assets
    if (strpos($path, 'css/') === 0 || strpos($path, 'js/') === 0) {
        return SITE_URL . '/public/assets/' . $path;
    }
    
    // Par défaut, chercher dans assets
    return SITE_URL . '/public/assets/' . $path;
}

function url($path = '') {
    $path = trim($path, '/');
    
    // Page d'accueil
    if (empty($path)) {
        return SITE_URL . '/';
    }
    
    // Pages d'authentification
    if (strpos($path, 'pages/auth/') === 0) {
        $pathParts = explode('/', $path);
        return SITE_URL . '/auth/' . end($pathParts);
    }
    
    // Pages normales
    if (strpos($path, 'pages/') === 0) {
        $pathParts = explode('/', $path);
        array_shift($pathParts); // Enlève "pages"
        return SITE_URL . '/' . implode('/', $pathParts);
    }
    
    // Autres URLs
    return SITE_URL . '/' . $path;
}

function redirect($path) {
    header('Location: ' . url($path));
    exit();
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour récupérer les véhicules
function getVehicles($limit = null) {
    global $db;
    $sql = "SELECT * FROM vehicules";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

// Fonction pour récupérer les avis clients
function getTestimonials($limit = 3) {
    global $db;
    $sql = "SELECT ac.*, c.nom as client_nom 
            FROM avis_clients ac 
            JOIN clients c ON ac.id_client = c.id_client 
            ORDER BY ac.date_avis DESC 
            LIMIT " . (int)$limit;
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

// Fonction pour récupérer les marques populaires
function getPopularBrands() {
    return [
        'Renault',
        'Peugeot',
        'Volkswagen',
        'Toyota',
        'BMW',
        'Mercedes',
        'Audi',
        'Ford'
    ];
}

// Fonction pour formater le prix
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

// Fonction pour obtenir l'image d'un véhicule
function getVehicleImage($marque, $modele) {
    $marque = strtolower(trim($marque));
    $modele = strtolower(trim($modele));
    
    // Nettoyage des caractères spéciaux mais conservation des espaces
    $marque = preg_replace('/[^a-z0-9\s-]/', '', $marque);
    $modele = preg_replace('/[^a-z0-9\s-]/', '', $modele);
    
    // Tableau des extensions d'images possibles
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    
    // Dossier des images
    $imageDir = ROOT_PATH . '/public/images/vehicules/';
    
    // Récupérer tous les fichiers du dossier
    $files = scandir($imageDir);
    
    // Différentes variantes de noms à essayer
    $variants = [
        // Exact match avec différents séparateurs
        $marque . ' ' . $modele,
        $marque . '-' . $modele,
        $marque . '_' . $modele,
        // Juste la marque et le modèle séparément
        $marque,
        $modele
    ];
    
    // Recherche insensible à la casse
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fileLower = strtolower($file);
        foreach ($variants as $variant) {
            if (strpos($fileLower, str_replace(' ', '-', $variant)) !== false ||
                strpos($fileLower, str_replace(' ', '_', $variant)) !== false ||
                strpos($fileLower, $variant) !== false) {
                return $file; // Retourne le nom exact du fichier
            }
        }
    }
    
    // Si aucune correspondance n'est trouvée, retourner l'image par défaut
    if (file_exists($imageDir . 'default-car.jpg')) {
        return 'default-car.jpg';
    }
    
    // Si même l'image par défaut n'existe pas
    return '';
}