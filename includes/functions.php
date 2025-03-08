<?php
/**
 * Fonctions utilitaires pour le site
 */

/**
 * Formate un prix en euros avec le symbole €
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Retourne l'URL complète d'un asset
 */
function asset($path) {
    $path = trim($path, '/');
    
    // Images (dans assets/images)
    if (strpos($path, 'images/') === 0) {
        return SITE_URL . '/public/assets/' . $path;
    }
    
    // CSS, JS et autres assets
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
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est un administrateur
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
} 