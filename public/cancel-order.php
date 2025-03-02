<?php
/**
 * Page d'annulation de commande
 * Permet aux utilisateurs d'annuler une commande en attente ou confirmée
 * Adapté à la structure de base de données optimisée
 */

// Inclusion des fichiers nécessaires
include '../config/config.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isLoggedIn()) {
    setAlert("Veuillez vous connecter pour accéder à vos commandes", "warning");
    redirect('login.php');
    exit();
}

// Vérifier si l'ID de commande est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setAlert("Identifiant de commande invalide", "danger");
    redirect('profile.php');
    exit();
}

$orderId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

// Vérifier si la table commandes existe
if (!tableExists($conn, 'commandes')) {
    setAlert("La table des commandes n'existe pas dans la base de données", "danger");
    redirect('profile.php');
    exit();
}

// Vérifier si la commande existe et appartient à l'utilisateur
$query = "SELECT * FROM commandes WHERE id_commande = ? AND id_utilisateur = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setAlert("Commande non trouvée ou vous n'êtes pas autorisé à y accéder", "danger");
    redirect('profile.php');
    exit();
}

$order = $result->fetch_assoc();

// Vérifier si la commande peut être annulée (statut en attente ou confirmée)
$canBeCancelled = in_array(strtolower($order['statut'] ?? ''), ['en attente', 'confirmée']);

if (!$canBeCancelled) {
    setAlert("Cette commande ne peut plus être annulée car elle a déjà été " . strtolower($order['statut']), "warning");
    redirect('order-details.php?id=' . $orderId);
    exit();
}

// Traiter l'annulation
$success = false;
$error = null;

// Mettre à jour le statut de la commande
$query = "UPDATE commandes SET statut = 'annulée' WHERE id_commande = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $orderId);

if ($stmt->execute()) {
    $success = true;
    
    // Mettre à jour le stock si nécessaire
    if (tableExists($conn, 'details_commande') && tableExists($conn, 'vehicules')) {
        $query = "SELECT id_vehicule, quantite FROM details_commande WHERE id_commande = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($item = $result->fetch_assoc()) {
            // Vérifier si la colonne stock existe dans la table vehicules
            if (columnExists($conn, 'vehicules', 'stock')) {
                $updateQuery = "UPDATE vehicules SET stock = stock + ? WHERE id_vehicule = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ii", $item['quantite'], $item['id_vehicule']);
                $updateStmt->execute();
            }
        }
    }
    
    // Enregistrer l'annulation dans l'historique (optionnel)
    // ...
    
    setAlert("Votre commande a été annulée avec succès", "success");
    redirect('order-details.php?id=' . $orderId);
    exit();
} else {
    $error = "Une erreur est survenue lors de l'annulation de la commande: " . $conn->error;
    setAlert($error, "danger");
    redirect('order-details.php?id=' . $orderId);
    exit();
}
?> 