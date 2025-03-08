<?php
// Définition du chemin racine
define('ROOT_PATH', dirname(__DIR__));

// Configuration
require_once ROOT_PATH . '/config/config.php';

// Démarrage de la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour obtenir l'URL de base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . SITE_URL;
} 