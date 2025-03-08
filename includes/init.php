<?php
/**
 * Fichier d'initialisation de l'application
 * Ce fichier est le point d'entrée pour toutes les pages de l'application
 */

// Définition du chemin racine s'il n'est pas déjà défini
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Chargement de la configuration
require_once ROOT_PATH . '/config/config.php';

// Démarrage de la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die("Une erreur est survenue lors de la connexion à la base de données.");
}

// Chargement des fonctions utilitaires
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/image_utils.php';
require_once ROOT_PATH . '/includes/database.php';
require_once ROOT_PATH . '/includes/cart_utils.php';

/**
 * Fonction pour obtenir l'URL de base du site
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . SITE_URL;
}

// Définition des variables globales pour les templates
$currentPage = '';
$pageTitle = SITE_NAME;
$pageDescription = ''; 