<?php
/**
 * Page de détails d'un véhicule
 * Affiche toutes les informations d'un véhicule spécifique
 */

// Définition du titre de la page
$pageTitle = "Détails du véhicule";

// Inclusion des fichiers nécessaires
include '../config/config.php'; // Connexion à la DB
include '../includes/header.php'; // En-tête du site

// Vérification de l'ID du véhicule
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirection vers le catalogue si l'ID n'est pas valide
    header('Location: catalogue.php');
    exit;
}

$id_vehicule = intval($_GET['id']);

// Récupération des informations du véhicule
$sql = "SELECT * FROM vehicules WHERE id_vehicule = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_vehicule);
$stmt->execute();
$result = $stmt->get_result();

// Vérification si le véhicule existe
if ($result->num_rows === 0) {
    // Redirection vers le catalogue si le véhicule n'existe pas
    header('Location: catalogue.php');
    exit;
}

// Récupération des données du véhicule
$vehicule = $result->fetch_assoc();

// Récupération des caractéristiques techniques du véhicule
$specs = [];
$sql_specs = "SELECT * FROM caracteristiques WHERE id_vehicule = ?";
$stmt_specs = $conn->prepare($sql_specs);
if ($stmt_specs) {
    $stmt_specs->bind_param("i", $id_vehicule);
    $stmt_specs->execute();
    $result_specs = $stmt_specs->get_result();
    if ($result_specs && $result_specs->num_rows > 0) {
        $specs = $result_specs->fetch_assoc();
    }
}

// Récupération des images du véhicule (si table disponible)
$images = [];
$sql_images = "SELECT * FROM images_vehicules WHERE id_vehicule = ? ORDER BY ordre ASC";
$stmt_images = $conn->prepare($sql_images);

// Vérifier si la requête a été préparée avec succès
if ($stmt_images) {
    $stmt_images->bind_param("i", $id_vehicule);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();
    
    if ($result_images && $result_images->num_rows > 0) {
        while ($image = $result_images->fetch_assoc()) {
            $images[] = $image;
        }
    }
}

// Si aucune image n'est trouvée dans la base de données, utiliser l'image par défaut
if (empty($images)) {
    // Créer une entrée d'image par défaut
    $images[] = [
        'id_image' => 0,
        'id_vehicule' => $id_vehicule,
        'chemin' => 'default.jpg',
        'ordre' => 1
    ];
}

// Récupération des véhicules similaires
$vehicules_similaires = [];
$sql_similar = "SELECT * FROM vehicules 
                WHERE marque = ? 
                AND id_vehicule != ? 
                LIMIT 3";
$stmt_similar = $conn->prepare($sql_similar);
if ($stmt_similar) {
    $stmt_similar->bind_param("si", $vehicule['marque'], $id_vehicule);
    $stmt_similar->execute();
    $result_similar = $stmt_similar->get_result();
    
    if ($result_similar && $result_similar->num_rows > 0) {
        while ($similar = $result_similar->fetch_assoc()) {
            $vehicules_similaires[] = $similar;
        }
    }
}

// Vérifier si le prix_location existe, sinon utiliser une valeur par défaut
$prix_location = isset($vehicule['prix_location']) ? $vehicule['prix_location'] : 0;
?>

