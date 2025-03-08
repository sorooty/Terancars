<?php
/**
 * Fonctions utilitaires pour le site
 */

/**
 * Formate un prix en FCFA
 */
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

/**
 * Retourne l'URL complète d'un asset
 */
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

/**
 * Retourne l'URL complète d'une page
 */
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

/**
 * Redirige vers une URL
 */
function redirect($path) {
    header('Location: ' . url($path));
    exit();
}

/**
 * Nettoie une entrée utilisateur
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Retourne le chemin de l'image d'un véhicule
 */
function getVehicleImage($marque, $modele) {
    $filename = strtolower(str_replace(' ', '-', $marque . '-' . $modele)) . '.jpg';
    $filepath = 'images/vehicules/' . $filename;
    
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/DaCar/assets/' . $filepath)) {
        return $filename;
    }
    
    return 'default-car.jpg';
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est un administrateur
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
} 