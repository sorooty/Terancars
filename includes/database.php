<?php
/**
 * Fonctions liées à la base de données
 */

/**
 * Récupère les véhicules
 */
function getVehicles($limit = null) {
    global $db;
    $sql = "SELECT * FROM vehicules";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Récupère les avis clients
 */
function getTestimonials($limit = 3) {
    global $db;
    $sql = "SELECT ac.*, c.nom as client_nom 
            FROM avis_clients ac 
            JOIN clients c ON ac.id_client = c.id_client 
            ORDER BY ac.date_avis DESC 
            LIMIT " . (int)$limit;
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Récupère un véhicule par son ID
 */
function getVehicleById($id) {
    global $db;
    try {
        $sql = "SELECT * FROM vehicules WHERE id_vehicule = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $vehicle = $stmt->fetch();

        if ($vehicle) {
            // Ajout des champs manquants avec des valeurs par défaut si nécessaire
            $vehicle['disponible'] = isset($vehicle['disponible']) ? $vehicle['disponible'] : true;
            $vehicle['disponible_location'] = isset($vehicle['disponible_location']) ? $vehicle['disponible_location'] : true;
            $vehicle['prix_location'] = isset($vehicle['prix_location']) ? $vehicle['prix_location'] : round($vehicle['prix'] * 0.002);
            $vehicle['description'] = isset($vehicle['description']) ? $vehicle['description'] : 
                "Découvrez notre {$vehicle['marque']} {$vehicle['modele']} {$vehicle['annee']}. " .
                "Un véhicule {$vehicle['carburant']} avec transmission {$vehicle['transmission']}, " .
                "offrant un excellent rapport qualité-prix.";
            $vehicle['puissance'] = isset($vehicle['puissance']) ? $vehicle['puissance'] : 0;
            $vehicle['couleur'] = isset($vehicle['couleur']) ? $vehicle['couleur'] : 'Non spécifiée';
            
            // Récupération des images
            $vehicleImage = getVehicleImage($vehicle['marque'], $vehicle['modele']);
            $vehicle['images'] = $vehicleImage ? [$vehicleImage] : ['default-car.jpg'];
            
            return $vehicle;
        }
        return null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du véhicule : " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère les marques populaires
 */
function getPopularBrands() {
    return defined('POPULAR_BRANDS') ? POPULAR_BRANDS : [
        'Renault',
        'Peugeot',
        'Volkswagen',
        'Toyota',
        'BMW',
        'Mercedes',
        'Audi',
        'Ford'
    ];
}

/**
 * Ajoute un véhicule au panier
 */
function addToCart($vehicleId, $type = 'achat') {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $vehicle = getVehicleById($vehicleId);
    if (!$vehicle) {
        return false;
    }

    // Vérifier la disponibilité
    if ($type === 'location' && !$vehicle['disponible_location']) {
        return false;
    }
    if ($type === 'achat' && !$vehicle['disponible']) {
        return false;
    }

    // Créer une clé unique pour le panier (véhicule + type)
    $cartKey = $vehicleId . '_' . $type;

    // Préparer les données de l'article
    $cartItem = [
        'id_vehicule' => $vehicleId,
        'marque' => $vehicle['marque'],
        'modele' => $vehicle['modele'],
        'type' => $type,
        'prix' => $type === 'location' ? $vehicle['prix_location'] : $vehicle['prix'],
        'image' => $vehicle['images'][0] ?? 'default-car.jpg',
        'quantity' => 1
    ];

    // Si l'article existe déjà, incrémenter la quantité
    if (isset($_SESSION['panier'][$cartKey])) {
        $_SESSION['panier'][$cartKey]['quantity']++;
    } else {
        $_SESSION['panier'][$cartKey] = $cartItem;
    }

    return true;
} 