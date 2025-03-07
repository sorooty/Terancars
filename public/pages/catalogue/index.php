<?php
// Inclusion du fichier d'initialisation
require_once '../../../includes/init.php';

// Variables de la page
$pageTitle = "Catalogue";
$pageDescription = "Découvrez notre sélection de véhicules de qualité";
$currentPage = 'catalogue';
$additionalCss = ['css/catalogue.css'];

// Récupération des véhicules
$vehicles = getVehicles();

// Début de la mise en mémoire tampon
ob_start();
?>

<section class="catalogue-header">
    <div class="container">
        <h1>Notre Catalogue</h1>
        <p>Découvrez notre sélection de véhicules disponibles à la vente et à la location</p>
    </div>
</section>

<section class="catalogue-content">
    <div class="container">
        <div class="catalogue-grid">
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="vehicle-card">
                <div class="vehicle-image">
                    <?php
                    $vehicleImage = getVehicleImage($vehicle['marque'], $vehicle['modele']);
                    $imagePath = !empty($vehicleImage) ? 
                        asset('images/vehicules/' . $vehicleImage) : 
                        asset('images/vehicules/default-car.jpg');
                    ?>
                    <img src="<?= $imagePath ?>" 
                         alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>"
                         onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'"
                         loading="lazy">
                </div>
                <div class="vehicle-details">
                    <h3><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                    <div class="vehicle-info">
                        <p class="vehicle-year"><i class="fas fa-calendar"></i> <?= htmlspecialchars($vehicle['annee']) ?></p>
                        <p class="vehicle-price"><i class="fas fa-tag"></i> <?= formatPrice($vehicle['prix']) ?></p>
                    </div>
                    <div class="vehicle-specs">
                        <span><i class="fas fa-gas-pump"></i> <?= htmlspecialchars($vehicle['carburant']) ?></span>
                        <span><i class="fas fa-cog"></i> <?= htmlspecialchars($vehicle['transmission']) ?></span>
                    </div>
                    <a href="<?= url('vehicule/detail?id=' . $vehicle['id_vehicule']) ?>" class="btn btn-primary">Voir détails</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 