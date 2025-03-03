<?php
require_once 'config/config.php';
require_once 'includes/database_operations.php';
require_once 'includes/cart_operations.php';

header('Content-Type: application/json');

if (!isset($_POST['action'])) {
    echo json_encode(['error' => 'Action non spécifiée']);
    exit;
}

$response = ['success' => false];

// Fonction pour obtenir l'état complet du panier
function getCartState() {
    return [
        'items' => [
            'achat' => $_SESSION['panier']['achat'] ?? [],
            'location' => $_SESSION['panier']['location'] ?? []
        ],
        'count' => [
            'achat' => count($_SESSION['panier']['achat'] ?? []),
            'location' => count($_SESSION['panier']['location'] ?? []),
            'total' => count($_SESSION['panier']['achat'] ?? []) + count($_SESSION['panier']['location'] ?? [])
        ],
        'totals' => [
            'sous_total' => $_SESSION['panier']['sous_total'] ?? 0,
            'tva' => $_SESSION['panier']['tva'] ?? 0,
            'total' => $_SESSION['panier']['total'] ?? 0
        ]
    ];
}

switch ($_POST['action']) {
    case 'search_vehicles':
        $filters = [
            'marque' => $_POST['marque'] ?? null,
            'prix_min' => $_POST['prix_min'] ?? null,
            'prix_max' => $_POST['prix_max'] ?? null,
            'disponible_location' => $_POST['disponible_location'] ?? null
        ];
        $result = getVehicules($conn, $filters);
        $vehicles = [];
        while ($row = $result->fetch_assoc()) {
            $vehicles[] = $row;
        }
        $response = ['success' => true, 'vehicles' => $vehicles];
        break;

    case 'add_to_favorites':
        if (!isLoggedIn()) {
            $response = ['error' => 'Utilisateur non connecté'];
            break;
        }
        $result = toggleFavori($conn, $_SESSION['user_id'], $_POST['produit_id']);
        $response = ['success' => true, 'status' => $result];
        break;

    case 'create_rdv':
        if (!isLoggedIn()) {
            $response = ['error' => 'Utilisateur non connecté'];
            break;
        }
        $rdvData = [
            'id_utilisateur' => $_SESSION['user_id'],
            'id_vehicule' => $_POST['vehicule_id'],
            'date_rdv' => $_POST['date_rdv']
        ];
        $result = createRendezVous($conn, $rdvData);
        if ($result) {
            createNotification($conn, $_SESSION['user_id'], 'Nouveau rendez-vous créé pour le ' . $_POST['date_rdv']);
            $response = ['success' => true];
        }
        break;

    case 'submit_contact':
        $messageData = [
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'email' => $_POST['email'],
            'telephone' => $_POST['telephone'],
            'sujet' => $_POST['sujet'],
            'message' => $_POST['message']
        ];
        $result = createMessage($conn, $messageData);
        $response = ['success' => $result];
        break;

    case 'add_review':
        if (!isLoggedIn()) {
            $response = ['error' => 'Utilisateur non connecté'];
            break;
        }
        $avisData = [
            'id_client' => $_SESSION['user_id'],
            'id_produit' => $_POST['produit_id'],
            'note' => $_POST['note'],
            'commentaire' => $_POST['commentaire']
        ];
        $result = addAvisClient($conn, $avisData);
        $response = ['success' => $result];
        break;

    case 'create_location':
        if (!isLoggedIn()) {
            $response = ['error' => 'Utilisateur non connecté'];
            break;
        }
        $result = createLocation(
            $conn,
            $_SESSION['user_id'],
            $_POST['vehicule_id'],
            $_POST['date_debut'],
            $_POST['date_fin'],
            $_POST['tarif_total']
        );
        if ($result) {
            createNotification($conn, $_SESSION['user_id'], 'Nouvelle location créée du ' . $_POST['date_debut'] . ' au ' . $_POST['date_fin']);
            $response = ['success' => true];
        }
        break;

    case 'create_support_ticket':
        if (!isLoggedIn()) {
            $response = ['error' => 'Utilisateur non connecté'];
            break;
        }
        $ticketData = [
            'id_utilisateur' => $_SESSION['user_id'],
            'sujet' => $_POST['sujet'],
            'message' => $_POST['message']
        ];
        $result = createSupportTicket($conn, $ticketData);
        $response = ['success' => $result];
        break;

    case 'get_accessories':
        $categorie = $_POST['categorie'] ?? null;
        $result = getAccessoires($conn, $categorie);
        $accessories = [];
        while ($row = $result->fetch_assoc()) {
            $accessories[] = $row;
        }
        $response = ['success' => true, 'accessories' => $accessories];
        break;

    case 'get_cart_state':
        $response = ['success' => true, 'cart' => getCartState()];
        break;

    case 'add_to_cart':
        if (!isset($_POST['vehicule_id']) || !isset($_POST['type'])) {
            $response = ['error' => 'Paramètres manquants'];
            break;
        }

        $vehiculeId = $_POST['vehicule_id'];
        $type = $_POST['type'];
        $quantite = $_POST['quantite'] ?? 1;
        $duree = $_POST['duree'] ?? null;

        if (!checkVehicleAvailability($conn, $vehiculeId, $type, $quantite)) {
            $response = ['error' => 'Véhicule non disponible en quantité suffisante'];
            break;
        }

        $stmt = $conn->prepare("SELECT * FROM vehicules WHERE id_vehicule = ?");
        $stmt->bind_param("i", $vehiculeId);
        $stmt->execute();
        $vehicule = $stmt->get_result()->fetch_assoc();

        if (!$vehicule) {
            $response = ['error' => 'Véhicule non trouvé'];
            break;
        }

        if (addToCart($vehicule, $type, $quantite, $duree)) {
            $response = [
                'success' => true,
                'message' => 'Véhicule ajouté au panier',
                'cart' => getCartState()
            ];
        } else {
            $response = ['error' => 'Erreur lors de l\'ajout au panier'];
        }
        break;

    case 'update_cart_quantity':
        if (!isset($_POST['vehicule_id']) || !isset($_POST['type']) || !isset($_POST['quantite'])) {
            $response = ['error' => 'Paramètres manquants'];
            break;
        }

        if (updateCartQuantity($_POST['vehicule_id'], $_POST['type'], $_POST['quantite'])) {
            $response = [
                'success' => true,
                'cart' => getCartState()
            ];
        } else {
            $response = ['error' => 'Erreur lors de la mise à jour de la quantité'];
        }
        break;

    case 'update_rental_duration':
        if (!isset($_POST['vehicule_id']) || !isset($_POST['duree'])) {
            $response = ['error' => 'Paramètres manquants'];
            break;
        }

        if (updateRentalDuration($_POST['vehicule_id'], $_POST['duree'])) {
            $response = [
                'success' => true,
                'cart' => getCartState()
            ];
        } else {
            $response = ['error' => 'Erreur lors de la mise à jour de la durée'];
        }
        break;

    case 'remove_from_cart':
        if (!isset($_POST['vehicule_id']) || !isset($_POST['type'])) {
            $response = ['error' => 'Paramètres manquants'];
            break;
        }

        if (removeFromCart($_POST['vehicule_id'], $_POST['type'])) {
            $response = [
                'success' => true,
                'cart' => getCartState()
            ];
        } else {
            $response = ['error' => 'Erreur lors de la suppression de l\'article'];
        }
        break;

    case 'empty_cart':
        emptyCart();
        $response = [
            'success' => true,
            'message' => 'Panier vidé avec succès',
            'cart' => getCartState()
        ];
        break;

    case 'validate_cart':
        if (!isLoggedIn()) {
            $response = ['error' => 'Utilisateur non connecté'];
            break;
        }

        $commandeId = validateCart($conn, $_SESSION['user_id']);
        if ($commandeId) {
            $response = [
                'success' => true,
                'commande_id' => $commandeId,
                'message' => 'Commande validée avec succès'
            ];
        } else {
            $response = ['error' => 'Erreur lors de la validation de la commande'];
        }
        break;

    default:
        $response = ['error' => 'Action non reconnue'];
}

echo json_encode($response); 