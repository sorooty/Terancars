<?php
/**
 * Script de transfert des images
 * Attribue les images aux véhicules selon leurs noms
 */

// Inclusion des fichiers nécessaires
require_once '../config/config.php';
require_once '../includes/image_functions.php';

// Fonction pour nettoyer les noms de fichiers
function cleanFileName($fileName) {
    return preg_replace('/[^a-zA-Z0-9\-\_\.]/', '', $fileName);
}

// Fonction pour créer le dossier de destination s'il n'existe pas
function createDestinationFolder($vehicleId) {
    $destDir = $_SERVER['DOCUMENT_ROOT'] . '/TeranCar/assets/images/vehicles/' . $vehicleId;
    if (!is_dir($destDir)) {
        mkdir($destDir, 0777, true);
    }
    return $destDir;
}

// Récupérer tous les véhicules
$query = "SELECT id_vehicule, marque, modele FROM vehicules";
$result = $conn->query($query);

if ($result) {
    while ($vehicle = $result->fetch_assoc()) {
        $sourceDir = __DIR__ . '/../assets/images/voitures';
        $destDir = __DIR__ . '/../assets/images/vehicles/' . $vehicle['id_vehicule'];
        
        // Créer le dossier de destination s'il n'existe pas
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }
        
        // Rechercher les images correspondant à la marque et au modèle
        $searchPatterns = [
            strtolower($vehicle['marque']),
            strtolower($vehicle['modele']),
            strtolower($vehicle['marque'] . $vehicle['modele']),
            strtolower($vehicle['marque'] . '-' . $vehicle['modele']),
            strtolower($vehicle['marque'] . '_' . $vehicle['modele'])
        ];
        
        // Parcourir tous les fichiers du dossier source
        $files = glob($sourceDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
        if ($files) {
            foreach ($files as $file) {
                $fileName = strtolower(basename($file));
                
                // Vérifier si le nom du fichier correspond à la marque/modèle
                foreach ($searchPatterns as $pattern) {
                    if (strpos($fileName, $pattern) !== false) {
                        // Copier l'image dans le dossier du véhicule
                        $destFile = $destDir . '/' . basename($file);
                        if (copy($file, $destFile)) {
                            echo "Image attribuée à {$vehicle['marque']} {$vehicle['modele']}: " . basename($file) . "<br>";
                            
                            // Créer la miniature
                            $thumbFile = $destDir . '/thumb_' . basename($file);
                            list($width, $height) = getimagesize($file);
                            $ratio = min(200/$width, 150/$height);
                            $newWidth = round($width * $ratio);
                            $newHeight = round($height * $ratio);
                            
                            $thumb = imagecreatetruecolor($newWidth, $newHeight);
                            $source = imagecreatefromstring(file_get_contents($file));
                            
                            imagecopyresampled(
                                $thumb, $source,
                                0, 0, 0, 0,
                                $newWidth, $newHeight,
                                $width, $height
                            );
                            
                            imagejpeg($thumb, $thumbFile, 80);
                            imagedestroy($thumb);
                            imagedestroy($source);
                            
                            break; // Sortir de la boucle une fois l'image attribuée
                        }
                    }
                }
            }
        }
    }
    echo "Attribution des images terminée.";
} else {
    echo "Erreur lors de la récupération des véhicules : " . $conn->error;
}

// Fermer la connexion
$conn->close(); 