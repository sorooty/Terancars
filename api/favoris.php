<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

// Vérification de la connexion de l'utilisateur
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Vous devez être connecté pour gérer vos favoris']);
    exit;
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Récupération des données
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['vehicule_id']) || !is_numeric($data['vehicule_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de véhicule invalide']);
    exit;
}

$vehiculeId = intval($data['vehicule_id']);
$userId = $_SESSION['user_id'];

try {
    // Vérification si le véhicule existe déjà dans les favoris
    $checkQuery = "SELECT id FROM favoris WHERE user_id = :user_id AND vehicule_id = :vehicule_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $checkStmt->bindParam(':vehicule_id', $vehiculeId, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        // Le favori existe, on le supprime
        $deleteQuery = "DELETE FROM favoris WHERE user_id = :user_id AND vehicule_id = :vehicule_id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $deleteStmt->bindParam(':vehicule_id', $vehiculeId, PDO::PARAM_INT);
        $deleteStmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Véhicule retiré des favoris',
            'action' => 'removed'
        ]);
    } else {
        // Le favori n'existe pas, on l'ajoute
        $insertQuery = "INSERT INTO favoris (user_id, vehicule_id, created_at) VALUES (:user_id, :vehicule_id, NOW())";
        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $insertStmt->bindParam(':vehicule_id', $vehiculeId, PDO::PARAM_INT);
        $insertStmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Véhicule ajouté aux favoris',
            'action' => 'added'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la gestion des favoris']);
    error_log("Erreur SQL: " . $e->getMessage());
    exit;
} 