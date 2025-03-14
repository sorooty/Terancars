// Variables globales
let currentImageIndex = 0;
let totalImages = 0;
let autoPlayInterval = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du carrousel
    const slider = document.querySelector('.gallery-slider');
    const slides = document.querySelectorAll('.gallery-img');
    const thumbs = document.querySelectorAll('.thumb');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const favoriteBtn = document.getElementById('favoriteBtn');
    
    let currentIndex = 0;
    let autoSlideInterval = null;

    // Fonction pour mettre à jour l'affichage du carrousel
    function updateSlider(index) {
        // Gérer les limites de l'index
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;

        // Mettre à jour les images
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');

        // Mettre à jour les miniatures
        thumbs.forEach(thumb => thumb.classList.remove('active'));
        thumbs[index].classList.add('active');

        currentIndex = index;
    }

    // Fonction pour le défilement automatique
    function startAutoSlide() {
        stopAutoSlide(); // Arrêter l'intervalle existant si présent
        autoSlideInterval = setInterval(() => {
            updateSlider(currentIndex + 1);
        }, 5000);
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    // Gestionnaire d'événements pour les boutons de navigation
    prevBtn.addEventListener('click', () => {
        updateSlider(currentIndex - 1);
        stopAutoSlide();
    });

    nextBtn.addEventListener('click', () => {
        updateSlider(currentIndex + 1);
        stopAutoSlide();
    });

    // Gestionnaire d'événements pour les miniatures
    thumbs.forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            updateSlider(index);
            stopAutoSlide();
        });
    });

    // Navigation au clavier
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            updateSlider(currentIndex - 1);
            stopAutoSlide();
        } else if (e.key === 'ArrowRight') {
            updateSlider(currentIndex + 1);
            stopAutoSlide();
        }
    });

    // Support tactile
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        stopAutoSlide();
    }, false);

    slider.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        const swipeDistance = touchEndX - touchStartX;
        
        if (Math.abs(swipeDistance) > 50) { // Seuil minimum pour le swipe
            if (swipeDistance > 0) {
                updateSlider(currentIndex - 1); // Swipe vers la droite
            } else {
                updateSlider(currentIndex + 1); // Swipe vers la gauche
            }
        }
    }, false);

    // Gestion du survol
    slider.addEventListener('mouseenter', stopAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);

    // Gestion des favoris
    if (favoriteBtn) {
        const vehicleId = new URLSearchParams(window.location.search).get('id_vehicule');
        const isFavorite = localStorage.getItem(`favorite_${vehicleId}`);
        
        if (isFavorite) {
            favoriteBtn.classList.add('active');
        }

        favoriteBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            if (this.classList.contains('active')) {
                localStorage.setItem(`favorite_${vehicleId}`, 'true');
            } else {
                localStorage.removeItem(`favorite_${vehicleId}`);
            }
        });
    }

    // Démarrer le carrousel
    updateSlider(0);
    startAutoSlide();
});

// Configuration des boutons de navigation
function setupNavigationButtons() {
    const prevBtn = document.querySelector('.gallery-prev');
    const nextBtn = document.querySelector('.gallery-next');

    prevBtn.addEventListener('click', () => {
        stopAutoPlay();
        changeImage(-1);
    });

    nextBtn.addEventListener('click', () => {
        stopAutoPlay();
        changeImage(1);
    });

    // Navigation au clavier
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            stopAutoPlay();
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            stopAutoPlay();
            changeImage(1);
        }
    });
}

// Configuration des points de navigation
function setupNavigationDots() {
    const dots = document.querySelectorAll('.gallery-dot');
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopAutoPlay();
            showImage(index);
        });
    });
}

// Configuration du bouton favori
function setupFavoriteButton() {
    const favoriteBtn = document.getElementById('favoriteBtn');
    const icon = favoriteBtn.querySelector('i');
    
    // Vérifier si le véhicule est déjà en favori
    const isFavorite = localStorage.getItem(`favorite_${getVehicleId()}`);
    if (isFavorite) {
        icon.classList.add('active');
    }

    favoriteBtn.addEventListener('click', toggleFavorite);
}

// Changement d'image
function changeImage(direction) {
    let newIndex = currentImageIndex + direction;

    if (newIndex < 0) {
        newIndex = totalImages - 1;
    } else if (newIndex >= totalImages) {
        newIndex = 0;
    }

    showImage(newIndex);
}

// Affichage d'une image spécifique
function showImage(index) {
    currentImageIndex = index;

    // Déplacement du slider
    const slider = document.querySelector('.gallery-slider');
    slider.style.transform = `translateX(-${index * (100 / totalImages)}%)`;

    // Mise à jour des points de navigation
    updateDots();
}

// Mise à jour des points de navigation
function updateDots() {
    const dots = document.querySelectorAll('.gallery-dot');
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentImageIndex);
    });
}

// Démarrage du défilement automatique
function startAutoPlay() {
    if (!autoPlayInterval) {
        autoPlayInterval = setInterval(() => {
            changeImage(1);
        }, 5000);
    }
}

// Arrêt du défilement automatique
function stopAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
    }
}

// Gestion des favoris
function toggleFavorite() {
    const vehicleId = getVehicleId();
    const icon = document.querySelector('#favoriteBtn i');
    const isFavorite = localStorage.getItem(`favorite_${vehicleId}`);

    if (isFavorite) {
        localStorage.removeItem(`favorite_${vehicleId}`);
        icon.classList.remove('active');
    } else {
        localStorage.setItem(`favorite_${vehicleId}`, 'true');
        icon.classList.add('active');
    }
}

// Récupération de l'ID du véhicule depuis l'URL
function getVehicleId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id_vehicule');
}

// Support du tactile pour la galerie
document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.image-gallery');
    let touchStartX = 0;
    let touchEndX = 0;

    gallery.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
    }, false);

    gallery.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].clientX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeDistance = touchEndX - touchStartX;
        const minSwipeDistance = 50;

        if (Math.abs(swipeDistance) > minSwipeDistance) {
            if (swipeDistance > 0) {
                changeImage(-1); // Swipe droite
            } else {
                changeImage(1); // Swipe gauche
            }
        }
    }
}); 