<?php
/**
 * Fichier contenant les fonctions de base pour interagir avec la base de données
 */

// Véhicules
function getVehicules($conn, $filters = []) {
    $sql = "SELECT * FROM vehicules WHERE 1=1";
    $types = "";
    $params = [];

    if (!empty($filters['marque'])) {
        $sql .= " AND marque LIKE ?";
        $types .= "s";
        $params[] = "%" . $filters['marque'] . "%";
    }

    if (!empty($filters['prix_min'])) {
        $sql .= " AND prix >= ?";
        $types .= "d";
        $params[] = $filters['prix_min'];
    }

    if (!empty($filters['prix_max'])) {
        $sql .= " AND prix <= ?";
        $types .= "d";
        $params[] = $filters['prix_max'];
    }

    if (isset($filters['disponible_location'])) {
        $sql .= " AND disponible_location = ?";
        $types .= "i";
        $params[] = $filters['disponible_location'];
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function getVehiculeById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM vehicules WHERE id_vehicule = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Utilisateurs
function createUser($conn, $userData) {
    $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, telephone, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
    $hashedPassword = password_hash($userData['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $userData['role'] ?? 'client';
    $stmt->bind_param("sssss", $userData['nom'], $userData['email'], $userData['telephone'], $hashedPassword, $role);
    return $stmt->execute();
}

function getUserByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Commandes
function createCommande($conn, $userId, $montantTotal) {
    $stmt = $conn->prepare("INSERT INTO commandes (id_client, montant_total, statut) VALUES (?, ?, 'en attente')");
    $stmt->bind_param("id", $userId, $montantTotal);
    if ($stmt->execute()) {
        return $conn->insert_id;
    }
    return false;
}

function addDetailsCommande($conn, $commandeId, $produitId, $quantite, $prixUnitaire) {
    $stmt = $conn->prepare("INSERT INTO details_commandes (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $commandeId, $produitId, $quantite, $prixUnitaire);
    return $stmt->execute();
}

// Locations
function createLocation($conn, $userId, $vehiculeId, $dateDebut, $dateFin, $tarifTotal) {
    $stmt = $conn->prepare("INSERT INTO locations (id_client, id_produit, date_debut, date_fin, tarif_total, statut_location) VALUES (?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("iissd", $userId, $vehiculeId, $dateDebut, $dateFin, $tarifTotal);
    return $stmt->execute();
}

// Messages
function createMessage($conn, $messageData) {
    $stmt = $conn->prepare("INSERT INTO messages (nom, prenom, email, telephone, sujet, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", 
        $messageData['nom'],
        $messageData['prenom'],
        $messageData['email'],
        $messageData['telephone'],
        $messageData['sujet'],
        $messageData['message']
    );
    return $stmt->execute();
}

// Avis clients
function addAvisClient($conn, $avisData) {
    $stmt = $conn->prepare("INSERT INTO avis_clients (id_client, id_produit, note, commentaire) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", 
        $avisData['id_client'],
        $avisData['id_produit'],
        $avisData['note'],
        $avisData['commentaire']
    );
    return $stmt->execute();
}

// Rendez-vous
function createRendezVous($conn, $rdvData) {
    $stmt = $conn->prepare("INSERT INTO rendez_vous (id_utilisateur, id_vehicule, date_rdv, statut) VALUES (?, ?, ?, 'en attente')");
    $stmt->bind_param("iis", 
        $rdvData['id_utilisateur'],
        $rdvData['id_vehicule'],
        $rdvData['date_rdv']
    );
    return $stmt->execute();
}

// Support
function createSupportTicket($conn, $ticketData) {
    $stmt = $conn->prepare("INSERT INTO support (id_utilisateur, sujet, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", 
        $ticketData['id_utilisateur'],
        $ticketData['sujet'],
        $ticketData['message']
    );
    return $stmt->execute();
}

// Favoris
function toggleFavori($conn, $userId, $produitId) {
    // Vérifier si le favori existe déjà
    $stmt = $conn->prepare("SELECT id_favori FROM favoris WHERE id_utilisateur = ? AND id_produit = ?");
    $stmt->bind_param("ii", $userId, $produitId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Supprimer le favori
        $stmt = $conn->prepare("DELETE FROM favoris WHERE id_utilisateur = ? AND id_produit = ?");
        $stmt->bind_param("ii", $userId, $produitId);
        return $stmt->execute() ? 'removed' : false;
    } else {
        // Ajouter le favori
        $stmt = $conn->prepare("INSERT INTO favoris (id_utilisateur, id_produit) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $produitId);
        return $stmt->execute() ? 'added' : false;
    }
}

// Notifications
function createNotification($conn, $userId, $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (id_utilisateur, message) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $message);
    return $stmt->execute();
}

// Paiements
function createPaiement($conn, $paiementData) {
    $stmt = $conn->prepare("INSERT INTO paiements (id_transaction, mode_paiement, montant) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", 
        $paiementData['id_transaction'],
        $paiementData['mode_paiement'],
        $paiementData['montant']
    );
    return $stmt->execute();
}

// Accessoires
function getAccessoires($conn, $categorie = null) {
    $sql = "SELECT * FROM accessoires";
    if ($categorie) {
        $sql .= " WHERE categorie = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $categorie);
    } else {
        $stmt = $conn->prepare($sql);
    }
    $stmt->execute();
    return $stmt->get_result();
} 