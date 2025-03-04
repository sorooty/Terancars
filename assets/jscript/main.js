/**
 * Fichier JavaScript principal pour TeranCar
 * Contient les fonctionnalités interactives du site
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des fonctionnalités
    initMobileMenu();
    initSmoothScroll();
    initImageGallery();
    initTooltips();
});

/**
 * Initialise le menu mobile pour les petits écrans
 */
function initMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            const mainNav = document.querySelector('.main-nav');
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
}

/**
 * Initialise le défilement fluide pour les ancres
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Initialise la galerie d'images sur la page de détails
 */
function initImageGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('main-vehicle-image');
    
    if (thumbnails.length && mainImage) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Mettre à jour l'image principale
                const imgSrc = this.querySelector('img').src;
                mainImage.src = imgSrc;
                
                // Mettre à jour la classe active
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Activer la première vignette par défaut
        thumbnails[0].classList.add('active');
    }
}

/**
 * Initialise les infobulles
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = tooltipText;
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.bottom + window.scrollY + 10 + 'px';
            tooltip.style.left = rect.left + window.scrollX + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            }, { once: true });
        });
    });
}

/**
 * Fonction pour changer l'image principale dans la galerie
 * @param {string} imageSrc - Chemin de l'image à afficher
 */
function changeMainImage(imageSrc) {
    const mainImage = document.getElementById('main-vehicle-image');
    if (mainImage) {
        mainImage.src = imageSrc;
        
        // Mettre à jour la vignette active
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumbnail => {
            const thumbImg = thumbnail.querySelector('img');
            if (thumbImg && thumbImg.src === imageSrc) {
                thumbnail.classList.add('active');
            } else {
                thumbnail.classList.remove('active');
            }
        });
    }
}

/**
 * Gestion du panier local
 */
class ShoppingCart {
    constructor() {
        this.items = this.loadCart();
        this.updateCartCount();
    }
    
    // Charger le panier depuis le stockage local
    loadCart() {
        const savedCart = localStorage.getItem('terancar_cart');
        return savedCart ? JSON.parse(savedCart) : [];
    }
    
    // Sauvegarder le panier dans le stockage local
    saveCart() {
        localStorage.setItem('terancar_cart', JSON.stringify(this.items));
        this.updateCartCount();
    }
    
    // Ajouter un véhicule au panier
    addItem(vehicleId, type = 'achat', quantity = 1) {
        const existingItem = this.items.find(item => 
            item.vehicleId === vehicleId && item.type === type
        );
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({ vehicleId, type, quantity });
        }
        
        this.saveCart();
        this.showNotification('Véhicule ajouté au panier');
    }
    
    // Mettre à jour le compteur du panier
    updateCartCount() {
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            const itemCount = this.items.reduce((total, item) => total + item.quantity, 0);
            cartCountElement.textContent = itemCount;
            cartCountElement.style.display = itemCount > 0 ? 'block' : 'none';
        }
    }
    
    // Afficher une notification
    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
}

// Initialiser le panier
const cart = new ShoppingCart();

// Ajouter des gestionnaires d'événements pour les boutons d'ajout au panier
document.querySelectorAll('[data-add-to-cart]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const vehicleId = this.getAttribute('data-vehicle-id');
        const type = this.getAttribute('data-type') || 'achat';
        
        cart.addItem(vehicleId, type);
    });
}); 