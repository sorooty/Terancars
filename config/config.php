<?php
/**
 * Configuration globale de l'application TeranCar
 */

// Activation du rapport d'erreurs en mode développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définition du chemin racine
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));

// Configuration de base
define('SITE_NAME', 'Teran\'Cars');
$public_domain = getenv('RAILWAY_PUBLIC_DOMAIN');
define('SITE_URL', $public_domain ? '' : '/DaCar');

// Lecture des informations Railway
$db_url = getenv('MYSQLDATABASE_URL');

if (!$db_url) {
    // Mode développement local
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "terancar";
    $port = 3307;
} else {
    // Mode production (Railway)
    $url = parse_url($db_url);
    if ($url === false || !isset($url["host"]) || !isset($url["user"]) || !isset($url["pass"]) || !isset($url["path"])) {
        die('Configuration de base de données invalide');
    }
    $host = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $dbname = ltrim($url["path"], '/');
    $port = $url["port"] ?? 3306;
}

// Connexion à la base de données PDO
try {
    $db = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
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

// Fonction pour récupérer un véhicule par son ID
function getVehicleById($id) {
    global $db;
    try {
        $sql = "SELECT * FROM vehicules WHERE id_vehicule = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $vehicle = $stmt->fetch();

        if ($vehicle) {
            // Ajout des champs manquants avec des valeurs par défaut si nécessaire
            $vehicle['disponible'] = isset($vehicle['disponible']) ? $vehicle['disponible'] : true;
            $vehicle['disponible_location'] = isset($vehicle['disponible_location']) ? $vehicle['disponible_location'] : true;
            $vehicle['prix_location'] = isset($vehicle['prix_location']) ? $vehicle['prix_location'] : round($vehicle['prix'] * 0.002); // 0.2% du prix comme tarif journalier par défaut
            $vehicle['description'] = isset($vehicle['description']) ? $vehicle['description'] : 
                "Découvrez notre {$vehicle['marque']} {$vehicle['modele']} {$vehicle['annee']}. " .
                "Un véhicule {$vehicle['carburant']} avec transmission {$vehicle['transmission']}, " .
                "offrant un excellent rapport qualité-prix.";
            $vehicle['puissance'] = isset($vehicle['puissance']) ? $vehicle['puissance'] : 0;
            $vehicle['couleur'] = isset($vehicle['couleur']) ? $vehicle['couleur'] : 'Non spécifiée';
            
            // Récupération des images
            $vehicleImage = getVehicleImage($vehicle['marque'], $vehicle['modele']);
            $vehicle['images'] = $vehicleImage ? [$vehicleImage] : ['default-car.jpg'];
            
            return $vehicle;
        }

        // Données de test si le véhicule n'est pas trouvé
        return [
            'id_vehicule' => $id,
            'marque' => 'Honda',
            'modele' => 'Civic',
            'annee' => 2021,
            'prix' => 21000000,
            'prix_location' => 42000,
            'kilometrage' => 12000,
            'carburant' => 'essence',
            'transmission' => 'manuelle',
            'puissance' => 130,
            'couleur' => 'Gris métallisé',
            'description' => 'Honda Civic 2021 en excellent état. Parfaite pour une conduite urbaine confortable et économique.',
            'disponible' => true,
            'disponible_location' => true,
            'images' => ['civic1.jpg', 'civic2.jpg', 'civic3.jpg']
        ];
    } catch (PDOException $e) {
        // En cas d'erreur, retourner les données de test
        error_log("Erreur lors de la récupération du véhicule : " . $e->getMessage());
        return [
            'id_vehicule' => $id,
            'marque' => 'Honda',
            'modele' => 'Civic',
            'annee' => 2021,
            'prix' => 21000000,
            'prix_location' => 42000,
            'kilometrage' => 12000,
            'carburant' => 'essence',
            'transmission' => 'manuelle',
            'puissance' => 130,
            'couleur' => 'Gris métallisé',
            'description' => 'Honda Civic 2021 en excellent état. Parfaite pour une conduite urbaine confortable et économique.',
            'disponible' => true,
            'disponible_location' => true,
            'images' => ['civic1.jpg', 'civic2.jpg', 'civic3.jpg']
        ];
    }
}

// Fonction pour ajouter un véhicule au panier
function addToCart($vehicleId, $type = 'achat') {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $vehicle = getVehicleById($vehicleId);
    if (!$vehicle) {
        return false;
    }

    // Vérifier la disponibilité
    if ($type === 'location' && !$vehicle['disponible_location']) {
        return false;
    }
    if ($type === 'achat' && !$vehicle['disponible']) {
        return false;
    }

    // Créer une clé unique pour le panier (véhicule + type)
    $cartKey = $vehicleId . '_' . $type;

    // Préparer les données de l'article
    $cartItem = [
        'id_vehicule' => $vehicleId,
        'marque' => $vehicle['marque'],
        'modele' => $vehicle['modele'],
        'type' => $type,
        'prix' => $type === 'location' ? $vehicle['prix_location'] : $vehicle['prix'],
        'image' => $vehicle['images'][0] ?? 'default-car.jpg',
        'quantity' => 1
    ];

    // Si l'article existe déjà, incrémenter la quantité
    if (isset($_SESSION['panier'][$cartKey])) {
        $_SESSION['panier'][$cartKey]['quantity']++;
    } else {
        $_SESSION['panier'][$cartKey] = $cartItem;
    }

    return true;
}