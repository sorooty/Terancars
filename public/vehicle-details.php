<?php
/**
 * Page de détails d'un véhicule
 * Affiche les informations détaillées d'un véhicule spécifique
 * Adapté à la structure de base de données optimisée
 */

// Définition du titre de la page (sera complété avec le nom du véhicule)
$pageTitle = "Détails du véhicule";

// Inclusion des fichiers nécessaires
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/image_functions.php';

// Vérifier si l'ID du véhicule est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$vehicleId = intval($_GET['id']);

// Vérifier si la table existe
if (!tableExists('vehicules')) {
    die("La table des véhicules n'existe pas.");
}

// Récupérer les détails du véhicule
$query = "SELECT * FROM vehicules WHERE id_vehicule = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$vehicle = $result->fetch_assoc();

// Récupérer les images du véhicule
$images = getVehicleImages($vehicleId);

// Mettre à jour le titre de la page avec le nom du véhicule
$pageTitle = $vehicle['marque'] . " " . $vehicle['modele'];

// Afficher les informations de débogage si demandé
if ($debugMode) {
    debugDatabase();
}
?>

<div class="breadcrumb">
    <a href="index.php">Accueil</a> &gt; 
    <a href="catalogue.php">Catalogue</a> &gt; 
    <span><?php echo $vehicle['marque'] . " " . $vehicle['modele']; ?></span>
</div>

