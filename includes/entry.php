<section class="welcome-section">
    <div class="welcome-overlay"></div>
    <div class="container">
        <div class="welcome-content">
            <div class="title-container">
                <h1 class="welcome-title">Découvrez</h1>
                <h1 class="brand-title">TeranCars</h1>
                <p class="welcome-subtitle">L'excellence automobile à Dakar - Location et vente de véhicules de prestige</p>
                <div class="welcome-buttons">
                    <a href="<?= url('catalogue') ?>" class="btn-primary">
                        <span>Explorer notre catalogue</span>
                        <i class="fas fa-car"></i>
                    </a>
                </div>
            </div>
            <div class="image-container">
                <img src="<?= asset('images/homeimage1.jpg') ?>" class="homeimg" alt="Accueil">
                <div class="floating-features">
                    <div class="feature-item">
                        <i class="fas fa-star"></i>
                        <span>Véhicules Premium</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Service Premium</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Qualité garantie</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="gallery-section">
    <div class="container">
        <h2 class="gallery-title">Notre Collection</h2>
        <div class="gallery-grid">
            <div class="gallery-item">
                <div class="gallery-image">
                    <img src="<?= asset('images/gallery/luxury.png') ?>" alt="Voitures de luxe">
                    <div class="gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
                <div class="gallery-content">
                    <h3>Voitures de Luxe</h3>
                    <p>Une sélection exclusive de véhicules haut de gamme pour une expérience de conduite incomparable.</p>
                </div>
            </div>
            <div class="gallery-item">
                <div class="gallery-image">
                    <img src="<?= asset('images/gallery/suv.png') ?>" alt="SUV et 4x4">
                    <div class="gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
                <div class="gallery-content">
                    <h3>SUV & 4x4</h3>
                    <p>Des véhicules robustes et confortables, parfaits pour toutes vos aventures.</p>
                </div>
            </div>
            <div class="gallery-item">
                <div class="gallery-image">
                    <img src="<?= asset('images/gallery/economic.jpg') ?>" alt="Véhicules économiques">
                    <div class="gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
                <div class="gallery-content">
                    <h3>Citadines</h3>
                    <p>Des véhicules compacts et économiques, idéaux pour la ville.</p>
        </div>
            </div>
            <div class="gallery-item">
                <div class="gallery-image">
                    <img src="<?= asset('images/gallery/service.jpg') ?>" alt="Service client">
                    <div class="gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                    </div>
                </div>
                <div class="gallery-content">
                    <h3>Service Premium</h3>
                    <p>Un accompagnement personnalisé et des services sur mesure pour votre satisfaction.</p>
        </div>
            </div>
        </div>
    </div>
</section>

<style>
.welcome-section {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
}

.welcome-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('<?= asset("images/pattern.png") ?>') repeat;
    opacity: 0.1;
    pointer-events: none;
}

.welcome-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4rem;
    padding: 2rem 0;
}

.title-container {
    flex: 1;
    max-width: 600px;
    animation: fadeInLeft 1s ease;
}

.welcome-title {
    font-size: 3.5rem;
    color: var(--light-text);
    margin-bottom: 0.5rem;
    font-weight: 300;
    opacity: 0;
    animation: fadeInUp 0.8s ease forwards;
    animation-delay: 0.3s;
}

.brand-title {
    font-size: 4.5rem;
    color: var(--secondary-color);
    margin-bottom: 1.5rem;
    font-weight: 700;
    opacity: 0;
    animation: fadeInUp 0.8s ease forwards;
    animation-delay: 0.5s;
}

.welcome-subtitle {
    font-size: 1.2rem;
    color: var(--light-text);
    margin-bottom: 2rem;
    line-height: 1.6;
    opacity: 0.9;
    opacity: 0;
    animation: fadeInUp 0.8s ease forwards;
    animation-delay: 0.7s;
}

.welcome-buttons {
    opacity: 0;
    animation: fadeInUp 0.8s ease forwards;
    animation-delay: 0.9s;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 2rem;
    background: var(--secondary-color);
    color: var(--light-text);
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--secondary-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(var(--secondary-rgb), 0.3);
}

