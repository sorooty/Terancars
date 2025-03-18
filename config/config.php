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
    $sql = "SELECT ac.*, u.nom as client_nom 
            FROM avis_clients ac 
            JOIN utilisateurs u ON ac.id_utilisateur = u.id_utilisateur 
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
            
            // Récupération des images du véhicule
            $vehicle['images'] = getVehicleImages($id);
            $vehicle['main_image'] = getVehicleMainImage($id);
            
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

// Fonction pour récupérer les images d'un véhicule
function getVehicleImages($vehicleId) {
    global $db;
    try {
        $sql = "SELECT * FROM images_vehicules WHERE id_vehicule = :id ORDER BY is_principale DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $vehicleId]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($images)) {
            // Retourner l'image par défaut si aucune image n'est trouvée
            return [['id_image' => 0, 'url' => asset('images/vehicules/default-car.jpg'), 'is_principale' => 1]];
        }
        
        // Convertir les URLs avec la fonction asset()
        foreach ($images as &$image) {
            $image['url'] = asset($image['url_image']);
        }
        
        return $images;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des images du véhicule: " . $e->getMessage());
        return [['id_image' => 0, 'url' => asset('images/vehicules/default-car.jpg'), 'is_principale' => 1]];
    }
}

// Fonction pour récupérer l'image principale d'un véhicule
function getVehicleMainImage($vehicleId) {
    global $db;
    try {
        // D'abord, chercher dans la table images_vehicules
        $sql = "SELECT url_image FROM images_vehicules WHERE id_vehicule = :id AND is_principale = 1 LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $vehicleId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            return asset($image['url_image']);
        }
        
        // Si aucune image principale, prendre la première image disponible
        $sql = "SELECT url_image FROM images_vehicules WHERE id_vehicule = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $vehicleId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            return asset($image['url_image']);
        }
        
        // Si aucune image dans la base, chercher dans le dossier (ancienne méthode)
        $imageFormats = ['jpg', 'png', 'jpeg', 'webp', 'gif', 'avif'];
        foreach ($imageFormats as $format) {
            $imagePath = ROOT_PATH . "/public/images/vehicules/{$vehicleId}.{$format}";
            if (file_exists($imagePath)) {
                // Ajouter cette image à la base de données pour la prochaine fois
                addVehicleImage($vehicleId, "images/vehicules/{$vehicleId}.{$format}", true);
                return asset("images/vehicules/{$vehicleId}.{$format}");
            }
        }
        
        // Si tout échoue, retourner l'image par défaut
        return asset("images/vehicules/default-car.jpg");
        
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'image principale: " . $e->getMessage());
        return asset("images/vehicules/default-car.jpg");
    }
}

// Fonction pour ajouter une image à un véhicule
function addVehicleImage($vehicleId, $imageUrl, $isPrincipal = false) {
    global $db;
    try {
        // Si l'image est principale, mettre à jour les autres images pour qu'elles ne soient plus principales
        if ($isPrincipal) {
            $sql = "UPDATE images_vehicules SET is_principale = 0 WHERE id_vehicule = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $vehicleId]);
        }
        
        // Ajouter la nouvelle image
        $sql = "INSERT INTO images_vehicules (id_vehicule, url_image, is_principale) VALUES (:id, :url, :principal)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $vehicleId, 
            'url' => $imageUrl, 
            'principal' => $isPrincipal ? 1 : 0
        ]);
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout de l'image: " . $e->getMessage());
        return false;
    }
}
