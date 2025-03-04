<?php
/**
 * Script pour créer l'image par défaut
 */

$defaultImagePath = __DIR__ . '/../assets/images/no-image.jpg';
$sourceImagePath = __DIR__ . '/default-no-image.jpg';  // Image source préexistante

// Vérifier si le dossier existe
$imageDir = dirname($defaultImagePath);
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
    echo "Dossier images créé.<br>";
}

// Copier l'image par défaut si elle n'existe pas
if (!file_exists($defaultImagePath)) {
    // Si l'image source existe, la copier
    if (file_exists($sourceImagePath)) {
        if (copy($sourceImagePath, $defaultImagePath)) {
            echo "Image par défaut copiée avec succès.";
        } else {
            echo "Erreur lors de la copie de l'image par défaut.";
        }
    } else {
        // Si l'image source n'existe pas, télécharger une image depuis une URL
        $imageUrl = 'https://via.placeholder.com/800x600.jpg?text=Image+non+disponible';
        if ($imageContent = @file_get_contents($imageUrl)) {
            if (file_put_contents($defaultImagePath, $imageContent)) {
                echo "Image par défaut téléchargée avec succès.";
            } else {
                echo "Erreur lors de la sauvegarde de l'image par défaut.";
            }
        } else {
            echo "Erreur lors du téléchargement de l'image par défaut.";
        }
    }
} else {
    echo "L'image par défaut existe déjà.";
}

// Vérifier les permissions
if (file_exists($defaultImagePath)) {
    chmod($defaultImagePath, 0644);
    echo "<br>Permissions de l'image mises à jour.";
}

// Afficher le chemin complet pour vérification
echo "<br><br>Chemin de l'image par défaut : " . $defaultImagePath;
echo "<br>L'image existe : " . (file_exists($defaultImagePath) ? 'Oui' : 'Non');
echo "<br>Permissions : " . substr(sprintf('%o', fileperms($defaultImagePath)), -4);
?> 