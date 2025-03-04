<?php
// Définition du chemin racine
define('ROOT_PATH', __DIR__);

// Configuration
require_once ROOT_PATH . '/config/config.php';

// Récupération des données
$vehicles = getVehicles(5); // 5 véhicules pour le slider
$popularBrands = getPopularBrands();
$testimonials = getTestimonials(3);

// Variables de la page
$pageTitle = "Accueil";
$pageDescription = "Teran'Cars - Votre partenaire de confiance pour l'achat et la location de véhicules de qualité";
$currentPage = 'home';

// Début de la mise en mémoire tampon
ob_start();
?>

<!-- Section Hero -->
<section class="hero">
    <div class="hero-content container">
        <h1>Bienvenue chez Teran'Cars</h1>
        <p class="hero-subtitle">Votre partenaire de confiance pour l'achat et la location de véhicules de qualité</p>
        <div class="hero-buttons">
            <a href="<?= url('pages/catalogue/') ?>" class="btn btn-primary">Voir notre catalogue</a>
            <a href="<?= url('pages/contact/') ?>" class="btn btn-outline">Nous contacter</a>
        </div>
    </div>
</section>

<!-- Section Offres du Moment -->
<section class="current-offers">
    <div class="container">
        <h2 class="section-title">Offre du moment:</h2>
        <div class="offers-slider">
            <button class="slider-arrow prev">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="offers-wrapper">
                <?php foreach ($vehicles as $index => $vehicle): ?>
                <div class="offer-card" style="--order: <?= $index ?>">
                    <?php
                    // Déterminer le nom du fichier d'image en fonction de la marque et du modèle
                    $imageFilename = '';
                    $marqueModele = strtolower($vehicle['marque'] . ' ' . $vehicle['modele']);
                    
                    switch(true) {
                        case strpos($marqueModele, 'toyota corolla') !== false:
                            $imageFilename = 'corolla.jpeg';
                            break;
                        // ... existing code ...
                        default:
                            $imageFilename = 'audi.jpeg';
                    }
                    ?>
                    <img src="<?= asset('images/vehicules/' . $imageFilename) ?>" alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>" loading="lazy">
                    <div class="offer-details">
                        <h3><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                        <p class="offer-year">Année : <?= htmlspecialchars($vehicle['annee']) ?></p>
                        <p class="offer-price">Prix : <?= formatPrice($vehicle['prix']) ?></p>
                        <div class="offer-specs">
                            <span><i class="fas fa-gas-pump"></i> <?= htmlspecialchars($vehicle['carburant']) ?></span>
                            <span><i class="fas fa-cog"></i> <?= htmlspecialchars($vehicle['transmission']) ?></span>
                        </div>
                        <a href="<?= url('pages/vehicule/' . $vehicle['id_vehicule']) ?>" class="btn btn-primary">Voir détails</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <button class="slider-arrow next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- Section Marques Populaires -->
<section class="popular-brands">
    <div class="container">
        <h2 class="section-title">Les marques populaires:</h2>
        <div class="brands-grid">
            <?php foreach ($popularBrands as $index => $brand): ?>
            <div class="brand-logo" style="--order: <?= $index ?>">
                <img src="<?= asset('images/brands/' . strtolower($brand) . '.png') ?>" alt="<?= htmlspecialchars($brand) ?>">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section Témoignages -->
<section class="testimonials">
    <div class="container">
        <h2 class="section-title">Ce que disent nos clients</h2>
        
        <div class="testimonials-container">
            <button class="slider-arrow prev">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="testimonials-wrapper">
                <?php if (empty($testimonials)): ?>
                <!-- Témoignages statiques si pas de données en base -->
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"Service exceptionnel ! J'ai trouvé la voiture de mes rêves à un prix très compétitif. L'équipe est professionnelle et à l'écoute."</p>
                    <div class="testimonial-author">
                        <img src="<?= asset('images/testimonials/user1.jpg') ?>" alt="Sophie Martin">
                        <div class="author-info">
                            <h4>Sophie Martin</h4>
                            <span>Cliente depuis 2023</span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <?php for ($i = 0; $i < $testimonial['note']; $i++): ?>
                            <i class="fas fa-star"></i>
                            <?php endfor; ?>
                            <?php if ($testimonial['note'] < 5): ?>
                            <i class="fas fa-star-half-alt"></i>
                            <?php endif; ?>
                        </div>
                        <p class="testimonial-text">"<?= htmlspecialchars($testimonial['commentaire']) ?>"</p>
                        <div class="testimonial-author">
                            <img src="<?= asset('images/testimonials/user' . rand(1, 3) . '.jpg') ?>" alt="<?= htmlspecialchars($testimonial['client_nom']) ?>">
                            <div class="author-info">
                                <h4><?= htmlspecialchars($testimonial['client_nom']) ?></h4>
                                <span>Client<?= substr($testimonial['client_nom'], -1) === 'e' ? 'e' : '' ?> depuis <?= date('Y', strtotime($testimonial['date_avis'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button class="slider-arrow next">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 