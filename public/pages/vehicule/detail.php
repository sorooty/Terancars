<?php
// Inclusion du fichier d'initialisation
require_once '../../../includes/init.php';

// Récupération de l'ID du véhicule depuis l'URL
$vehicleId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Traitement de l'ajout au panier
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $type = isset($_POST['type']) ? $_POST['type'] : 'achat';
    if (addToCart($vehicleId, $type)) {
        $_SESSION['success_message'] = 'Le véhicule a été ajouté au panier avec succès.';
    } else {
        $_SESSION['error_message'] = 'Une erreur est survenue lors de l\'ajout au panier.';
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Récupération des données du véhicule
$vehicle = getVehicleById($vehicleId);

// Si le véhicule n'existe pas, redirection vers la page 404
if (!$vehicle) {
    header('Location: ' . url('pages/errors/404.php'));
    exit;
}

// Variables de la page
$pageTitle = $vehicle['marque'] . ' ' . $vehicle['modele'];
$pageDescription = "Découvrez les caractéristiques détaillées de la " . $vehicle['marque'] . ' ' . $vehicle['modele'] . ' ' . $vehicle['annee'];
$currentPage = 'vehicule';
$additionalCss = ['css/vehicule.css'];
$additionalJs = ['js/vehicule.js'];

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <!-- Messages de notification -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message'] ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Bouton retour -->
    <div class="back-btn-container">
        <a href="<?= url('catalogue/') ?>" class="btn btn-link">
            <i class="fas fa-chevron-left"></i>
            Retour au catalogue
        </a>
    </div>

    <div class="car-details">
        <div class="car-image-container">
            <button class="favorite-btn" id="favoriteBtn">
                <i class="fas fa-heart"></i>
            </button>

            <div class="image-gallery">
                <div class="gallery-slider">
                    <?php foreach ($vehicle['images'] as $image): ?>
                    <img src="<?= asset('images/vehicules/' . $image) ?>" 
                         alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>" 
                         class="gallery-img"
                         onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                    <?php endforeach; ?>
                </div>
                
                <button class="gallery-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <button class="gallery-next">
                    <i class="fas fa-chevron-right"></i>
                </button>

                <div class="gallery-nav">
                    <?php foreach ($vehicle['images'] as $index => $image): ?>
                    <div class="gallery-dot <?= $index === 0 ? 'active' : '' ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="car-info">
            <div class="tag <?= $vehicle['disponible'] ? 'available' : 'unavailable' ?>">
                <?= $vehicle['disponible'] ? 'Disponible' : 'Indisponible' ?>
            </div>
            
            <h1 class="price"><?= formatPrice($vehicle['prix']) ?></h1>
            
            <?php if ($vehicle['prix_location']): ?>
            <div class="rental-price">
                Location: <?= formatPrice($vehicle['prix_location']) ?> / jour
            </div>
            <?php endif; ?>

            <div class="description">
                <?= nl2br(htmlspecialchars($vehicle['description'])) ?>
            </div>

            <div class="dropdowns">
                <div class="dropdown-container">
                    <div class="label">Marque</div>
                    <div class="value"><?= htmlspecialchars($vehicle['marque']) ?></div>
                </div>

                <div class="dropdown-container">
                    <div class="label">Modèle</div>
                    <div class="value"><?= htmlspecialchars($vehicle['modele']) ?></div>
                </div>
            </div>

            <div class="button-group">
                <?php if ($vehicle['disponible']): ?>
                <form method="post" class="cart-form">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="type" value="achat">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Ajouter à l'achat
                    </button>
                </form>
                <?php endif; ?>
                
                <?php if ($vehicle['disponible_location']): ?>
                <form method="post" class="cart-form">
                    <input type="hidden" name="action" value="add_to_cart">
                    <input type="hidden" name="type" value="location">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-key"></i>
                        Ajouter à la location
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="characteristics">
        <h2>Caractéristiques</h2>
        <div class="characteristics-grid">
            <div class="characteristic-item">
                <div class="characteristic-label">Année</div>
                <div class="characteristic-value"><?= $vehicle['annee'] ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Kilométrage</div>
                <div class="characteristic-value"><?= number_format($vehicle['kilometrage'], 0, ',', ' ') ?> km</div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Carburant</div>
                <div class="characteristic-value"><?= htmlspecialchars(ucfirst($vehicle['carburant'])) ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Transmission</div>
                <div class="characteristic-value"><?= htmlspecialchars(ucfirst($vehicle['transmission'])) ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Puissance</div>
                <div class="characteristic-value"><?= $vehicle['puissance'] ? $vehicle['puissance'] . ' ch' : 'Non spécifiée' ?></div>
            </div>

            <div class="characteristic-item">
                <div class="characteristic-label">Couleur</div>
                <div class="characteristic-value"><?= htmlspecialchars(ucfirst($vehicle['couleur'])) ?></div>
            </div>
        </div>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 