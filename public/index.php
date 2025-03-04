<?php
// Définition du chemin racine
define('ROOT_PATH', dirname(__DIR__));

// Configuration
require_once ROOT_PATH . '/config/config.php';

// Récupération des données
$vehicles = getVehicles(5); // 5 véhicules pour le slider
$popularBrands = getPopularBrands();
$testimonials = getTestimonials(3);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Teran'Cars - Votre partenaire de confiance pour l'achat et la location de véhicules de qualité">
    <title><?= SITE_NAME ?> - Vente et Location de Véhicules</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
    <header class="header">
        <nav class="nav-main container">
            <div class="logo">
                <a href="/">
                    <img src="<?= asset('images/icones/Tlogo.png') ?>" alt="Teran'Cars Logo" class="logo-img">
                    <span class="logo-text">Teran<span class="highlight">'Cars</span></span>
                </a>
            </div>
            
            <div class="nav-links">
                <a href="/" class="active">
                    <i class="fas fa-home"></i>
                    Accueil
                </a>
                <a href="/catalogue">
                    <i class="fas fa-car"></i>
                    Catalogue
                </a>
                <a href="/contact">
                    <i class="fas fa-envelope"></i>
                    Contact
                </a>
                </a>
                <a href="/a-propos">
                    <i class="fas fa-info-circle"></i>
                    A propos
                </a>
            </div>

            <div class="nav-auth">
                <a href="/connexion" class="btn btn-outline">Connexion</a>
                <a href="/inscription" class="btn btn-secondary">Inscription</a>
            </div>
        </nav>
    </header>

    <!-- Section Hero -->
    <section class="hero">
        <div class="hero-content container">
            <h1>Bienvenue chez Teran'Cars</h1>
            <p class="hero-subtitle">Votre partenaire de confiance pour l'achat et la location de véhicules de qualité</p>
            <div class="hero-buttons">
                <a href="/catalogue" class="btn btn-primary">Voir notre catalogue</a>
                <a href="/contact" class="btn btn-outline">Nous contacter</a>
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
                            case strpos($marqueModele, 'honda civic') !== false:
                                $imageFilename = 'honda-civic-2017.jpg';
                                break;
                            case strpos($marqueModele, 'bmw x5') !== false:
                                $imageFilename = '2021_bmw_x5.avif';
                                break;
                            case strpos($marqueModele, 'tesla model 3') !== false:
                                $imageFilename = 'tesla model 3.jpeg';
                                break;
                            case strpos($marqueModele, 'mercedes classe a') !== false:
                                $imageFilename = 'mercedes classe A.jpeg';
                                break;
                            case strpos($marqueModele, 'peugeot 208') !== false:
                                $imageFilename = 'peugeot 208.jpeg';
                                break;
                            case strpos($marqueModele, 'ford mustang') !== false:
                                $imageFilename = 'ford_mustang.jpg';
                                break;
                            case strpos($marqueModele, 'audi') !== false:
                                $imageFilename = 'audi.jpeg';
                                break;
                            case strpos($marqueModele, 'hyundai tucson') !== false:
                                $imageFilename = 'Hyundai-Tucson.jpg';
                                break;
                            case strpos($marqueModele, 'renault clio') !== false:
                                $imageFilename = 'Renault clio.jpeg';
                                break;
                            case strpos($marqueModele, 'nissan leaf') !== false:
                                $imageFilename = 'Nissan_leaf.jpeg';
                                break;
                            case strpos($marqueModele, 'toyota rav4') !== false:
                                $imageFilename = '2023-Toyota-RAV4.jpg';
                                break;
                            case strpos($marqueModele, 'jeep wrangler') !== false:
                                $imageFilename = 'JeepWrangler.jpeg';
                                break;
                            case strpos($marqueModele, 'land rover defender') !== false:
                                $imageFilename = 'Land RoverDefender.webp';
                                break;
                            case strpos($marqueModele, 'hyundai ioniq') !== false:
                                $imageFilename = 'Hyundai-Ioniq.jpeg';
                                break;
                            case strpos($marqueModele, 'toyota prius') !== false:
                                $imageFilename = 'prius.jpeg';
                                break;
                            case strpos($marqueModele, 'fiat 500') !== false:
                                $imageFilename = 'Fiat500.webp';
                                break;
                            case strpos($marqueModele, 'chevrolet camaro') !== false:
                                $imageFilename = 'chevrolet_camaro.jpg';
                                break;
                            default:
                                // Image par défaut si aucune correspondance n'est trouvée
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
                            <a href="<?= url('vehicule/' . $vehicle['id_vehicule']) ?>" class="btn btn-primary">Voir détails</a>
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

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>À propos de Teran'Cars</h3>
                <p>Teran'Cars est votre partenaire de confiance pour l'achat et la location de véhicules de qualité depuis 2020.</p>
            </div>
            
            <div class="footer-section links">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="<?= url('/') ?>">Accueil</a></li>
                    <li><a href="<?= url('catalogue') ?>">Catalogue</a></li>
                    <li><a href="<?= url('contact') ?>">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section contact">
                <h3>Contactez-nous</h3>
                <p>
                    <span><i class="fas fa-phone"></i> +33 1 23 45 67 89</span>
                    <span><i class="fas fa-envelope"></i> contact@terancars.fr</span>
                    <span><i class="fas fa-map-marker-alt"></i> 123 Avenue des Véhicules, 75000 Paris</span>
                </p>
            </div>
            
            <div class="footer-section social">
                <h3>Suivez-nous</h3>
                <div class="socials">
                    <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?> - Vente & Location de Voitures | Tous droits réservés.</p>
        </div>
    </footer>

    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>