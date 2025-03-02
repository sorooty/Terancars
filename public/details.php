<?php
/**
 * Page de détails d'un véhicule
 * Affiche les informations détaillées d'un véhicule spécifique
 */

// Définition du titre de la page (sera complété avec le nom du véhicule)
$pageTitle = "Détails du véhicule";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Vérifier si un ID est passé en paramètre dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='error-message'>Véhicule introuvable. <a href='index.php'>Retour à l'accueil</a></div>";
    include '../includes/footer.php';
    exit();
}

$id = intval($_GET['id']); // Sécurisation de l'ID

// Récupérer les infos du véhicule depuis la base
$query = "SELECT * FROM vehicules WHERE id_vehicule = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vehicule = $result->fetch_assoc();

if (!$vehicule) {
    echo "<div class='error-message'>Véhicule introuvable. <a href='index.php'>Retour à l'accueil</a></div>";
    include '../includes/footer.php';
    exit();
}

// Mettre à jour le titre de la page avec le nom du véhicule
$pageTitle = $vehicule['marque'] . " " . $vehicule['modele'];

// Récupérer les images du véhicule (simulation - à implémenter avec une table d'images)
$images = [];
for ($i = 1; $i <= 5; $i++) {
    $imagePath = "/DaCar/assets/images/vehicles/" . $vehicule['id_vehicule'] . "_" . $i . ".jpg";
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
        $images[] = $imagePath;
    }
}

// Si aucune image n'est trouvée, utiliser une image par défaut
if (empty($images)) {
    $images[] = "/DaCar/assets/images/no-image.jpg";
}
?>

<div class="breadcrumb">
    <a href="index.php">Accueil</a> &gt; 
    <a href="catalogue.php">Catalogue</a> &gt; 
    <span><?php echo $vehicule['marque'] . " " . $vehicule['modele']; ?></span>
</div>

<section class="vehicle-details">
    <div class="vehicle-header">
        <h1><?php echo $vehicule['marque'] . " " . $vehicule['modele']; ?></h1>
        <?php if (isset($vehicule['annee'])) { ?>
            <span class="vehicle-year"><?php echo $vehicule['annee']; ?></span>
        <?php } ?>
    </div>

    <div class="vehicle-content">
        <!-- Galerie d'images -->
        <div class="vehicle-gallery">
            <div class="main-image">
                <img src="<?php echo $images[0]; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>" id="main-vehicle-image">
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
                    <span class="price-value"><?php echo number_format($vehicule['prix'], 2); ?> €</span>
                </div>
                
                <?php if ($vehicule['disponible_location']) { ?>
                    <div class="rental-price">
                        <span class="rental-label">Location :</span>
                        <span class="rental-value"><?php echo number_format($vehicule['tarif_location_journalier'], 2); ?> €/jour</span>
                    </div>
                <?php } ?>
            </div>

            <div class="vehicle-actions">
                <a href="panier.php?ajouter=<?php echo $vehicule['id_vehicule']; ?>&type=achat" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Ajouter au panier
                </a>
                
                <?php if ($vehicule['disponible_location']) { ?>
                    <a href="panier.php?ajouter=<?php echo $vehicule['id_vehicule']; ?>&type=location" class="btn btn-secondary">
                        <i class="fas fa-key"></i> Louer ce véhicule
                    </a>
                <?php } ?>
                
                <a href="contact.php?sujet=vehicule&id=<?php echo $vehicule['id_vehicule']; ?>" class="btn btn-outline">
                    <i class="fas fa-question-circle"></i> Demander plus d'infos
                </a>
            </div>

            <div class="vehicle-specs">
                <h3>Caractéristiques</h3>
                <ul class="specs-list">
                    <li><i class="fas fa-road"></i> <strong>Kilométrage :</strong> <?php echo number_format($vehicule['kilometrage']); ?> km</li>
                    <li><i class="fas fa-gas-pump"></i> <strong>Carburant :</strong> <?php echo ucfirst($vehicule['carburant']); ?></li>
                    <li><i class="fas fa-cog"></i> <strong>Transmission :</strong> <?php echo ucfirst($vehicule['transmission']); ?></li>
                    <?php if (isset($vehicule['puissance'])) { ?>
                        <li><i class="fas fa-tachometer-alt"></i> <strong>Puissance :</strong> <?php echo $vehicule['puissance']; ?> ch</li>
                    <?php } ?>
                    <?php if (isset($vehicule['couleur'])) { ?>
                        <li><i class="fas fa-palette"></i> <strong>Couleur :</strong> <?php echo ucfirst($vehicule['couleur']); ?></li>
                    <?php } ?>
                    <?php if (isset($vehicule['nb_portes'])) { ?>
                        <li><i class="fas fa-door-open"></i> <strong>Nombre de portes :</strong> <?php echo $vehicule['nb_portes']; ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

    <?php if (isset($vehicule['description']) && !empty($vehicule['description'])) { ?>
        <div class="vehicle-description">
            <h3>Description</h3>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($vehicule['description'])); ?>
            </div>
        </div>
    <?php } ?>
</section>

<!-- Section des véhicules similaires (à implémenter) -->
<section class="similar-vehicles">
    <h2>Véhicules similaires</h2>
    <p>Ces véhicules pourraient aussi vous intéresser</p>
    
    <div class="similar-vehicles-container">
        <!-- Ici, vous pouvez ajouter une requête pour afficher des véhicules similaires -->
        <p class="coming-soon">Fonctionnalité à venir prochainement...</p>
    </div>
</section>

<!-- Script pour la galerie d'images -->
<script>
function changeMainImage(imageSrc) {
    document.getElementById('main-vehicle-image').src = imageSrc;
}
</script>

<?php include '../includes/footer.php'; ?>
