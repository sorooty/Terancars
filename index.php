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

// Fonction pour trouver le bon format d'image du véhicule
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

<!-- Section Bienvenue (Page d'entrée d'Omar) -->
<?php include ROOT_PATH . '/includes/entry.php'; ?>


<!-- Section Hero -->
<section class="hero">
    <div class="hero-content container">
        <h1>Devenez client privilégié TeranCar</h1>
        <p class="hero-subtitle">Profitez d'avantages exclusifs, de réductions spéciales et d'un service premium pour vos locations et achats de véhicules à Dakar</p>
        <div class="hero-buttons">
            <a href="<?= url('pages/auth/register.php') ?>" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Créer un compte
            </a>
            <a href="<?= url('pages/auth/login.php') ?>" class="btn btn-secondary">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </a>
        </div>
        <div class="hero-features">
            <div class="feature-item">
                <i class="fas fa-percentage"></i>
                <span>Réductions exclusives</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span>Réservation prioritaire</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-award"></i>
                <span>Programme de fidélité</span>
            </div>
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
                            <a href="<?= url('vehicule/detail?id_vehicule=' . $vehicle['id_vehicule']) ?>" class="btn btn-primary">
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
                <a href="<?= url('marque/' . urlencode($brand)) ?>" class="brand-logo">
                    <img src="<?= asset('images/brands/' . strtolower($brand) . '.png') ?>"
                        alt="Logo <?= htmlspecialchars($brand) ?>"
                        title="Voir les véhicules <?= htmlspecialchars($brand) ?>"
                        onerror="this.src='<?= asset('images/brands/default-brand.png') ?>'">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section Témoignages -->
<section class="testimonials">
    <div class="container">
        <h2 class="section-title">Ce que disent nos clients</h2>
        <div class="testimonials-slider">
            <?php if (!empty($testimonials)): ?>
                <div class="testimonials-track">
                    <?php foreach ($testimonials as $index => $testimonial): ?>
                        <div class="testimonial-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                            <div class="testimonial-header">
                                <div class="testimonial-avatar">
                                    <img src="<?= asset('images/testimonials/user' . rand(1, 3) . '.jpg') ?>" 
                                         alt="<?= htmlspecialchars($testimonial['client_nom']) ?>">
                                </div>
                                <div class="testimonial-meta">
                                    <h3><?= htmlspecialchars($testimonial['client_nom']) ?></h3>
                                    <span class="testimonial-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        Client depuis <?= date('Y', strtotime($testimonial['date_avis'])) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="testimonial-rating">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="<?= $i < $testimonial['note'] ? 'fas' : 'far' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <div class="testimonial-content">
                                <i class="fas fa-quote-left quote-icon"></i>
                                <p><?= htmlspecialchars($testimonial['commentaire']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="slider-controls">
                    <button class="slider-arrow prev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="slider-dots"></div>
                    <button class="slider-arrow next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            <?php else: ?>
                <p class="no-testimonials">Aucun avis client disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Styles pour la section témoignages */
.testimonials {
    padding: 6rem 0;
    background: linear-gradient(135deg, var(--light) 0%, white 100%);
    overflow: hidden;
}

.testimonials .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.testimonials-slider {
    position: relative;
    margin-top: 3rem;
    overflow: hidden;
    padding: 0 1rem;
}

.testimonials-track {
    display: flex;
    gap: 2rem;
    transition: transform 0.5s ease;
    width: max-content;
}

.testimonial-card {
    flex: 0 0 calc(33.333% - 1.33rem);
    width: 350px;
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
}

.testimonial-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    border-radius: 20px 20px 0 0;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.testimonial-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.testimonial-avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--secondary-color);
    box-shadow: 0 5px 15px rgba(var(--secondary-rgb), 0.2);
}

.testimonial-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.testimonial-meta {
    flex: 1;
}

.testimonial-meta h3 {
    color: var(--primary-color);
    font-size: 1.2rem;
    margin-bottom: 0.3rem;
}

.testimonial-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.testimonial-rating {
    margin-bottom: 1.5rem;
    color: #FFD700;
    font-size: 1.1rem;
}

.quote-icon {
    color: var(--secondary-color);
    font-size: 2rem;
    opacity: 0.2;
    margin-bottom: 1rem;
}

.testimonial-content {
    position: relative;
}

.testimonial-content p {
    color: var(--text-color);
    line-height: 1.6;
    font-style: italic;
}

.slider-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    margin-top: 3rem;
}

.slider-arrow {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    transition: all 0.3s ease;
}

.slider-arrow:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.slider-dots {
    display: flex;
    gap: 0.5rem;
}

