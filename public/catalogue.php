<?php
/**
 * Page de catalogue
 * Affiche tous les véhicules disponibles avec options de filtrage
 * Adapté à la structure de base de données optimisée
 */

// Définition du titre de la page
$pageTitle = "Catalogue";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Initialisation des filtres
$where = "1=1"; // Condition toujours vraie pour commencer
$params = [];
$types = "";

// Traitement des filtres si soumis
if (isset($_GET['filter'])) {
    // Filtre par marque
    if (!empty($_GET['marque'])) {
        $where .= " AND marque = ?";
        $params[] = $_GET['marque'];
        $types .= "s";
    }
    
    // Filtre par carburant
    if (!empty($_GET['carburant'])) {
        $where .= " AND carburant = ?";
        $params[] = $_GET['carburant'];
        $types .= "s";
    }
    
    // Filtre par transmission
    if (!empty($_GET['transmission'])) {
        $where .= " AND transmission = ?";
        $params[] = $_GET['transmission'];
        $types .= "s";
    }
    
    // Filtre par prix minimum
    if (!empty($_GET['prix_min'])) {
        $where .= " AND prix >= ?";
        $params[] = $_GET['prix_min'];
        $types .= "d";
    }
    
    // Filtre par prix maximum
    if (!empty($_GET['prix_max'])) {
        $where .= " AND prix <= ?";
        $params[] = $_GET['prix_max'];
        $types .= "d";
    }
    
    // Filtre par disponibilité en location
    if (isset($_GET['disponible_location']) && $_GET['disponible_location'] == 1) {
        $where .= " AND disponible_location = 1";
    }
    
    // Filtre par année (ajouté selon la structure de la base de données)
    if (!empty($_GET['annee_min'])) {
        $where .= " AND annee >= ?";
        $params[] = $_GET['annee_min'];
        $types .= "i";
    }
    
    if (!empty($_GET['annee_max'])) {
        $where .= " AND annee <= ?";
        $params[] = $_GET['annee_max'];
        $types .= "i";
    }
    
    // Filtre par stock disponible
    if (isset($_GET['en_stock']) && $_GET['en_stock'] == 1) {
        $where .= " AND stock > 0";
    }
}

// Récupération des marques pour le filtre
$queryMarques = "SELECT DISTINCT marque FROM vehicules ORDER BY marque";
$resultMarques = $conn->query($queryMarques);

// Récupération des années pour le filtre
$queryAnnees = "SELECT MIN(annee) as min_annee, MAX(annee) as max_annee FROM vehicules WHERE annee IS NOT NULL";
$resultAnnees = $conn->query($queryAnnees);
$annees = $resultAnnees->fetch_assoc();

// Récupération des véhicules avec filtres
$query = "SELECT * FROM vehicules WHERE $where ORDER BY date_ajout DESC";

// Si la colonne date_ajout n'existe pas, utiliser id_vehicule comme ordre par défaut
if (!$conn->query("SHOW COLUMNS FROM vehicules LIKE 'date_ajout'")->num_rows) {
    $query = "SELECT * FROM vehicules WHERE $where ORDER BY id_vehicule DESC";
}

$stmt = $conn->prepare($query);

