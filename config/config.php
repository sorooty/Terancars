<?php
/**
 * Configuration de la base de données
 * Ce fichier contient les paramètres de connexion à la base de données MySQL
 * Version avancée pour la branche version-ia (développement assisté par IA)
 */

// Démarrage de la session
session_start();

// Paramètres de connexion à la base de données
$host = 'localhost';      // Adresse du serveur MySQL
$user = 'root';           // Nom d'utilisateur MySQL
$pass = '';               // Mot de passe MySQL
$dbname = "terancar";     // Nom de la base de données (version IA)
$port = 3307;             // Port MySQL (3306 par défaut, 3307 dans votre configuration)

// Création de la connexion à MySQL
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de connexion à la base de données : " . $conn->connect_error);
}

// Définir l'encodage UTF-8 pour éviter les problèmes de caractères spéciaux
$conn->set_charset("utf8");

// Définition des constantes globales du site
define('SITE_NAME', 'Terancar');
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

// Fonction pour vérifier si une table existe
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Fonction pour vérifier si une colonne existe dans une table
function columnExists($conn, $tableName, $columnName) {
    $result = $conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
    return $result->num_rows > 0;
}

// Fonction pour déboguer la connexion à la base de données
function debugDatabase() {
    global $conn, $host, $user, $dbname, $port;
    
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 15px; border-radius: 5px;'>";
    echo "<h3>Informations de débogage de la base de données</h3>";
    echo "<p>Hôte: $host</p>";
    echo "<p>Utilisateur: $user</p>";
    echo "<p>Base de données: $dbname</p>";
    echo "<p>Port: $port</p>";
    
    // Vérifier la connexion
    if ($conn->connect_error) {
        echo "<p>Statut: <strong>Échec de connexion</strong></p>";
        echo "<p>Erreur: " . $conn->connect_error . "</p>";
    } else {
        echo "<p>Statut: <strong>Connexion réussie</strong></p>";
        
        // Vérifier les tables
        $tables = ['utilisateurs', 'vehicules', 'commandes', 'locations'];
        echo "<h4>Vérification des tables:</h4>";
        echo "<ul>";
        foreach ($tables as $table) {
            $exists = tableExists($conn, $table);
            echo "<li>$table: " . ($exists ? "✅ Existe" : "❌ N'existe pas") . "</li>";
        }
        echo "</ul>";
    }
    echo "</div>";
}

// Fonction pour journaliser les erreurs
function logError($message, $severity = 'ERROR') {
    $logFile = __DIR__ . '/../logs/errors.log';
    $logDir = dirname($logFile);
    
    // Créer le répertoire de logs s'il n'existe pas
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$severity] $message" . PHP_EOL;
    
    // Écrire dans le fichier de log
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Fonction pour vérifier la structure de la base de données
function checkDatabaseStructure() {
    global $conn;
    
    $requiredTables = [
        'utilisateurs' => [
            'id_utilisateur', 'nom', 'prenom', 'email', 'mot_de_passe', 'role'
        ],
        'vehicules' => [
            'id_vehicule', 'marque', 'modele', 'prix', 'kilometrage', 'carburant', 'transmission'
        ],
        'commandes' => [
            'id_commande', 'id_utilisateur', 'date_commande', 'statut', 'montant_total'
        ],
        'locations' => [
            'id_location', 'id_utilisateur', 'id_vehicule', 'date_debut', 'date_fin', 'montant_total'
        ]
    ];
    
    $issues = [];
    
    foreach ($requiredTables as $table => $columns) {
        if (!tableExists($conn, $table)) {
            $issues[] = "Table manquante: $table";
            continue;
        }
        
        foreach ($columns as $column) {
            if (!columnExists($conn, $table, $column)) {
                $issues[] = "Colonne manquante: $column dans la table $table";
            }
        }
    }
    
    return [
        'success' => empty($issues),
        'issues' => $issues
    ];
}
?> 