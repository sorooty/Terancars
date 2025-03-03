/**
 * Fichier JavaScript principal pour le site Terancar
 * Gère les interactions utilisateur et les animations
 */

// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser toutes les fonctionnalités
    initCartCount();
    initAlertMessages();
    initMobileMenu();
    initSmoothScroll();
    initReviewsSlider();
    initProductGallery();
    initTabsSystem();
    initAddToCartButtons();
});

/**
 * Initialise le compteur du panier
 */
function initCartCount() {
    // Cette fonction sera utilisée pour mettre à jour le compteur du panier
    // en fonction des données stockées dans le localStorage ou la session
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        // Pour l'instant, on utilise une valeur statique
        // À remplacer par une requête AJAX pour obtenir le nombre réel d'articles
        const count = localStorage.getItem('cartCount') || 0;
        cartCount.textContent = count;
    }
}

/**
 * Initialise le menu mobile
 */
function initMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuBtn && mainNav) {
        mobileMenuBtn.addEventListener('click', function() {
            // Toggle la classe active pour afficher/masquer le menu
            mainNav.classList.toggle('active');
            mobileMenuBtn.classList.toggle('active');
            
            // Accessibilité
            const expanded = mainNav.classList.contains('active');
            mobileMenuBtn.setAttribute('aria-expanded', expanded);
        });
        
        // Fermer le menu mobile lorsqu'on clique sur un lien
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                mainNav.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
                mobileMenuBtn.setAttribute('aria-expanded', false);
            });
        });
    }
}

/**
 * Initialise le défilement fluide pour les ancres
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            // Ne rien faire si c'est juste un "#"
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // Offset pour l'en-tête fixe
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Initialise le slider des avis clients
 */
function initReviewsSlider() {
    const reviewsContainer = document.querySelector('.reviews-container');
    const prevArrow = document.querySelector('.slider-arrow.prev');
    const nextArrow = document.querySelector('.slider-arrow.next');
    
    if (reviewsContainer && prevArrow && nextArrow) {
        // Largeur d'une carte d'avis + marge
        const cardWidth = 250 + 24; // 250px de largeur + 24px de marge
        
        // Gestionnaire pour le bouton suivant
        nextArrow.addEventListener('click', function() {
            reviewsContainer.scrollBy({
                left: cardWidth,
                behavior: 'smooth'
            });
            updateButtons();
        });
        
        // Gestionnaire pour le bouton précédent
        prevArrow.addEventListener('click', function() {
            reviewsContainer.scrollBy({
                left: -cardWidth,
                behavior: 'smooth'
            });
            updateButtons();
        });
        
        // Mettre à jour l'état des boutons en fonction de la position du scroll
        function updateButtons() {
            const isAtStart = reviewsContainer.scrollLeft <= 0;
            const isAtEnd = reviewsContainer.scrollLeft + reviewsContainer.clientWidth >= reviewsContainer.scrollWidth - 10;
            
            prevArrow.classList.toggle('disabled', isAtStart);
            nextArrow.classList.toggle('disabled', isAtEnd);
        }
        
        // Initialiser l'état des boutons
        updateButtons();
        
        // Mettre à jour l'état des boutons lors du défilement
        reviewsContainer.addEventListener('scroll', updateButtons);
    }
}

/**
 * Initialise la galerie de produits sur la page de détails
 */
function initProductGallery() {
    const mainImage = document.querySelector('.main-image img');
    const thumbnails = document.querySelectorAll('.thumbnail');
    
    if (mainImage && thumbnails.length > 0) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Mettre à jour l'image principale
                const imgSrc = this.querySelector('img').getAttribute('src');
                mainImage.setAttribute('src', imgSrc);
                
                // Mettre à jour la classe active
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }
}

/**
 * Initialise le système d'onglets
 */
function initTabsSystem() {
    const tabLinks = document.querySelectorAll('.nav-link');
    const tabContents = document.querySelectorAll('.tab-inner-content');
    
    if (tabLinks.length > 0 && tabContents.length > 0) {
        tabLinks.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Retirer la classe active de tous les onglets
                tabLinks.forEach(t => t.classList.remove('active'));
                
                // Ajouter la classe active à l'onglet cliqué
                this.classList.add('active');
                
                // Masquer tous les contenus d'onglets
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
                
                // Afficher le contenu de l'onglet correspondant
                const targetId = this.getAttribute('href').substring(1);
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.style.display = 'block';
                }
            });
        });
    }
}

/**
 * Initialise les messages d'alerte
 */
function initAlertMessages() {
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    
    if (alertCloseButtons.length > 0) {
        alertCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.closest('.alert');
                if (alert) {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }
            });
        });
        
        // Fermer automatiquement les alertes après 5 secondes
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    }
}

/**
 * Initialise les boutons d'ajout au panier
 */
function initAddToCartButtons() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    if (addToCartButtons.length > 0) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const vehicleId = this.getAttribute('data-id');
                const type = this.getAttribute('data-type');
                
                if (vehicleId && type) {
                    // Rediriger vers la page panier avec les paramètres
                    window.location.href = `panier.php?action=ajouter&id=${vehicleId}&type=${type}`;
                }
            });
        });
    }
    
    // Mettre à jour le compteur du panier
    updateCartCount();
}

/**
 * Met à jour le compteur du panier
 */
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    
    if (cartCount) {
        // Faire une requête AJAX pour obtenir le nombre d'articles dans le panier
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                cartCount.textContent = data.count;
            })
            .catch(error => {
                console.error('Erreur lors de la récupération du nombre d\'articles dans le panier:', error);
            });
    }
} 