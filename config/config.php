<?php

/**
 * Configuration globale de l'application
 */

// Activation du rapport d'erreurs en mode développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration de base
define('SITE_NAME', 'Teran\'Cars');
define('SITE_URL', '/DaCar');

// Configuration des chemins
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('PAGES_PATH', PUBLIC_PATH . '/pages');
define('ASSETS_PATH', PUBLIC_PATH . '/assets');

// Configuration de la base de données
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'terancar';
$port = 3307;

// Connexion à la base de données PDO
try {
    $db = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Configuration du fuseau horaire
date_default_timezone_set('Africa/Dakar');

// Démarrage de la session
session_start();

// Inclusion des fonctions utilitaires
require_once ROOT_PATH . '/includes/functions.php';

// Fonctions liées à la base de données
function getVehicles($limit = null)
{
    global $db;
    $sql = "SELECT * FROM vehicules";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function getTestimonials($limit = 3)
{
    global $db;
    $sql = "SELECT ac.*, c.nom as client_nom 
            FROM avis_clients ac 
            JOIN clients c ON ac.id_client = c.id_client 
            ORDER BY ac.date_avis DESC 
            LIMIT " . (int)$limit;
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function getPopularBrands()
{
    return [
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

function getVehicleById($id)
{
    global $db;
    try {
        $sql = "SELECT * FROM vehicules WHERE id_vehicule = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $vehicle = $stmt->fetch();

        if ($vehicle) {
            // Ajout des champs manquants avec des valeurs par défaut si nécessaire
            if (!isset($vehicle['disponible_location'])) {
                $vehicle['disponible_location'] = true;
            }
            if (!isset($vehicle['tarif_location_journalier'])) {
                $vehicle['tarif_location_journalier'] = round($vehicle['prix'] * 0.002); // 0.2% du prix comme tarif journalier par défaut
            }
            if (!isset($vehicle['stock'])) {
                $vehicle['stock'] = 0;
            }

            return $vehicle;
        }

        return false;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du véhicule: " . $e->getMessage());
        return false;
    }
}

// Fonctions de gestion du panier
function getCartCount($userId = null) {
    global $db;
    if (!$userId && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    if (!$userId) return 0;
    
    try {
        $stmt = $db->prepare("SELECT SUM(quantite) as total FROM panier WHERE id_utilisateur = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ? (int)$result['total'] : 0;
    } catch (PDOException $e) {
        error_log("Erreur lors du comptage du panier: " . $e->getMessage());
        return 0;
    }
}

function addToCart($vehicleId, $userId, $type = 'achat', $quantity = 1) {
    global $db;
    try {
        // Vérifier si l'article existe déjà dans le panier
        $stmt = $db->prepare("SELECT id_panier, quantite FROM panier WHERE id_utilisateur = ? AND id_vehicule = ? AND type = ?");
        $stmt->execute([$userId, $vehicleId, $type]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Mettre à jour la quantité
            $stmt = $db->prepare("UPDATE panier SET quantite = quantite + ? WHERE id_panier = ?");
            return $stmt->execute([$quantity, $existing['id_panier']]);
        } else {
            // Ajouter un nouvel article
            $stmt = $db->prepare("INSERT INTO panier (id_utilisateur, id_vehicule, type, quantite) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$userId, $vehicleId, $type, $quantity]);
        }
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout au panier: " . $e->getMessage());
        return false;
    }
}

function removeFromCart($cartId, $userId) {
    global $db;
    try {
        $stmt = $db->prepare("DELETE FROM panier WHERE id_panier = ? AND id_utilisateur = ?");
        return $stmt->execute([$cartId, $userId]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la suppression du panier: " . $e->getMessage());
        return false;
    }
}

function updateCartQuantity($cartId, $userId, $quantity) {
    global $db;
    try {
        $stmt = $db->prepare("UPDATE panier SET quantite = ? WHERE id_panier = ? AND id_utilisateur = ?");
        return $stmt->execute([$quantity, $cartId, $userId]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de la quantité: " . $e->getMessage());
        return false;
    }
}

function clearCart($userId) {
    global $db;
    try {
        $stmt = $db->prepare("DELETE FROM panier WHERE id_utilisateur = ?");
        return $stmt->execute([$userId]);
    } catch (PDOException $e) {
        error_log("Erreur lors du vidage du panier: " . $e->getMessage());
        return false;
    }
}
