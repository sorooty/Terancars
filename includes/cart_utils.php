<?php
/**
 * Fonctions utilitaires pour la gestion du panier
 */

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