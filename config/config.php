<?php
/**
 * Configuration de la base de données
 * Ce fichier contient les paramètres de connexion à la base de données MySQL
 * Version simplifiée pour la branche main (développement personnel)
 */

// Démarrage de la session
session_start();

// Paramètres de connexion à la base de données
$host = 'localhost';      // Adresse du serveur MySQL
$user = 'root';           // Nom d'utilisateur MySQL
$pass = '';               // Mot de passe MySQL
$dbname = "dacar";        // Nom de la base de données (version originale)
$port = 3306;             // Port MySQL standard

// Création de la connexion à MySQL
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de connexion à la base de données : " . $conn->connect_error);
}

// Définir l'encodage UTF-8 pour éviter les problèmes de caractères spéciaux
$conn->set_charset("utf8");

// Définition des constantes globales du site
define('SITE_NAME', 'DaCar');
define('SITE_URL', '/DaCar/');

// Fonction pour nettoyer les entrées utilisateur
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour vérifier si l'utilisateur est un administrateur
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Fonction pour rediriger vers une page
function redirect($page) {
    header("Location: " . SITE_URL . $page);
    exit();
}

// Fonction pour afficher un message d'alerte
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Fonction pour récupérer et supprimer un message d'alerte
function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

// Fonction pour formater le prix
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

// Fonction pour obtenir les informations de l'utilisateur connecté
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM utilisateurs WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Version simplifiée - sans les fonctions avancées de débogage et de vérification
// ajoutées dans la branche version-ia
?>

