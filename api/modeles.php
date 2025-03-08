<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérification du paramètre marque_id
if (!isset($_GET['marque_id']) || !is_numeric($_GET['marque_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de marque invalide']);
    exit;
}

$marqueId = intval($_GET['marque_id']);

try {
    // Préparation de la requête
    $query = "SELECT id, nom FROM modeles WHERE marque_id = :marque_id ORDER BY nom ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':marque_id', $marqueId, PDO::PARAM_INT);
    $stmt->execute();

    // Récupération des résultats
    $modeles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envoi de la réponse
    echo json_encode($modeles);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des modèles']);
    error_log("Erreur SQL: " . $e->getMessage());
    exit;
} 