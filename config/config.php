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
require_once ROOT_PATH . '/includes/image_utils.php';
require_once ROOT_PATH . '/includes/database.php';
require_once ROOT_PATH . '/includes/cart_utils.php';