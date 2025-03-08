<?php
/**
 * Fonctions utilitaires pour la gestion des images
 */

/**
 * Retourne le chemin de l'image d'un véhicule
 */
function getVehicleImage($marque, $modele) {
    $basePath = ROOT_PATH . '/public/assets/images/vehicles/';
    $filename = strtolower(str_replace(' ', '-', $marque . '-' . $modele));
    
    // Chercher avec différentes extensions
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    foreach ($extensions as $ext) {
        if (file_exists($basePath . $filename . '.' . $ext)) {
            return 'images/vehicles/' . $filename . '.' . $ext;
        }
    }
    
    // Si aucune correspondance n'est trouvée, retourner l'image par défaut
    if (file_exists($basePath . 'default-car.jpg')) {
        return 'images/vehicles/default-car.jpg';
    }
    
    return '';
}

/**
 * Retourne le chemin du logo d'une marque
 */
function getBrandLogo($marque) {
    $basePath = ROOT_PATH . '/public/assets/images/brands/';
    $filename = strtolower(str_replace(' ', '-', $marque));
    
    // Chercher avec différentes extensions
    $extensions = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
    foreach ($extensions as $ext) {
        if (file_exists($basePath . $filename . '-logo.' . $ext)) {
            return 'images/brands/' . $filename . '-logo.' . $ext;
        }
    }
    
    return 'images/brands/default-brand.png';
} 