<section class="vehicle-details">
    <div class="vehicle-header">
        <h1><?php echo $vehicle['marque'] . " " . $vehicle['modele']; ?></h1>
        <?php if (isset($vehicle['annee'])) { ?>
            <span class="vehicle-year"><?php echo $vehicle['annee']; ?></span>
        <?php } ?>
        
        <?php if (isset($vehicle['stock']) && $vehicle['stock'] <= 0) { ?>
            <span class="stock-badge out-of-stock">Rupture de stock</span>
        <?php } elseif (isset($vehicle['stock']) && $vehicle['stock'] < 5) { ?>
            <span class="stock-badge low-stock">Plus que <?php echo $vehicle['stock']; ?> en stock</span>
        <?php } elseif (isset($vehicle['stock'])) { ?>
            <span class="stock-badge in-stock">En stock</span>
        <?php } ?>
    </div>

    <div class="vehicle-content">
        <!-- Galerie d'images -->
        <div class="vehicle-gallery">
            <div class="main-image">
                <img src="<?php echo $images[0]; ?>" alt="<?php echo $vehicle['marque'] . ' ' . $vehicle['modele']; ?>" id="main-vehicle-image">
            </div>
            
            <?php if (count($images) > 1) { ?>
                <div class="thumbnail-images">
                    <?php foreach ($images as $index => $image) { ?>
                        <div class="thumbnail" onclick="changeMainImage('<?php echo $image; ?>')">
                            <img src="<?php echo $image; ?>" alt="Vue <?php echo $index + 1; ?>">
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- Informations principales -->
        <div class="vehicle-info">
            <div class="price-section">
                <div class="price">
                    <span class="price-label">Prix :</span>
                    <span class="price-value"><?php echo formatPrice($vehicle['prix']); ?></span>
                </div>
                
                <?php if (isset($vehicle['disponible_location']) && $vehicle['disponible_location']) { ?>
                    <div class="rental-price">
                        <span class="rental-label">Location :</span>
                        <span class="rental-value"><?php echo formatPrice($vehicle['tarif_location_journalier']); ?>/jour</span>
                    </div>
                <?php } ?>
            </div>

            <div class="vehicle-actions">
                <?php if (!isset($vehicle['stock']) || $vehicle['stock'] > 0) { ?>
                    <a href="panier.php?ajouter=<?php echo $vehicle['id_vehicule']; ?>&type=achat" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                    </a>
                <?php } else { ?>
                    <button class="btn btn-disabled" disabled>
                        <i class="fas fa-shopping-cart"></i> Indisponible
                    </button>
                <?php } ?>
                
                <?php if (isset($vehicle['disponible_location']) && $vehicle['disponible_location']) { ?>
                    <a href="panier.php?ajouter=<?php echo $vehicle['id_vehicule']; ?>&type=location" class="btn btn-secondary">
                        <i class="fas fa-key"></i> Louer ce véhicule
                    </a>
                <?php } ?>
                
                <a href="contact.php?sujet=vehicule&id=<?php echo $vehicle['id_vehicule']; ?>" class="btn btn-outline">
                    <i class="fas fa-question-circle"></i> Demander plus d'infos
                </a>
            </div>

            <div class="vehicle-specs">
                <h3>Caractéristiques</h3>
                <ul class="specs-list">
                    <li><i class="fas fa-road"></i> <strong>Kilométrage :</strong> <?php echo number_format($vehicle['kilometrage']); ?> km</li>
                    <li><i class="fas fa-gas-pump"></i> <strong>Carburant :</strong> <?php echo ucfirst($vehicle['carburant']); ?></li>
                    <li><i class="fas fa-cog"></i> <strong>Transmission :</strong> <?php echo ucfirst($vehicle['transmission']); ?></li>
                    <?php if (isset($vehicle['annee'])) { ?>
                        <li><i class="fas fa-calendar-alt"></i> <strong>Année :</strong> <?php echo $vehicle['annee']; ?></li>
                    <?php } ?>
                    <?php if (isset($vehicle['puissance'])) { ?>
                        <li><i class="fas fa-tachometer-alt"></i> <strong>Puissance :</strong> <?php echo $vehicle['puissance']; ?> ch</li>
                    <?php } ?>
                    <?php if (isset($vehicle['couleur'])) { ?>
                        <li><i class="fas fa-palette"></i> <strong>Couleur :</strong> <?php echo ucfirst($vehicle['couleur']); ?></li>
                    <?php } ?>
                    <?php if (isset($vehicle['nb_portes'])) { ?>
                        <li><i class="fas fa-door-open"></i> <strong>Nombre de portes :</strong> <?php echo $vehicle['nb_portes']; ?></li>
                    <?php } ?>
                    <?php if (isset($vehicle['nb_places'])) { ?>
                        <li><i class="fas fa-users"></i> <strong>Nombre de places :</strong> <?php echo $vehicle['nb_places']; ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

    <?php if (isset($vehicle['description']) && !empty($vehicle['description'])) { ?>
        <div class="vehicle-description">
            <h3>Description</h3>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($vehicle['description'])); ?>
            </div>
        </div>
    <?php } ?>
    
    <?php if (isset($vehicle['equipements']) && !empty($vehicle['equipements'])) { ?>
        <div class="vehicle-equipment">
            <h3>Équipements</h3>
            <div class="equipment-content">
                <?php 
                $equipements = explode(',', $vehicle['equipements']);
                echo '<ul class="equipment-list">';
                foreach ($equipements as $equipement) {
                    echo '<li><i class="fas fa-check"></i> ' . trim($equipement) . '</li>';
                }
                echo '</ul>';
                ?>
            </div>
        </div>
    <?php } ?>
</section>

<!-- Section des véhicules similaires -->
<section class="similar-vehicles">
    <h2>Véhicules similaires</h2>
    <p>Ces véhicules pourraient aussi vous intéresser</p>
    
    <div class="similar-vehicles-container">
        <?php
        // Récupérer des véhicules similaires (même marque ou même type)
        $query = "SELECT * FROM vehicules 
                 WHERE id_vehicule != ? 
                 AND (marque = ? OR carburant = ?) 
                 ORDER BY RAND() 
                 LIMIT 3";
        $stmt = $conn->prepare($query);
        
        if ($stmt !== false) {
            $stmt->bind_param("iss", $vehicleId, $vehicle['marque'], $vehicle['carburant']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo '<div class="similar-vehicles-grid">';
                while ($similar = $result->fetch_assoc()) {
                    // Récupérer l'image principale du véhicule
                    $imagePath = "/DaCar/assets/images/vehicles/" . $similar['id_vehicule'] . "_1.jpg";
                    // Image par défaut si non disponible
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                        $imagePath = "/DaCar/assets/images/no-image.jpg";
                    }
                    ?>
                    <div class="similar-vehicle-card">
                        <div class="similar-vehicle-image">
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo $similar['marque'] . ' ' . $similar['modele']; ?>">
                        </div>
                        <div class="similar-vehicle-info">
                            <h3><?php echo $similar['marque'] . " " . $similar['modele']; ?></h3>
                            <div class="similar-vehicle-price"><?php echo formatPrice($similar['prix']); ?></div>
                            <a href="vehicle-details.php?id=<?php echo $similar['id_vehicule']; ?>" class="btn btn-outline btn-sm">Voir détails</a>
                        </div>
                    </div>
                    <?php
                }
                echo '</div>';
            } else {
                echo '<p class="no-similar">Aucun véhicule similaire trouvé.</p>';
            }
        } else {
            echo '<p class="no-similar">Impossible de charger les véhicules similaires.</p>';
        }
        ?>
    </div>
</section>

<!-- Styles spécifiques à la page de détails -->
<style>
.stock-badge {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-left: 1rem;
}

.out-of-stock {
    background-color: #f8d7da;
    color: #721c24;
}

.low-stock {
    background-color: #fff3cd;
    color: #856404;
}

.in-stock {
    background-color: #d4edda;
    color: #155724;
}

.btn-disabled {
    background-color: #6c757d;
    color: #fff;
    cursor: not-allowed;
    opacity: 0.65;
}

.vehicle-equipment {
    margin-top: 2rem;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.vehicle-equipment h3 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 1rem;
}

.equipment-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 0.8rem;
    list-style-type: none;
    padding: 0;
}

.equipment-list li {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.equipment-list li i {
    color: #28a745;
    margin-right: 0.5rem;
}

.similar-vehicles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.similar-vehicle-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.similar-vehicle-card:hover {
    transform: translateY(-5px);
}

.similar-vehicle-image {
    height: 180px;
    overflow: hidden;
}

.similar-vehicle-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.similar-vehicle-card:hover .similar-vehicle-image img {
    transform: scale(1.05);
}

.similar-vehicle-info {
    padding: 1rem;
}

.similar-vehicle-info h3 {
    font-size: 1.1rem;
    margin: 0 0 0.5rem 0;
    color: #333;
}

.similar-vehicle-price {
    font-weight: 600;
    color: #007bff;
    margin-bottom: 0.8rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.no-similar {
    text-align: center;
    color: #6c757d;
    margin-top: 1.5rem;
}

@media (max-width: 768px) {
    .similar-vehicles-grid {
        grid-template-columns: 1fr;
    }
    
    .equipment-list {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../includes/footer.php'; ?> 