<div class="vehicle-details-container">
    <!-- En-tête avec le nom du véhicule -->
    <div class="vehicle-header">
        <div class="container">
            <h1><?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?></h1>
            <div class="vehicle-meta">
                <span class="vehicle-year"><?php echo $vehicule['annee']; ?></span>
                <span class="vehicle-id">Réf: <?php echo $vehicule['id_vehicule']; ?></span>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <!-- Galerie d'images -->
            <div class="col-md-7">
                <div class="vehicle-gallery">
                    <div class="main-image">
                        <?php 
                        $mainImage = SITE_URL . 'assets/images/voitures/' . ($images[0]['chemin'] ?? $id_vehicule . '.jpg');
                        $defaultImage = SITE_URL . 'assets/images/voitures/default.jpg';
                        ?>
                        <img id="main-vehicle-image" src="<?php echo $mainImage; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>" onerror="this.src='<?php echo $defaultImage; ?>'">
                    </div>
                    
                    <?php if (count($images) > 1): ?>
                    <div class="thumbnails">
                        <?php foreach ($images as $index => $image): ?>
                            <?php 
                            $thumbImage = SITE_URL . 'assets/images/voitures/' . ($image['chemin'] ?? $id_vehicule . '_' . ($index + 1) . '.jpg');
                            ?>
                            <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo $thumbImage; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele'] . ' - Image ' . ($index + 1); ?>" onerror="this.src='<?php echo $defaultImage; ?>'">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informations et actions -->
            <div class="col-md-5">
                <div class="vehicle-info-card">
                    <div class="vehicle-price-section">
                        <div class="vehicle-price">
                            <h2><?php echo number_format($vehicule['prix'], 0, ',', ' '); ?> €</h2>
                            <?php if (isset($vehicule['disponible_location']) && $vehicule['disponible_location']): ?>
                                <div class="rental-price">
                                    <span>Location: <?php echo number_format($prix_location, 0, ',', ' '); ?> €/jour</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="vehicle-stock">
                            <?php if ($vehicule['stock'] > 0): ?>
                                <span class="in-stock">En stock (<?php echo $vehicule['stock']; ?>)</span>
                            <?php else: ?>
                                <span class="out-of-stock">Rupture de stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="vehicle-specs-overview">
                        <div class="spec-item">
                            <i class="fas fa-gas-pump"></i>
                            <span><?php echo $vehicule['carburant']; ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-cog"></i>
                            <span><?php echo $vehicule['transmission']; ?></span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-road"></i>
                            <span><?php echo number_format($vehicule['kilometrage'], 0, ',', ' '); ?> km</span>
                        </div>
                        <div class="spec-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?php echo $vehicule['annee']; ?></span>
                        </div>
                    </div>
                    
                    <div class="vehicle-actions">
                        <?php if ($vehicule['stock'] > 0): ?>
                            <a href="panier.php?ajouter=<?php echo $vehicule['id_vehicule']; ?>&type=achat" class="btn-add-cart">
                                <i class="fas fa-shopping-cart"></i> Ajouter au panier
                            </a>
                        <?php endif; ?>
                        
                        <?php if (isset($vehicule['disponible_location']) && $vehicule['disponible_location']): ?>
                            <a href="panier.php?ajouter=<?php echo $vehicule['id_vehicule']; ?>&type=location" class="btn-add-rental">
                                <i class="fas fa-key"></i> Réserver pour location
                            </a>
                        <?php endif; ?>
                        
                        <a href="contact.php?sujet=Demande d'information - <?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>" class="btn-contact">
                            <i class="fas fa-envelope"></i> Demander plus d'informations
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Description et caractéristiques -->
        <div class="vehicle-details-tabs">
            <ul class="nav nav-tabs" id="vehicleDetailsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button" role="tab" aria-controls="specs" aria-selected="false">Caractéristiques techniques</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="equipment-tab" data-bs-toggle="tab" data-bs-target="#equipment" type="button" role="tab" aria-controls="equipment" aria-selected="false">Équipements</button>
                </li>
            </ul>
            <div class="tab-content" id="vehicleDetailsTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <div class="tab-inner-content">
                        <?php if (!empty($vehicule['description'])): ?>
                            <p><?php echo nl2br($vehicule['description']); ?></p>
                        <?php else: ?>
                            <p>Ce <?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?> de <?php echo $vehicule['annee']; ?> est disponible à l'achat chez Terancar. 
                            Avec son moteur <?php echo $vehicule['carburant']; ?> et sa transmission <?php echo $vehicule['transmission']; ?>, 
                            ce véhicule offre une expérience de conduite exceptionnelle. Contactez-nous pour plus d'informations.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                    <div class="tab-inner-content">
                        <div class="specs-grid">
                            <div class="spec-group">
                                <h3>Moteur et Performance</h3>
                                <ul>
                                    <li><strong>Carburant:</strong> <?php echo $vehicule['carburant']; ?></li>
                                    <li><strong>Transmission:</strong> <?php echo $vehicule['transmission']; ?></li>
                                    <?php if (isset($specs['puissance'])): ?>
                                        <li><strong>Puissance:</strong> <?php echo $specs['puissance']; ?> ch</li>
                                    <?php endif; ?>
                                    <?php if (isset($specs['cylindree'])): ?>
                                        <li><strong>Cylindrée:</strong> <?php echo $specs['cylindree']; ?> cm³</li>
                                    <?php endif; ?>
                                    <?php if (isset($specs['consommation'])): ?>
                                        <li><strong>Consommation:</strong> <?php echo $specs['consommation']; ?> L/100km</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            
                            <div class="spec-group">
                                <h3>Dimensions</h3>
                                <ul>
                                    <?php if (isset($specs['longueur'])): ?>
                                        <li><strong>Longueur:</strong> <?php echo $specs['longueur']; ?> mm</li>
                                    <?php endif; ?>
                                    <?php if (isset($specs['largeur'])): ?>
                                        <li><strong>Largeur:</strong> <?php echo $specs['largeur']; ?> mm</li>
                                    <?php endif; ?>
                                    <?php if (isset($specs['hauteur'])): ?>
                                        <li><strong>Hauteur:</strong> <?php echo $specs['hauteur']; ?> mm</li>
                                    <?php endif; ?>
                                    <?php if (isset($specs['poids'])): ?>
                                        <li><strong>Poids:</strong> <?php echo $specs['poids']; ?> kg</li>
                                    <?php endif; ?>
                                    <?php if (isset($specs['coffre'])): ?>
                                        <li><strong>Volume du coffre:</strong> <?php echo $specs['coffre']; ?> L</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="equipment" role="tabpanel" aria-labelledby="equipment-tab">
                    <div class="tab-inner-content">
                        <?php if (isset($specs['equipements']) && !empty($specs['equipements'])): ?>
                            <div class="equipment-list">
                                <?php 
                                $equipements = explode(',', $specs['equipements']);
                                foreach ($equipements as $equipement): 
                                ?>
                                    <div class="equipment-item">
                                        <i class="fas fa-check"></i>
                                        <span><?php echo trim($equipement); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>Les informations détaillées sur les équipements de ce véhicule ne sont pas disponibles. Veuillez nous contacter pour plus d'informations.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Véhicules similaires -->
        <?php if (!empty($vehicules_similaires)): ?>
        <div class="similar-vehicles">
            <h2>Véhicules similaires</h2>
            <div class="row">
                <?php foreach ($vehicules_similaires as $similar): ?>
                    <div class="col-md-4">
                        <div class="vehicule-card">
                            <div class="vehicule-image">
                                <?php 
                                $similarImage = SITE_URL . 'assets/images/voitures/' . $similar['id_vehicule'] . '.jpg';
                                $defaultImage = SITE_URL . 'assets/images/voitures/default.jpg';
                                ?>
                                <img src="<?php echo $similarImage; ?>" alt="<?php echo $similar['marque'] . ' ' . $similar['modele']; ?>" onerror="this.src='<?php echo $defaultImage; ?>'">
                                <?php if (isset($similar['disponible_location']) && $similar['disponible_location']): ?>
                                    <span class="badge-location">Location</span>
                                <?php endif; ?>
                            </div>
                            <div class="vehicule-details">
                                <div class="vehicule-titre">
                                    <h3><?php echo $similar['marque'] . ' ' . $similar['modele']; ?></h3>
                                    <span class="vehicule-annee"><?php echo $similar['annee']; ?></span>
                                </div>
                                
                                <div class="vehicule-prix">
                                    <div>
                                        <div class="prix-achat"><?php echo number_format($similar['prix'], 0, ',', ' '); ?> €</div>
                                    </div>
                                    
                                    <div class="vehicule-actions">
                                        <a href="vehicle-details.php?id=<?php echo $similar['id_vehicule']; ?>" class="btn-details">
                                            <i class="fas fa-eye"></i> Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 