<?php
// Démarrage de la session
session_start();

// Définition des constantes
define('ROOT_PATH', dirname(__DIR__));
define('SITE_NAME', 'TeranCar');

// Connexion à la base de données
try {
    $db = new PDO(
        'mysql:host=localhost;dbname=terancar;charset=utf8',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

/**
 * Génère l'URL complète pour un chemin donné
 * @param string $path
 * @return string
 */
function url($path = '') {
    $basePath = '/DaCar/';
    return $basePath . ltrim($path, '/');
}

/**
 * Génère l'URL complète pour un asset
 * @param string $path
 * @return string
 */
function asset($path = '') {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Ajoute un véhicule au panier
 * @param int $vehicleId
 * @param string $type
 * @return bool
 */
function addToCart($vehicleId, $type = 'achat') {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    
    // Vérifier si le véhicule existe déjà dans le panier
    foreach ($_SESSION['panier'] as $item) {
        if ($item['vehicule_id'] === $vehicleId && $item['type'] === $type) {
            return false;
        }
    }
    
    // Ajouter le véhicule au panier
    $_SESSION['panier'][] = [
        'vehicule_id' => $vehicleId,
        'type' => $type,
        'date_ajout' => date('Y-m-d H:i:s')
    ];
    
    return true;
}

/**
 * Récupère un véhicule par son ID
 * @param int $id
 * @return array|false
 */
function getVehicleById($id) {
    global $db;
    $query = "SELECT * FROM vehicules WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Récupère les véhicules pour le slider
 * @param int $limit
 * @return array
 */
function getVehicles($limit = 5) {
    global $db;
    $query = "SELECT * FROM vehicules WHERE stock > 0 ORDER BY date_ajout DESC LIMIT :limit";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les marques populaires
 * @return array
 */
function getPopularBrands() {
    global $db;
    $query = "SELECT DISTINCT marque FROM vehicules ORDER BY marque ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Récupère les témoignages
 * @param int $limit
 * @return array
 */
function getTestimonials($limit = 3) {
    global $db;
    $query = "SELECT * FROM temoignages ORDER BY date_avis DESC LIMIT :limit";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Formate un prix
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

/**
 * Récupère l'image d'un véhicule
 * @param string $marque
 * @param string $modele
 * @return string
 */
function getVehicleImage($marque, $modele) {
    $baseDir = ROOT_PATH . '/public/assets/images/vehicules/';
    $fileName = strtolower($marque . '-' . $modele . '.jpg');
    
    if (file_exists($baseDir . $fileName)) {
        return $fileName;
    }
    
    return 'default-car.jpg';
} 