<?php
// Inclusion du fichier d'initialisation
require_once '../../../includes/init.php';

// Variables de la page
$pageTitle = "Catalogue";
$pageDescription = "Découvrez notre sélection de véhicules de qualité";
$currentPage = 'catalogue';
$additionalCss = ['css/catalogue.css'];

// Récupération du paramètre marque
$marqueFilter = isset($_GET['marque']) ? $_GET['marque'] : null;

// Construction de la requête SQL de base
$sql = "SELECT * FROM vehicules WHERE 1=1";
$params = [];

// Ajout du filtre par marque si présent
if ($marqueFilter) {
    $sql .= " AND marque = ?";
    $params[] = $marqueFilter;
}

// Modification du tri pour utiliser une colonne existante
$sql .= " ORDER BY id_vehicule DESC"; // On utilise l'ID au lieu de date_ajout

$stmt = $db->prepare($sql);
$stmt->execute($params);
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Début de la mise en mémoire tampon
ob_start();
?>

<section class="catalogue-header">
    <div class="container">
        <?php if ($marqueFilter): ?>
            <h1>Véhicules <?= htmlspecialchars($marqueFilter) ?></h1>
            <a href="<?= url('catalogue/') ?>" class="btn btn-link">
                <i class="fas fa-times"></i> 
                Retirer le filtre
            </a>
        <?php else: ?>
            <h1>Tous nos véhicules</h1>
        <?php endif; ?>
        <p>Découvrez notre sélection de véhicules disponibles à la vente et à la location</p>
    </div>
</section>

<section class="search-sort-section">
    <div class="container">
        <div class="search-sort-container">
            <div class="search-bar">
                <input type="text" placeholder="Rechercher un véhicule" id="search-input">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
            <div class="sort-buttons">
                <button class="sort-btn active" data-sort="newest">Plus récent</button>
                <button class="sort-btn" data-sort="price-asc">Prix croissant</button>
                <button class="sort-btn" data-sort="price-desc">Prix décroissant</button>
            </div>
        </div>
    </div>
</section>

<section class="catalogue-content">
    <div class="container">
        <?php if (empty($vehicules) && $marqueFilter): ?>
            <div class="no-results">
                <p>Aucun véhicule de la marque <?= htmlspecialchars($marqueFilter) ?> n'est disponible pour le moment.</p>
                <a href="<?= url('catalogue/') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> 
                    Voir tous les véhicules
                </a>
            </div>
        <?php else: ?>
            <div class="catalogue-grid">
                <?php foreach ($vehicules as $vehicle): ?>
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
        <?php endif; ?>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 