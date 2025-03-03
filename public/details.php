<?php
/**
 * Page de détails d'un véhicule
 * Affiche les informations détaillées d'un véhicule spécifique
 */

// Définition du titre de la page (sera complété avec le nom du véhicule)
$pageTitle = "Détails du véhicule";

// Inclusion des fichiers nécessaires
require_once '../config/config.php';

// Vérifier si un ID est passé en paramètre dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    include '../includes/header.php';
    echo "<div class='container'><div class='alert alert-danger'>Véhicule introuvable. <a href='index.php'>Retour à l'accueil</a></div></div>";
    include '../includes/footer.php';
    exit();
}

$id = intval($_GET['id']); // Sécurisation de l'ID

// Vérifier si la connexion à la base de données est établie
if (!isset($conn) || $conn->connect_error) {
    include '../includes/header.php';
    echo "<div class='container'><div class='alert alert-danger'>Erreur de connexion à la base de données. Veuillez réessayer plus tard.</div></div>";
    include '../includes/footer.php';
    exit();
}

// Récupérer les infos du véhicule depuis la base
$query = "SELECT * FROM vehicules WHERE id_vehicule = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    include '../includes/header.php';
    echo "<div class='container'><div class='alert alert-danger'>Erreur de préparation de la requête : " . $conn->error . "</div></div>";
    include '../includes/footer.php';
    exit();
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$vehicule = $result->fetch_assoc();

if (!$vehicule) {
    include '../includes/header.php';
    echo "<div class='container'><div class='alert alert-danger'>Véhicule introuvable. <a href='index.php'>Retour à l'accueil</a></div></div>";
    include '../includes/footer.php';
    exit();
}

// Mettre à jour le titre de la page avec le nom du véhicule
$pageTitle = $vehicule['marque'] . " " . $vehicule['modele'];

// Inclure l'en-tête après avoir défini le titre de la page
include '../includes/header.php';

// Récupérer les images du véhicule
$images = [];
$image_path = '../assets/images/default-car.jpg'; // Image par défaut

// Vérifier si la table images_vehicules existe
$images_table_exists = false;
$check_images_table = $conn->query("SHOW TABLES LIKE 'images_vehicules'");
$images_table_exists = ($check_images_table && $check_images_table->num_rows > 0);

if ($images_table_exists) {
    $image_query = "SELECT chemin_image FROM images_vehicules WHERE id_vehicule = ?";
    $image_stmt = $conn->prepare($image_query);
    
    if ($image_stmt) {
        $image_stmt->bind_param("i", $id);
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();
        
        if ($image_result && $image_result->num_rows > 0) {
            while ($image = $image_result->fetch_assoc()) {
                $images[] = $image['chemin_image'];
            }
        }
        $image_stmt->close();
    }
}

// Si aucune image n'est trouvée, utiliser l'image par défaut
if (empty($images)) {
    $images[] = $image_path;
}
?>

<div class="container">
    <div class="breadcrumb">
        <a href="index.php">Accueil</a> &gt; 
        <a href="catalogue.php">Catalogue</a> &gt; 
        <span><?php echo htmlspecialchars($vehicule['marque'] . " " . $vehicule['modele']); ?></span>
    </div>

    <section class="vehicle-details">
        <div class="vehicle-header">
            <h1><?php echo htmlspecialchars($vehicule['marque'] . " " . $vehicule['modele']); ?></h1>
            <?php if (isset($vehicule['annee'])) { ?>
                <span class="vehicle-year"><?php echo htmlspecialchars($vehicule['annee']); ?></span>
            <?php } ?>
        </div>

        <div class="vehicle-content">
            <!-- Galerie d'images -->
            <div class="vehicle-gallery">
                <div class="main-image">
                    <img src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']); ?>" id="main-vehicle-image">
                </div>
                
                <?php if (count($images) > 1) { ?>
                    <div class="thumbnail-images">
                        <?php foreach ($images as $index => $image) { ?>
                            <div class="thumbnail" onclick="changeMainImage('<?php echo htmlspecialchars($image); ?>')">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Vue <?php echo $index + 1; ?>">
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
                        <span class="price-value"><?php echo number_format(isset($vehicule['prix_achat']) ? $vehicule['prix_achat'] : $vehicule['prix'], 2, ',', ' '); ?> €</span>
                    </div>
                    
                    <?php if (isset($vehicule['disponible_location']) && $vehicule['disponible_location']) { ?>
                        <div class="rental-price">
                            <span class="rental-label">Location :</span>
                            <span class="rental-value"><?php echo number_format(isset($vehicule['prix_location']) ? $vehicule['prix_location'] : 0, 2, ',', ' '); ?> €/jour</span>
                        </div>
                    <?php } ?>
                </div>

                <div class="vehicle-actions">
                    <a href="panier.php?action=ajouter&id=<?php echo $vehicule['id_vehicule']; ?>&type=achat" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                    </a>
                    
                    <?php if (isset($vehicule['disponible_location']) && $vehicule['disponible_location']) { ?>
                        <a href="panier.php?action=ajouter&id=<?php echo $vehicule['id_vehicule']; ?>&type=location" class="btn btn-secondary">
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
                        <?php if (isset($vehicule['kilometrage'])) { ?>
                            <li><i class="fas fa-road"></i> <strong>Kilométrage :</strong> <?php echo number_format($vehicule['kilometrage'], 0, ',', ' '); ?> km</li>
                        <?php } ?>
                        <?php if (isset($vehicule['carburant'])) { ?>
                            <li><i class="fas fa-gas-pump"></i> <strong>Carburant :</strong> <?php echo ucfirst(htmlspecialchars($vehicule['carburant'])); ?></li>
                        <?php } ?>
                        <?php if (isset($vehicule['transmission'])) { ?>
                            <li><i class="fas fa-cog"></i> <strong>Transmission :</strong> <?php echo ucfirst(htmlspecialchars($vehicule['transmission'])); ?></li>
                        <?php } ?>
                        <?php if (isset($vehicule['puissance'])) { ?>
                            <li><i class="fas fa-tachometer-alt"></i> <strong>Puissance :</strong> <?php echo htmlspecialchars($vehicule['puissance']); ?> ch</li>
                        <?php } ?>
                        <?php if (isset($vehicule['couleur'])) { ?>
                            <li><i class="fas fa-palette"></i> <strong>Couleur :</strong> <?php echo ucfirst(htmlspecialchars($vehicule['couleur'])); ?></li>
                        <?php } ?>
                        <?php if (isset($vehicule['nb_portes'])) { ?>
                            <li><i class="fas fa-door-open"></i> <strong>Nombre de portes :</strong> <?php echo htmlspecialchars($vehicule['nb_portes']); ?></li>
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
</div>

<!-- Script pour la galerie d'images -->
<script>
function changeMainImage(imageSrc) {
    document.getElementById('main-vehicle-image').src = imageSrc;
}
</script>

<?php include '../includes/footer.php'; ?>
