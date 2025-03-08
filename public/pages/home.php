<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = "TeranCar - Vente et Location de Véhicules";
$pageDescription = "Découvrez notre sélection de véhicules de qualité pour la vente et la location. Des voitures pour tous les besoins et tous les budgets.";
$currentPage = 'home';

// Début de la mise en mémoire tampon
ob_start();
?>

<!-- Section Hero -->
<section class="hero">
    <div class="hero-content">
        <h1>Bienvenue chez TeranCar</h1>
        <p class="hero-subtitle">Votre partenaire de confiance pour l'achat et la location de véhicules</p>
        <div class="hero-buttons">
            <a href="<?= url('catalogue/') ?>" class="btn btn-primary">
                <i class="fas fa-car"></i>
                Voir nos véhicules
            </a>
            <a href="<?= url('contact/') ?>" class="btn btn-secondary">
                <i class="fas fa-envelope"></i>
                Nous contacter
            </a>
        </div>
    </div>
</section>

<!-- Section Marques Populaires -->
<section class="popular-brands">
    <div class="container">
        <h2 class="section-title">Nos marques populaires</h2>
        <div class="brands-grid">
            <?php
            // Récupération des marques distinctes depuis la table vehicules
            $query = "SELECT DISTINCT marque FROM vehicules ORDER BY marque";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Tableau associatif des logos de marques
            $logosPaths = [
                'Toyota' => 'toyota.png',
                'BMW' => 'bmw.png',
                'Mercedes' => 'mercedes.png',
                'Audi' => 'audi.png',
                'Tesla' => 'tesla.png',
                'Volkswagen' => 'volkswagen.png',
                'Ford' => 'ford.png',
                'Peugeot' => 'peugeot.png',
                'Citroën' => 'citroen.png',
                'Hyundai' => 'hyundai.png',
                'Renault' => 'renault.png'
            ];

            foreach ($marques as $marque) {
                $logoPath = isset($logosPaths[$marque]) 
                    ? asset('images/brands/' . $logosPaths[$marque])
                    : asset('images/brands/default-brand.png');
                ?>
                <a href="<?= url('catalogue/?marque=' . urlencode($marque)) ?>" class="brand-logo">
                    <img src="<?= $logoPath ?>" 
                         alt="Logo <?= htmlspecialchars($marque) ?>" 
                         title="Voir les véhicules <?= htmlspecialchars($marque) ?>"
                         onerror="this.src='<?= asset('images/brands/default-brand.png') ?>'">
                </a>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 