.btn-primary i {
    transition: transform 0.3s ease;
}

.btn-primary:hover i {
    transform: translateX(5px);
}

.image-container {
    flex: 1;
    position: relative;
    animation: fadeInRight 1s ease;
}

.homeimg {
    width: 100%;
    max-width: 600px;
    height: auto;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    transform: perspective(1000px) rotateY(-5deg);
    transition: transform 0.5s ease;
}

.homeimg:hover {
    transform: perspective(1000px) rotateY(0deg);
}

.floating-features {
    position: absolute;
    right: -20px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.feature-item {
    background: rgba(255, 255, 255, 0.95);
    padding: 1rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transform: translateX(100%);
    animation: slideIn 0.5s ease forwards;
}

.feature-item:nth-child(1) { animation-delay: 1.1s; }
.feature-item:nth-child(2) { animation-delay: 1.3s; }
.feature-item:nth-child(3) { animation-delay: 1.5s; }

.feature-item i {
    color: var(--primary-color);
    font-size: 1.2rem;
}

.feature-item span {
    color: var(--primary-color);
    font-weight: 500;
    white-space: nowrap;
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@media (max-width: 1200px) {
    .welcome-title {
        font-size: 3rem;
    }
    
    .brand-title {
        font-size: 4rem;
    }
    
    .floating-features {
        right: 0;
    }
}

@media (max-width: 992px) {
    .welcome-content {
        flex-direction: column;
        text-align: center;
        gap: 3rem;
    }
    
    .title-container {
        max-width: 100%;
    }
    
    .floating-features {
        position: static;
        flex-direction: row;
        justify-content: center;
        margin-top: 2rem;
        transform: none;
    }
    
    .feature-item {
        transform: translateY(50px);
    }
}

@media (max-width: 768px) {
    .welcome-title {
        font-size: 2.5rem;
    }
    
    .brand-title {
        font-size: 3.5rem;
    }
    
    .welcome-subtitle {
        font-size: 1.1rem;
    }
    
    .floating-features {
        flex-direction: column;
        align-items: center;
    }
    
    .feature-item {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 576px) {
    .welcome-title {
        font-size: 2rem;
    }
    
    .brand-title {
        font-size: 3rem;
    }
    
    .btn-primary {
        width: 100%;
        justify-content: center;
    }
}

/* Nouveaux styles pour la galerie */
.gallery-section {
    padding: 6rem 0;
    background: var(--light);
}

.gallery-title {
    text-align: center;
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 3rem;
    position: relative;
}

.gallery-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 3px;
    background: var(--secondary-color);
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    padding: 0 1rem;
}

.gallery-item {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.8s ease forwards;
}

.gallery-item:nth-child(1) { animation-delay: 0.2s; }
.gallery-item:nth-child(2) { animation-delay: 0.4s; }
.gallery-item:nth-child(3) { animation-delay: 0.6s; }
.gallery-item:nth-child(4) { animation-delay: 0.8s; }

.gallery-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.gallery-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(var(--primary-rgb), 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-overlay i {
    color: white;
    font-size: 2rem;
    transform: scale(0.5);
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover .gallery-overlay i {
    transform: scale(1);
}

.gallery-item:hover .gallery-image img {
    transform: scale(1.1);
}

.gallery-content {
    padding: 1.5rem;
}

.gallery-content h3 {
    color: var(--primary-color);
    font-size: 1.3rem;
    margin-bottom: 0.8rem;
}

.gallery-content p {
    color: var(--text-color);
    font-size: 0.95rem;
    line-height: 1.6;
}

@media (max-width: 992px) {
    .gallery-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .gallery-image {
        height: 200px;
    }
}

@media (max-width: 576px) {
    .gallery-title {
        font-size: 2rem;
    }
    
    .gallery-content {
        padding: 1rem;
    }
    
    .gallery-content h3 {
        font-size: 1.2rem;
    }
    
    .gallery-content p {
        font-size: 0.9rem;
    }
}
</style>