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
    return '/DaCar/assets/' . ltrim($path, '/');
}

/**
 * Retourne l'URL complète d'une page
 */
function url($path) {
    return '/DaCar/' . ltrim($path, '/');
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