// Bind des paramètres si nécessaire
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="catalogue-container">
    <div class="catalogue-header">
        <h1>Catalogue de Véhicules</h1>
        <p>Découvrez notre sélection de véhicules disponibles à l'achat et à la location</p>
    </div>
    
    <div class="catalogue-content">
        <!-- Filtres -->
        <div class="filtres">
            <h2>Filtres</h2>
            <form action="catalogue.php" method="GET" class="filtre-form">
                <div class="filtre-group">
                    <label for="marque">Marque</label>
                    <select name="marque" id="marque">
                        <option value="">Toutes les marques</option>
                        <?php while ($marque = $resultMarques->fetch_assoc()) { ?>
                            <option value="<?php echo $marque['marque']; ?>" <?php echo (isset($_GET['marque']) && $_GET['marque'] == $marque['marque']) ? 'selected' : ''; ?>>
                                <?php echo $marque['marque']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="filtre-group">
                    <label for="carburant">Carburant</label>
                    <select name="carburant" id="carburant">
                        <option value="">Tous types</option>
                        <option value="essence" <?php echo (isset($_GET['carburant']) && $_GET['carburant'] == 'essence') ? 'selected' : ''; ?>>Essence</option>
                        <option value="diesel" <?php echo (isset($_GET['carburant']) && $_GET['carburant'] == 'diesel') ? 'selected' : ''; ?>>Diesel</option>
                        <option value="électrique" <?php echo (isset($_GET['carburant']) && $_GET['carburant'] == 'électrique') ? 'selected' : ''; ?>>Électrique</option>
                        <option value="hybride" <?php echo (isset($_GET['carburant']) && $_GET['carburant'] == 'hybride') ? 'selected' : ''; ?>>Hybride</option>
                    </select>
                </div>
                
                <div class="filtre-group">
                    <label for="transmission">Transmission</label>
                    <select name="transmission" id="transmission">
                        <option value="">Toutes</option>
                        <option value="manuelle" <?php echo (isset($_GET['transmission']) && $_GET['transmission'] == 'manuelle') ? 'selected' : ''; ?>>Manuelle</option>
                        <option value="automatique" <?php echo (isset($_GET['transmission']) && $_GET['transmission'] == 'automatique') ? 'selected' : ''; ?>>Automatique</option>
                    </select>
                </div>
                
                <!-- Ajout des filtres par année -->
                <?php if (isset($annees['min_annee']) && isset($annees['max_annee'])) { ?>
                <div class="filtre-group">
                    <label for="annee_min">Année minimum</label>
                    <select name="annee_min" id="annee_min">
                        <option value="">Toutes</option>
                        <?php for ($i = $annees['min_annee']; $i <= $annees['max_annee']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($_GET['annee_min']) && $_GET['annee_min'] == $i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                
                <div class="filtre-group">
                    <label for="annee_max">Année maximum</label>
                    <select name="annee_max" id="annee_max">
                        <option value="">Toutes</option>
                        <?php for ($i = $annees['min_annee']; $i <= $annees['max_annee']; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($_GET['annee_max']) && $_GET['annee_max'] == $i) ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
                
                <div class="filtre-group">
                    <label for="prix_min">Prix minimum</label>
                    <input type="number" name="prix_min" id="prix_min" min="0" step="1000" value="<?php echo isset($_GET['prix_min']) ? $_GET['prix_min'] : ''; ?>">
                </div>
                
                <div class="filtre-group">
                    <label for="prix_max">Prix maximum</label>
                    <input type="number" name="prix_max" id="prix_max" min="0" step="1000" value="<?php echo isset($_GET['prix_max']) ? $_GET['prix_max'] : ''; ?>">
                </div>
                
                <div class="filtre-group checkbox">
                    <input type="checkbox" name="disponible_location" id="disponible_location" value="1" <?php echo (isset($_GET['disponible_location']) && $_GET['disponible_location'] == 1) ? 'checked' : ''; ?>>
                    <label for="disponible_location">Disponible en location</label>
                </div>
                
                <div class="filtre-group checkbox">
                    <input type="checkbox" name="en_stock" id="en_stock" value="1" <?php echo (isset($_GET['en_stock']) && $_GET['en_stock'] == 1) ? 'checked' : ''; ?>>
                    <label for="en_stock">En stock uniquement</label>
                </div>
                
                <div class="filtre-actions">
                    <input type="hidden" name="filter" value="1">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="catalogue.php" class="btn btn-outline">Réinitialiser</a>
                </div>
            </form>
        </div>
        
        <!-- Liste des véhicules -->
        <div class="vehicules-liste">
            <?php 
            if ($result->num_rows > 0) {
                while ($vehicule = $result->fetch_assoc()) {
                    // Récupérer l'image principale du véhicule
                    $imagePath = "/DaCar/assets/images/vehicles/" . $vehicule['id_vehicule'] . "_1.jpg";
                    // Image par défaut si non disponible
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                        $imagePath = "/DaCar/assets/images/no-image.jpg";
                    }
                    
                    // Vérifier si le véhicule est en stock
                    $enStock = isset($vehicule['stock']) && $vehicule['stock'] > 0;
            ?>
                    <div class="vehicule-card">
                        <div class="vehicule-image">
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>">
                            <?php if ($vehicule['disponible_location']) { ?>
                                <span class="badge-location">Location</span>
                            <?php } ?>
                            <?php if (!$enStock) { ?>
                                <span class="badge-stock">Rupture de stock</span>
                            <?php } ?>
                        </div>
                        <div class="vehicule-info">
                            <h3><?php echo $vehicule['marque'] . " " . $vehicule['modele']; ?></h3>
                            <?php if (isset($vehicule['annee'])) { ?>
                                <span class="vehicule-annee"><?php echo $vehicule['annee']; ?></span>
                            <?php } ?>
                            
                            <div class="vehicule-specs">
                                <span><i class="fas fa-gas-pump"></i> <?php echo ucfirst($vehicule['carburant']); ?></span>
                                <span><i class="fas fa-cog"></i> <?php echo ucfirst($vehicule['transmission']); ?></span>
                                <span><i class="fas fa-road"></i> <?php echo number_format($vehicule['kilometrage']); ?> km</span>
                                <?php if (isset($vehicule['stock'])) { ?>
                                    <span><i class="fas fa-warehouse"></i> Stock: <?php echo $vehicule['stock']; ?></span>
                                <?php } ?>
                            </div>
                            
                            <div class="vehicule-prix">
                                <span class="prix-achat"><?php echo formatPrice($vehicule['prix']); ?></span>
                                <?php if ($vehicule['disponible_location']) { ?>
                                    <span class="prix-location"><?php echo formatPrice($vehicule['tarif_location_journalier']); ?>/jour</span>
                                <?php } ?>
                            </div>
                            
                            <div class="vehicule-actions">
                                <a href="vehicle-details.php?id=<?php echo $vehicule['id_vehicule']; ?>" class="btn btn-view">Voir détails</a>
                                <?php if ($enStock) { ?>
                                    <a href="panier.php?ajouter=<?php echo $vehicule['id_vehicule']; ?>&type=achat" class="btn btn-add-cart" data-add-to-cart data-vehicle-id="<?php echo $vehicule['id_vehicule']; ?>" data-type="achat">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($vehicule['disponible_location']) { ?>
                                    <a href="panier.php?ajouter=<?php echo $vehicule['id_vehicule']; ?>&type=location" class="btn btn-add-location" data-add-to-cart data-vehicle-id="<?php echo $vehicule['id_vehicule']; ?>" data-type="location">
                                        <i class="fas fa-key"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
            <?php 
                }
            } else {
                echo "<div class='no-results'>Aucun véhicule ne correspond à vos critères de recherche.</div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Ajout des styles spécifiques pour les nouveaux badges -->
<style>
.badge-stock {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #dc3545;
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.btn-add-location {
    background-color: #28a745;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-add-location:hover {
    background-color: #218838;
    transform: scale(1.1);
}
</style>

<?php include '../includes/footer.php'; ?>