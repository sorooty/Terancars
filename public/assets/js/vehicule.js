// Variables globales
let currentImageIndex = 0;
let totalImages = 0;
let autoPlayInterval = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Récupération du nombre total d'images
    const images = document.querySelectorAll('.gallery-img');
    totalImages = images.length;

    // Configuration des boutons de navigation
    setupNavigationButtons();

    // Configuration des points de navigation
    setupNavigationDots();

    // Configuration du bouton favori
    setupFavoriteButton();

    // Démarrage du défilement automatique
    startAutoPlay();

    // Arrêt du défilement automatique au survol
    const gallery = document.querySelector('.image-gallery');
    gallery.addEventListener('mouseenter', stopAutoPlay);
    gallery.addEventListener('mouseleave', startAutoPlay);
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
    return urlParams.get('id');
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