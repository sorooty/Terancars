<?php
/**
 * Fonctions de gestion des images des véhicules
 */

/**
 * Sauvegarde une image de véhicule
 * @param int $vehicleId ID du véhicule
 * @param array $file Informations sur le fichier uploadé
 * @return bool Succès de l'opération
 */
function saveVehicleImage($vehicleId, $file) {
    // Vérifier le type de fichier
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    // Créer le dossier des images si nécessaire
    $baseDir = __DIR__ . '/../assets/images/vehicles/' . $vehicleId;
    if (!file_exists($baseDir)) {
        mkdir($baseDir, 0777, true);
    }
    
    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $baseDir . '/' . $filename;
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Créer une miniature
        createThumbnail($destination, $baseDir . '/thumb_' . $filename);
        return true;
    }
    
    return false;
}

/**
 * Crée une miniature d'une image
 * @param string $source Chemin de l'image source
 * @param string $destination Chemin de la miniature
 * @param int $width Largeur maximale
 * @param int $height Hauteur maximale
 * @return bool Succès de l'opération
 */
function createThumbnail($source, $destination, $width = 200, $height = 150) {
    list($sourceWidth, $sourceHeight, $type) = getimagesize($source);
    
    // Calculer les dimensions
    $ratio = min($width / $sourceWidth, $height / $sourceHeight);
    $newWidth = round($sourceWidth * $ratio);
    $newHeight = round($sourceHeight * $ratio);
    
    // Créer l'image de destination
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    
    // Charger l'image source
    switch ($type) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($source);
            // Préserver la transparence
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            break;
        case IMAGETYPE_WEBP:
            $sourceImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    // Redimensionner
    imagecopyresampled(
        $thumb, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $sourceWidth, $sourceHeight
    );
    
    // Sauvegarder la miniature
    switch ($type) {
        case IMAGETYPE_JPEG:
            return imagejpeg($thumb, $destination, 80);
        case IMAGETYPE_PNG:
            return imagepng($thumb, $destination, 8);
        case IMAGETYPE_WEBP:
            return imagewebp($thumb, $destination, 80);
    }
    
    return false;
}

/**
 * Récupère les images d'un véhicule
 * @param int $vehicleId ID du véhicule
 * @return array Liste des chemins des images
 */
function getVehicleImages($vehicleId) {
    $baseDir = __DIR__ . '/../assets/images/vehicles/' . $vehicleId;
    $images = [];
    
    // Debug: Afficher les informations de débogage
    error_log("=== Début de la recherche d'images pour le véhicule " . $vehicleId . " ===");
    error_log("Chemin complet du dossier : " . $baseDir);
    error_log("Le dossier existe : " . (file_exists($baseDir) ? 'Oui' : 'Non'));
    
    if (file_exists($baseDir)) {
        $files = scandir($baseDir);
        error_log("Fichiers trouvés dans le dossier : " . implode(", ", $files));
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && !str_starts_with($file, 'thumb_')) {
                // Vérifier si le fichier est une image
                $filePath = $baseDir . '/' . $file;
                $mimeType = mime_content_type($filePath);
                error_log("Fichier trouvé : " . $file . " (Type MIME : " . $mimeType . ")");
                
                if (strpos($mimeType, 'image/') === 0) {
                    $webPath = '/TeranCar/assets/images/vehicles/' . $vehicleId . '/' . $file;
                    $images[] = $webPath;
                    error_log("Image ajoutée : " . $webPath);
                }
            }
        }
    } else {
        error_log("Le dossier n'existe pas : " . $baseDir);
        // Essayer de créer le dossier
        if (@mkdir($baseDir, 0777, true)) {
            error_log("Dossier créé avec succès");
        } else {
            error_log("Impossible de créer le dossier");
        }
    }
    
    // Si aucune image, retourner l'image par défaut
    if (empty($images)) {
        $defaultImage = '/TeranCar/assets/images/no-image.jpg';
        $images[] = $defaultImage;
        error_log("Aucune image trouvée, utilisation de l'image par défaut : " . $defaultImage);
        
        // Vérifier si l'image par défaut existe physiquement
        $defaultImagePath = __DIR__ . '/..' . $defaultImage;
        error_log("L'image par défaut existe : " . (file_exists($defaultImagePath) ? 'Oui' : 'Non'));
    }
    
    error_log("=== Fin de la recherche d'images ===");
    return $images;
}

/**
 * Supprime une image de véhicule
 * @param int $vehicleId ID du véhicule
 * @param string $imageName Nom du fichier
 * @return bool Succès de l'opération
 */
function deleteVehicleImage($vehicleId, $imageName) {
    $baseDir = __DIR__ . '/../assets/images/vehicles/' . $vehicleId;
    $imagePath = $baseDir . '/' . $imageName;
    $thumbPath = $baseDir . '/thumb_' . $imageName;
    
    $success = true;
    
    // Supprimer l'image principale
    if (file_exists($imagePath)) {
        $success = $success && unlink($imagePath);
    }
    
    // Supprimer la miniature
    if (file_exists($thumbPath)) {
        $success = $success && unlink($thumbPath);
    }
    
    return $success;
}

/**
 * Récupère l'image principale d'un véhicule
 * @param int $vehiculeId ID du véhicule
 * @return string Chemin de l'image principale
 */
function getMainVehicleImage($vehiculeId) {
    $images = getVehicleImages($vehiculeId);
    return $images[0] ?? '/TeranCar/assets/images/no-image.jpg';
}

/**
 * Vérifie la structure des dossiers d'images
 * @return void
 */
function checkImageDirectories() {
    $baseDir = __DIR__ . '/../assets/images';
    
    // Vérifier si le dossier de base existe
    if (!file_exists($baseDir)) {
        mkdir($baseDir, 0777, true);
        error_log("Dossier images créé : " . $baseDir);
    }
    
    // Vérifier si le dossier vehicles existe
    $vehiclesDir = $baseDir . '/vehicles';
    if (!file_exists($vehiclesDir)) {
        mkdir($vehiclesDir, 0777, true);
        error_log("Dossier vehicles créé : " . $vehiclesDir);
    }
    
    // Vérifier si l'image par défaut existe
    $defaultImage = $baseDir . '/no-image.jpg';
    if (!file_exists($defaultImage)) {
        error_log("Attention : L'image par défaut n'existe pas : " . $defaultImage);
    }
} 