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

// Inclusion des fonctions utilitaires
require_once ROOT_PATH . '/includes/functions.php';

// Fonctions liées à la base de données
function getVehicles($limit = null) {
    global $db;
    $sql = "SELECT * FROM vehicules";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

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

function getVehicleById($id) {
    global $db;
    try {
        $sql = "SELECT * FROM vehicules WHERE id_vehicule = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $vehicle = $stmt->fetch();

        if ($vehicle) {
            // Ajout des champs manquants avec des valeurs par défaut si nécessaire
            if (!isset($vehicle['disponible_location'])) {
                $vehicle['disponible_location'] = true;
            }
            if (!isset($vehicle['tarif_location_journalier'])) {
                $vehicle['tarif_location_journalier'] = round($vehicle['prix'] * 0.002); // 0.2% du prix comme tarif journalier par défaut
            }
            if (!isset($vehicle['stock'])) {
                $vehicle['stock'] = 0;
            }
            
            return $vehicle;
        }

        return false;

    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du véhicule: " . $e->getMessage());
        return false;
    }
}

function addToCart($vehicleId, $type = 'achat') {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $vehicle = getVehicleById($vehicleId);
    if (!$vehicle) {
        return false;
    }

    // Vérifier si le véhicule est déjà dans le panier
    foreach ($_SESSION['panier'] as &$item) {
        if ($item['id'] == $vehicleId && $item['type'] == $type) {
            $item['quantity']++;
            return true;
        }
    }

    // Ajouter le véhicule au panier
    $_SESSION['panier'][] = [
        'id' => $vehicleId,
        'type' => $type,
        'quantity' => 1
    ];

    return true;
}