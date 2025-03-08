<?php
// Inclusion du fichier d'initialisation
require_once __DIR__ . '/../includes/init.php';

// Récupérer l'URI demandée
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = parse_url($request_uri, PHP_URL_PATH);

// Supprimer le slash final s'il existe
$base_path = rtrim($base_path, '/');

// Routes définies
$routes = [
    '/' => '/public/pages/home.php',
    '/catalogue' => '/public/pages/catalogue/index.php',
    '/contact' => '/public/pages/contact.php',
    '/about' => '/public/pages/about/index.php',
    '/auth/login' => '/public/pages/auth/login.php',
    '/auth/inscription' => '/public/pages/auth/inscription.php',
    '/auth/reset-password' => '/public/pages/auth/reset-password.php',
    '/vehicule/detail' => '/public/pages/vehicule/detail.php',
    '/panier' => '/public/pages/panier.php'
];

// Vérifier si la route existe
if (isset($routes[$base_path])) {
    $file_path = ROOT_PATH . $routes[$base_path];
    if (file_exists($file_path)) {
        require $file_path;
        exit;
    }
}

// Si aucune route ne correspond, afficher la page 404
http_response_code(404);
require __DIR__ . '/pages/errors/404.php'; 