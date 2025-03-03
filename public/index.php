<?php
/**
 * Page d'accueil
 * Affiche les derniers véhicules ajoutés et une bannière d'accueil
 */

// Définition du titre de la page
$pageTitle = "Accueil";

// Inclusion des fichiers nécessaires
include '../config/config.php'; // Connexion à la DB
require_once '../includes/header.php'; // En-tête du site

// Récupérer les 6 derniers véhicules ajoutés
$query = "SELECT * FROM vehicules ORDER BY id_vehicule DESC LIMIT 6";
$result = $conn->query($query);

// Récupérer les marques distinctes
$marques_query = "SELECT DISTINCT marque FROM vehicules ORDER BY marque ASC LIMIT 5";
$marques_result = $conn->query($marques_query);
?>

<!-- Bannière d'accueil -->
<section class="hero-banner">
    <div class="hero-content">
        <h1>Trouvez votre véhicule idéal</h1>
        <p>Location et vente de véhicules de qualité pour tous vos besoins</p>
        <div class="hero-buttons">
            <a href="catalogue.php" class="btn btn-primary"><i class="fas fa-search"></i> Explorer le catalogue</a>
            <a href="location.php" class="btn btn-secondary"><i class="fas fa-key"></i> Louer un véhicule</a>
        </div>
    </div>
</section>

<!-- Section de recherche de véhicules -->
<section class="vehicle-search">
    <div class="container">
        <h2 class="section-title">Nos catégories de véhicules</h2>
        <div class="vehicle-types">
            <div class="vehicle-type">
                <i class="fas fa-car vehicle-icon"></i>
                <h3>Citadines</h3>
                <p>Parfaites pour la ville</p>
            </div>
            <div class="vehicle-type">
                <i class="fas fa-car-side vehicle-icon"></i>
                <h3>Berlines</h3>
                <p>Confort et élégance</p>
            </div>
            <div class="vehicle-type">
                <i class="fas fa-truck vehicle-icon"></i>
                <h3>SUV</h3>
                <p>Polyvalence maximale</p>
            </div>
            <div class="vehicle-type">
                <i class="fas fa-truck-pickup vehicle-icon"></i>
                <h3>Utilitaires</h3>
                <p>Pour les professionnels</p>
            </div>
        </div>
    </div>
</section>

<!-- Section des marques populaires -->
<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="widget">
                <i class="fas fa-shield-alt widget-icon"></i>
                <h3 class="widget-title">Sécurité garantie</h3>
                <p class="widget-content">Tous nos véhicules sont régulièrement contrôlés et entretenus</p>
            </div>
            <div class="widget">
                <i class="fas fa-clock widget-icon"></i>
                <h3 class="widget-title">Service 24/7</h3>
                <p class="widget-content">Assistance routière disponible à tout moment</p>
            </div>
            <div class="widget">
                <i class="fas fa-hand-holding-usd widget-icon"></i>
                <h3 class="widget-title">Prix compétitifs</h3>
                <p class="widget-content">Les meilleurs tarifs du marché garantis</p>
            </div>
            <div class="widget">
                <i class="fas fa-smile widget-icon"></i>
                <h3 class="widget-title">Satisfaction client</h3>
                <p class="widget-content">98% de nos clients satisfaits</p>
            </div>
        </div>
    </div>
</section>

<section class="popular-brands">
    <div class="container">
        <h2 class="section-title">Marques populaires</h2>
        <div class="brands-grid">
            <div class="brand-card">
                <img src="assets/images/brands/volkswagen.png" alt="Volkswagen">
                <h3>Volkswagen</h3>
            </div>
            <div class="brand-card">
                <img src="assets/images/brands/toyota.png" alt="Toyota">
                <h3>Toyota</h3>
            </div>
            <div class="brand-card">
                <img src="assets/images/brands/renault.png" alt="Renault">
                <h3>Renault</h3>
            </div>
            <div class="brand-card">
                <img src="assets/images/brands/peugeot.png" alt="Peugeot">
                <h3>Peugeot</h3>
            </div>
            <div class="brand-card">
                <img src="assets/images/brands/mercedes.png" alt="Mercedes">
                <h3>Mercedes</h3>
            </div>
        </div>
    </div>
</section>

<!-- Section des avis clients -->
<section class="customer-reviews">
    <div class="container">
        <h2 class="section-title">Avis de nos clients</h2>
        <div class="reviews-container">
            <div class="review-card">
                <div class="review-stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="review-title">Service excellent</h3>
                <p>Location rapide et simple. Véhicule en parfait état. Je recommande !</p>
                <p class="reviewer-name">Marie D.</p>
            </div>
            <div class="review-card">
                <div class="review-stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="review-title">Très professionnel</h3>
                <p>Personnel accueillant et prix très compétitifs. Parfait !</p>
                <p class="reviewer-name">Thomas L.</p>
            </div>
            <div class="review-card">
                <div class="review-stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3 class="review-title">Très satisfait</h3>
                <p>Processus de location simple et transparent. Je reviendrai !</p>
                <p class="reviewer-name">Pierre M.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Prêt à trouver votre véhicule ?</h2>
            <p>Découvrez notre sélection de véhicules disponibles à la location et à la vente</p>
            <div class="cta-buttons">
                <a href="catalogue.php" class="btn btn-primary"><i class="fas fa-car"></i> Voir le catalogue</a>
                <a href="contact.php" class="btn btn-secondary"><i class="fas fa-envelope"></i> Nous contacter</a>
            </div>
        </div>
    </div>
</section>

<div class="container mt-4">
    <?php if (isset($_SESSION['alert'])): ?>
        <?php $alert = getAlert(); ?>
        <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $alert['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Outils de diagnostic -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Outils de diagnostic</h5>
        </div>
        <div class="card-body">
            <p>Utilisez ces outils pour vérifier que la connexion entre le front-end et le back-end fonctionne correctement.</p>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <a href="test-connection.php" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-database mr-2"></i> Test de connexion à la base de données
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="api-test.php" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-exchange-alt mr-2"></i> Test d'API et communication AJAX
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; // Pied de page ?>