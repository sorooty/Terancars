<?php
/**
 * Fonctions de gestion du panier
 */

/**
 * Ajoute un véhicule au panier
 * @param array $vehicule Les données du véhicule
 * @param string $type 'achat' ou 'location'
 * @param int $quantite Quantité désirée
 * @param int $duree Durée de location (en jours, uniquement pour la location)
 * @return bool
 */
function addToCart($vehicule, $type, $quantite = 1, $duree = null) {
    if (!isset($_SESSION['panier'])) {
        initializeCart();
    }

    $id = $vehicule['id_vehicule'];
    $prix = ($type === 'location') ? $vehicule['tarif_location_journalier'] : $vehicule['prix'];

    // Vérifier si le véhicule est déjà dans le panier
    if (isset($_SESSION['panier'][$type][$id])) {
        // Mettre à jour la quantité
        $_SESSION['panier'][$type][$id]['quantite'] += $quantite;
    } else {
        // Ajouter le nouveau véhicule
        $_SESSION['panier'][$type][$id] = [
            'id' => $id,
            'marque' => $vehicule['marque'],
            'modele' => $vehicule['modele'],
            'prix' => $prix,
            'quantite' => $quantite,
            'duree' => $duree
        ];
    }

    // Recalculer les totaux
    calculateCartTotals();
    return true;
}

/**
 * Initialise le panier
 */
function initializeCart() {
    $_SESSION['panier'] = [
        'achat' => [],
        'location' => [],
        'sous_total' => 0,
        'tva' => 0,
        'total' => 0
    ];
}

/**
 * Recalcule les totaux du panier
 */
function calculateCartTotals() {
    $sous_total = 0;
    $tva_rate = 0.20; // 20% TVA

    // Calculer pour les achats
    foreach ($_SESSION['panier']['achat'] as $item) {
        $sous_total += $item['prix'] * $item['quantite'];
    }

    // Calculer pour les locations
    foreach ($_SESSION['panier']['location'] as $item) {
        $sous_total += $item['prix'] * $item['quantite'] * $item['duree'];
    }

    $tva = $sous_total * $tva_rate;
    $total = $sous_total + $tva;

    $_SESSION['panier']['sous_total'] = $sous_total;
    $_SESSION['panier']['tva'] = $tva;
    $_SESSION['panier']['total'] = $total;
}

/**
 * Met à jour la quantité d'un article dans le panier
 * @param int $id ID du véhicule
 * @param string $type Type d'opération ('achat' ou 'location')
 * @param int $quantite Nouvelle quantité
 * @return bool
 */
function updateCartQuantity($id, $type, $quantite) {
    if (!isset($_SESSION['panier'][$type][$id])) {
        return false;
    }

    if ($quantite <= 0) {
        unset($_SESSION['panier'][$type][$id]);
    } else {
        $_SESSION['panier'][$type][$id]['quantite'] = $quantite;
    }

    calculateCartTotals();
    return true;
}

/**
 * Met à jour la durée de location d'un véhicule
 * @param int $id ID du véhicule
 * @param int $duree Nouvelle durée en jours
 * @return bool
 */
function updateRentalDuration($id, $duree) {
    if (!isset($_SESSION['panier']['location'][$id])) {
        return false;
    }

    if ($duree <= 0) {
        unset($_SESSION['panier']['location'][$id]);
    } else {
        $_SESSION['panier']['location'][$id]['duree'] = $duree;
    }

    calculateCartTotals();
    return true;
}

/**
 * Valide le panier et crée la commande
 * @param mysqli $conn Connexion à la base de données
 * @param int $userId ID de l'utilisateur
 * @return int|false ID de la commande créée ou false en cas d'erreur
 */
function validateCart($conn, $userId) {
    if (empty($_SESSION['panier']['achat']) && empty($_SESSION['panier']['location'])) {
        return false;
    }

    try {
        $conn->begin_transaction();

        // Créer la commande principale
        $stmt = $conn->prepare("INSERT INTO commandes (id_client, montant_total, statut) VALUES (?, ?, 'en attente')");
        $stmt->bind_param("id", $userId, $_SESSION['panier']['total']);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création de la commande");
        }
        
        $commandeId = $conn->insert_id;

        // Traiter les achats
        foreach ($_SESSION['panier']['achat'] as $item) {
            $stmt = $conn->prepare("INSERT INTO details_commandes (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $commandeId, $item['id'], $item['quantite'], $item['prix']);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'ajout des détails de la commande");
            }

            // Mettre à jour le stock
            $stmt = $conn->prepare("UPDATE vehicules SET stock = stock - ? WHERE id_vehicule = ?");
            $stmt->bind_param("ii", $item['quantite'], $item['id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la mise à jour du stock");
            }
        }

        // Traiter les locations
        foreach ($_SESSION['panier']['location'] as $item) {
            $stmt = $conn->prepare("INSERT INTO locations (id_client, id_produit, date_debut, date_fin, tarif_total, statut_location) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), ?, 'active')");
            $tarifTotal = $item['prix'] * $item['duree'];
            $stmt->bind_param("iiid", $userId, $item['id'], $item['duree'], $tarifTotal);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la création de la location");
            }
        }

        $conn->commit();
        
        // Vider le panier après validation
        initializeCart();
        
        return $commandeId;
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Vérifie la disponibilité d'un véhicule
 * @param mysqli $conn Connexion à la base de données
 * @param int $vehiculeId ID du véhicule
 * @param string $type Type d'opération ('achat' ou 'location')
 * @param int $quantite Quantité désirée
 * @return bool
 */
function checkVehicleAvailability($conn, $vehiculeId, $type, $quantite = 1) {
    $stmt = $conn->prepare("SELECT stock, disponible_location FROM vehicules WHERE id_vehicule = ?");
    $stmt->bind_param("i", $vehiculeId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($type === 'location') {
        return $result && $result['disponible_location'] == 1 && $result['stock'] >= $quantite;
    } else {
        return $result && $result['stock'] >= $quantite;
    }
}

/**
 * Supprime un article du panier
 * @param int $id ID du véhicule
 * @param string $type Type d'opération ('achat' ou 'location')
 * @return bool
 */
function removeFromCart($id, $type) {
    if (!isset($_SESSION['panier'][$type][$id])) {
        return false;
    }

    unset($_SESSION['panier'][$type][$id]);
    calculateCartTotals();
    return true;
}

/**
 * Vide complètement le panier
 */
function emptyCart() {
    initializeCart();
} 