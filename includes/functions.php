<?php
/**
 * Fonctions utilitaires pour l'application
 */

/**
 * Formate un prix en euros avec le symbole €
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirige vers une URL
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Génère une URL absolue
 * @param string $path
 * @return string
 */
function url($path = '') {
    $baseUrl = '/DaCar/';
    return $baseUrl . ltrim($path, '/');
}

/**
 * Génère une URL pour les assets
 * @param string $path
 * @return string
 */
function asset($path = '') {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Affiche un message d'erreur
 * @param string $message
 */
function displayError($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * Affiche un message de succès
 * @param string $message
 */
function displaySuccess($message) {
    $_SESSION['success_message'] = $message;
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
 * Vérifie si l'utilisateur est un administrateur
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Retourne le chemin correct pour une image de véhicule
 * @param int $vehicleId
 * @param string $type
 * @return string
 */
function getVehicleImagePath($vehicleId, $type = 'main') {
    $basePath = $_SERVER['DOCUMENT_ROOT'] . '/DaCar/assets/images/vehicules/';
    $imagePath = $basePath . $vehicleId . '/' . $type . '.jpg';
    
    if (file_exists($imagePath)) {
        return asset('images/vehicules/' . $vehicleId . '/' . $type . '.jpg');
    }
    
    return asset('images/default-car.jpg');
} 