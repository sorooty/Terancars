<?php
// Inclusion du fichier d'initialisation
require_once __DIR__ . '/includes/init.php';

// Récupération des données
$vehicles = getVehicles(5); // 5 véhicules pour le slider
$popularBrands = getPopularBrands();
$testimonials = getTestimonials(3);

// Variables de la page
$pageTitle = "Accueil";
$pageDescription = "TeranCar - Votre partenaire de confiance pour le transport et la mobilité à Dakar, Sénégal";
$currentPage = 'home';

// Fonction pour trouver le bon format d’image du véhicule
function getVehicleImagePath($vehicleId)
{
    $imageFormats = ['jpg', 'png', 'jpeg', 'webp', 'gif', 'avif'];
    foreach ($imageFormats as $format) {
        $imagePath = ROOT_PATH . "/public/images/vehicules/{$vehicleId}.{$format}";
        if (file_exists($imagePath)) {
            return asset("images/vehicules/{$vehicleId}.{$format}");
        }
    }
    return asset("images/vehicules/default.jpg"); // Image par défaut si aucune trouvée
}

// Début de la mise en mémoire tampon
ob_start();
?>

<!-- Section Hero -->
<section class="hero">
    <div class="hero-content container">
        <h1>Bienvenue chez TeranCar</h1>
        <p class="hero-subtitle">Votre partenaire de confiance pour des solutions de transport fiables et efficaces à Dakar</p>
        <div class="hero-buttons">
            <a href="<?= url('catalogue') ?>" class="btn btn-primary">
                <i class="fas fa-car"></i> Voir notre catalogue
            </a>
            <a href="<?= url('contact') ?>" class="btn btn-secondary">
                <i class="fas fa-envelope"></i> Nous contacter
            </a>
        </div>
    </div>
</section>

<!-- Section Offres du Moment -->
<section class="current-offers">
    <div class="container">
        <h2 class="section-title">Offres du moment</h2>
        <div class="offers-slider">
            <button class="slider-arrow prev">
                <i class="fas fa-chevron-left"></i>
            </button>

            <div class="offers-wrapper">
                <?php foreach ($vehicles as $vehicle): ?>
                    <?php $imageUrl = getVehicleImagePath($vehicle['id_vehicule']); ?>
                    <div class="offer-card">
                        <div class="offer-image">
                            <img src="<?= $imageUrl ?>"
                                alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>"
                                loading="lazy">
                        </div>
                        <div class="offer-details">
                            <h3><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                            <div class="offer-info">
                                <p class="offer-price">
                                    <i class="fas fa-tag"></i> <?= number_format($vehicle['prix'], 2, ',', ' ') . ' €' ?>
                                </p>
                            </div>
                            <div class="offer-specs">
                                <span><i class="fas fa-gas-pump"></i> <?= htmlspecialchars($vehicle['carburant']) ?></span>
                                <span><i class="fas fa-cog"></i> <?= htmlspecialchars($vehicle['transmission']) ?></span>
                            </div>
                            <a href="<?= url('vehicule/detail?id=' . $vehicle['id_vehicule']) ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Voir détails
                            </a>
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
        <h2 class="section-title">Nos marques populaires</h2>
        <div class="brands-grid">
            <?php foreach ($popularBrands as $brand): ?>
                <a href="<?= url('catalogue/?marque=' . urlencode($brand)) ?>" class="brand-logo">
                    <img src="<?= asset('images/brands/' . strtolower($brand) . '.png') ?>"
                        alt="Logo <?= htmlspecialchars($brand) ?>"
                        title="Voir les véhicules <?= htmlspecialchars($brand) ?>"
                        onerror="this.src='<?= asset('images/brands/default-brand.png') ?>'">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS pour le carrousel -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Section Témoignages -->
<section class="testimonials">
    <div class="container">
        <h2 class="section-title">Ce que disent nos clients</h2>

        <?php if (!empty($testimonials)): ?>
            <div id="carouselTestimonials" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <div class="testimonial-card text-center">
                                <div class="testimonial-rating">
                                    <?php for ($i = 0; $i < $testimonial['note']; $i++): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="testimonial-text">"<?= htmlspecialchars($testimonial['commentaire']) ?>"</p>
                                <div class="testimonial-author">
                                    <img src="<?= asset('images/testimonials/user' . rand(1, 3) . '.jpg') ?>"
                                        alt="<?= htmlspecialchars($testimonial['client_nom']) ?>" class="rounded-circle">
                                    <div class="author-info">
                                        <h4><?= htmlspecialchars($testimonial['client_nom']) ?></h4>
                                        <span>Client depuis <?= date('Y', strtotime($testimonial['date_avis'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselTestimonials" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselTestimonials" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Aucun avis client disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?>