<?php
// Inclusion du fichier d'initialisation
require_once __DIR__ . '/../includes/init.php';

// Récupérer l'URI demandée et supprimer les paramètres
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Définir le sous-dossier de ton application
$base_url = SITE_URL; // '/DaCar' en local ou '' en production

// Enlever le sous-dossier de l'URL pour obtenir la vraie route
$route = substr($request_uri, strlen($base_url));

// Supprimer le slash final éventuel
$route = rtrim($route, '/') ?: '/';

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
if (isset($routes[$route])) {
    $file_path = ROOT_PATH . $routes[$route];
    if (file_exists($file_path)) {
        require $file_path;
        exit;
    }
}

// Si aucune route ne correspond, afficher la page 404
http_response_code(404);
require __DIR__ . '/pages/errors/404.php';