.slider-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--border-color);
    cursor: pointer;
    transition: all 0.3s ease;
}

.slider-dot.active {
    background: var(--secondary-color);
    transform: scale(1.5);
}

.no-testimonials {
    text-align: center;
    color: var(--text-muted);
    font-style: italic;
    padding: 2rem;
}

@media (max-width: 1200px) {
    .testimonial-card {
        flex: 0 0 calc(50% - 1rem);
    }
}

@media (max-width: 768px) {
    .testimonial-card {
        flex: 0 0 100%;
    }
    
    .testimonial-header {
        flex-direction: column;
        text-align: center;
    }
    
    .testimonial-meta {
        text-align: center;
    }
    
    .testimonial-date {
        justify-content: center;
    }
    
    .testimonial-rating {
        text-align: center;
    }
    
    .slider-controls {
        gap: 1rem;
    }
}

.hero {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    padding: 6rem 0;
    color: white;
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= asset("images/pattern.png") ?>') repeat;
    opacity: 0.1;
}

.hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
}

.hero h1 {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.hero-subtitle {
    font-size: 1.25rem;
    max-width: 800px;
    margin: 0 auto 2.5rem;
    line-height: 1.6;
    opacity: 0.9;
}

.hero-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    margin-bottom: 3rem;
}

.hero-buttons .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    min-width: 200px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hero-buttons .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.hero-features {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-top: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: rgba(255,255,255,0.1);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    backdrop-filter: blur(5px);
}

.feature-item i {
    color: white;
    font-size: 1.25rem;
}

.feature-item span {
    color: white;
    font-size: 1rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.25rem;
    }

    .hero-subtitle {
        font-size: 1.1rem;
        padding: 0 1rem;
    }

    .hero-buttons {
        flex-direction: column;
        padding: 0 1rem;
    }

    .hero-buttons .btn {
        width: 100%;
    }

    .hero-features {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.testimonials-track');
    const cards = document.querySelectorAll('.testimonial-card');
    const prevBtn = document.querySelector('.slider-arrow.prev');
    const nextBtn = document.querySelector('.slider-arrow.next');
    const dotsContainer = document.querySelector('.slider-dots');
    
    if (!track || cards.length === 0) return;
    
    let currentIndex = 0;
    const cardWidth = 350 + 32; // Largeur fixe + gap
    
    // Créer les points de navigation
    cards.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('slider-dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });
    
    function updateDots() {
        document.querySelectorAll('.slider-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }
    
    function goToSlide(index) {
        currentIndex = index;
        const translateX = -currentIndex * cardWidth;
        track.style.transform = `translateX(${translateX}px)`;
        updateDots();
        
        // Mettre à jour l'état des boutons
        prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
        nextBtn.style.opacity = currentIndex === cards.length - 1 ? '0.5' : '1';
    }
    
    prevBtn?.addEventListener('click', () => {
        if (currentIndex > 0) {
            goToSlide(currentIndex - 1);
        }
    });
    
    nextBtn?.addEventListener('click', () => {
        if (currentIndex < cards.length - 1) {
            goToSlide(currentIndex + 1);
        }
    });
    
    // Support du glissement tactile
    let touchStartX = 0;
    let touchEndX = 0;
    
    track.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoplay(); // Arrêter l'autoplay au toucher
    });
    
    track.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
        startAutoplay(); // Redémarrer l'autoplay après le toucher
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0 && currentIndex < cards.length - 1) {
                goToSlide(currentIndex + 1);
            } else if (diff < 0 && currentIndex > 0) {
                goToSlide(currentIndex - 1);
            }
        }
    }
    
    // Défilement automatique
    let autoplayInterval;
    
    function startAutoplay() {
        autoplayInterval = setInterval(() => {
            if (currentIndex < cards.length - 1) {
                goToSlide(currentIndex + 1);
            } else {
                goToSlide(0);
            }
        }, 5000);
    }
    
    function stopAutoplay() {
        clearInterval(autoplayInterval);
    }
    
    // Démarrer le défilement automatique
    startAutoplay();
    
    // Arrêter le défilement au survol
    track.addEventListener('mouseenter', stopAutoplay);
    track.addEventListener('mouseleave', startAutoplay);
    
    // État initial des boutons
    goToSlide(0);
});
</script>

<script src="<?= asset('js/faq.js'); ?>"></script>


<!-- Section FAQ / Blog (Omar) -->
<?php include ROOT_PATH . '/includes/faq.php'; ?>


<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template commun (header + footer inclus dedans)
require_once ROOT_PATH . '/includes/template.php';
