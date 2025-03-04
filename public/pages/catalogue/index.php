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
                <img src="<?= asset('images/vehicules/' . $vehicle['image']) ?>" alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>">
                <div class="vehicle-details">
                    <h3><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                    <p class="vehicle-year">Année : <?= htmlspecialchars($vehicle['annee']) ?></p>
                    <p class="vehicle-price">Prix : <?= formatPrice($vehicle['prix']) ?></p>
                    <div class="vehicle-specs">
                        <span><i class="fas fa-gas-pump"></i> <?= htmlspecialchars($vehicle['carburant']) ?></span>
                        <span><i class="fas fa-cog"></i> <?= htmlspecialchars($vehicle['transmission']) ?></span>
                    </div>
                    <a href="<?= url('vehicule/' . $vehicle['id_vehicule']) ?>" class="btn btn-primary">Voir détails</a>
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