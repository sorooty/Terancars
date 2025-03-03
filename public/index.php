<?php
/**
 * Page d'accueil
 * Affiche les derniers véhicules ajoutés et une bannière d'accueil
 */

// Définition du titre de la page
$pageTitle = "Accueil";

// Inclusion des fichiers nécessaires
include '../config/config.php'; // Connexion à la DB
include '../includes/header.php'; // En-tête du site

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
        <p>Vente et location de véhicules neufs et d'occasion de qualité</p>
        <div class="hero-buttons">
            <a href="catalogue.php" class="btn btn-primary">Voir notre catalogue</a>
            <a href="contact.php" class="btn btn-secondary">Nous contacter</a>
        </div>
    </div>
</section>

<!-- Section de recherche de véhicules -->
<section class="vehicle-search">
    <div class="container">
        <div class="search-header">
            <h2>Rechercher par type de véhicule</h2>
        </div>
        
        <div class="vehicle-types">
            <div class="vehicle-type">
                <img src="<?php echo SITE_URL; ?>assets/images/icones/sedan.png" alt="Berline">
                <h3>Berlines</h3>
            </div>
            <div class="vehicle-type">
                <img src="<?php echo SITE_URL; ?>assets/images/icones/suv.png" alt="SUV">
                <h3>SUV</h3>
            </div>
            <div class="vehicle-type">
                <img src="<?php echo SITE_URL; ?>assets/images/icones/hatchback.png" alt="Compacte">
                <h3>Compactes</h3>
            </div>
            <div class="vehicle-type">
                <img src="<?php echo SITE_URL; ?>assets/images/icones/convertible.png" alt="Cabriolet">
                <h3>Cabriolets</h3>
            </div>
            <div class="vehicle-type">
                <img src="<?php echo SITE_URL; ?>assets/images/icones/electric.png" alt="Électrique">
                <h3>Électriques</h3>
            </div>
        </div>
    </div>
</section>

<!-- Section des marques populaires -->
<section class="popular-brands">
    <div class="container">
        <div class="brands-header">
            <h2>Marques populaires</h2>
        </div>
        
        <div class="brands-grid">
            <?php 
            // Afficher les marques disponibles
            if ($marques_result && $marques_result->num_rows > 0) {
                while ($marque = $marques_result->fetch_assoc()) {
                    $marque_nom = $marque['marque'];
                    $logo_path = SITE_URL . "assets/images/marques/" . strtolower($marque_nom) . ".png";
                    // Logo par défaut si non disponible
                    $default_logo = SITE_URL . "assets/images/marques/default.png";
            ?>
                <div class="brand-card">
                    <img src="<?php echo $logo_path; ?>" alt="<?php echo $marque_nom; ?>" onerror="this.src='<?php echo $default_logo; ?>'">
                    <h3><?php echo $marque_nom; ?></h3>
                </div>
            <?php
                }
            } else {
                // Afficher des marques par défaut si aucune n'est trouvée
                $default_brands = ['Audi', 'BMW', 'Mercedes', 'Renault', 'Peugeot'];
                foreach ($default_brands as $brand) {
                    $logo_path = SITE_URL . "assets/images/marques/" . strtolower($brand) . ".png";
                    $default_logo = SITE_URL . "assets/images/marques/default.png";
            ?>
                <div class="brand-card">
                    <img src="<?php echo $logo_path; ?>" alt="<?php echo $brand; ?>" onerror="this.src='<?php echo $default_logo; ?>'">
                    <h3><?php echo $brand; ?></h3>
                </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Section des avis clients -->
<section class="customer-reviews">
    <div class="container">
        <div class="reviews-header">
            <h2>Ce que disent nos clients</h2>
        </div>
        
        <div class="reviews-slider">
            <div class="reviews-container">
                <div class="review-card">
                    <div class="review-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="review-title">Service exceptionnel</h3>
                    <p class="review-text">J'ai acheté ma nouvelle voiture chez Terancar et je suis très satisfait du service client et de la qualité du véhicule.</p>
                    <p class="reviewer-name">Jean Dupont</p>
                </div>
                
                <div class="review-card">
                    <div class="review-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <h3 class="review-title">Excellent rapport qualité-prix</h3>
                    <p class="review-text">Les prix sont compétitifs et la qualité des véhicules est au rendez-vous. Je recommande vivement.</p>
                    <p class="reviewer-name">Marie Martin</p>
                </div>
                
                <div class="review-card">
                    <div class="review-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="review-title">Location sans souci</h3>
                    <p class="review-text">J'ai loué une voiture pour mes vacances, tout s'est parfaitement déroulé. Le processus était simple et rapide.</p>
                    <p class="reviewer-name">Pierre Leroy</p>
                </div>
                
                <div class="review-card">
                    <div class="review-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <h3 class="review-title">Équipe professionnelle</h3>
                    <p class="review-text">L'équipe est très professionnelle et à l'écoute des besoins. Ils m'ont aidé à trouver le véhicule parfait.</p>
                    <p class="reviewer-name">Sophie Bernard</p>
                </div>
                
                <div class="review-card">
                    <div class="review-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="review-title">Service après-vente impeccable</h3>
                    <p class="review-text">J'ai eu un petit problème après l'achat, et le service après-vente a été très réactif. Problème résolu rapidement.</p>
                    <p class="reviewer-name">Thomas Petit</p>
                </div>
            </div>
            
            <div class="slider-controls">
                <div class="slider-arrow prev">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="slider-arrow next">
                    <i class="fas fa-chevron-right"></i>
                </div>
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

<?php include '../includes/footer.php'; // Pied de page ?>