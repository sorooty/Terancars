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
    
    // Gestion spéciale du panier
    if ($path === 'panier' || $path === 'pages/panier' || $path === 'pages/panier/index.php') {
        return SITE_URL . '/panier';
    }
    
    // Gestion spéciale des pages de détail des véhicules
    if (strpos($path, 'pages/vehicule/detail') === 0 || strpos($path, 'vehicule/detail') === 0) {
        $query = parse_url($path, PHP_URL_QUERY);
        return SITE_URL . '/vehicule/detail' . ($query ? '?' . $query : '');
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
    $marque = strtolower(trim($marque));
    $modele = strtolower(trim($modele));
    
    // Nettoyage des caractères spéciaux mais conservation des espaces
    $marque = preg_replace('/[^a-z0-9\s-]/', '', $marque);
    $modele = preg_replace('/[^a-z0-9\s-]/', '', $modele);
    
    // Tableau des extensions d'images possibles
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    
    // Dossier des images
    $imageDir = ROOT_PATH . '/public/images/vehicules/';
    
    // Récupérer tous les fichiers du dossier
    $files = scandir($imageDir);
    
    // Différentes variantes de noms à essayer
    $variants = [
        // Exact match avec différents séparateurs
        $marque . ' ' . $modele,
        $marque . '-' . $modele,
        $marque . '_' . $modele,
        // Juste la marque et le modèle séparément
        $marque,
        $modele
    ];
    
    // Recherche insensible à la casse
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $fileLower = strtolower($file);
        foreach ($variants as $variant) {
            if (strpos($fileLower, str_replace(' ', '-', $variant)) !== false ||
                strpos($fileLower, str_replace(' ', '_', $variant)) !== false ||
                strpos($fileLower, $variant) !== false) {
                return $file; // Retourne le nom exact du fichier
            }
        }
    }
    
    // Si aucune correspondance n'est trouvée, retourner l'image par défaut
    if (file_exists($imageDir . 'default-car.jpg')) {
        return 'default-car.jpg';
    }
    
    // Si même l'image par défaut n'existe pas
    return '';
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