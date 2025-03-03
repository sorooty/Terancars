/**
 * Fonctions de gestion du panier côté client
 */

// Fonction utilitaire pour envoyer des requêtes AJAX
const sendRequest = async (action, data = {}) => {
    try {
        const formData = new FormData();
        formData.append('action', action);
        Object.entries(data).forEach(([key, value]) => {
            formData.append(key, value);
        });

        const response = await fetch('/includes/ajax_handler.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.error) {
            throw new Error(result.error);
        }
        
        return result;
    } catch (error) {
        showAlert(error.message, 'danger');
        throw error;
    }
};

// Rafraîchir l'état du panier
const refreshCart = async () => {
    try {
        const result = await sendRequest('get_cart_state');
        updateCartUI(result.cart);
    } catch (error) {
        console.error('Erreur lors du rafraîchissement du panier:', error);
    }
};

// Ajouter un véhicule au panier
const addToCart = async (vehiculeId, type, quantite = 1, duree = null) => {
    try {
        const result = await sendRequest('add_to_cart', {
            vehicule_id: vehiculeId,
            type: type,
            quantite: quantite,
            duree: duree
        });

        updateCartUI(result.cart);
        showAlert('Véhicule ajouté au panier avec succès', 'success');
    } catch (error) {
        console.error('Erreur lors de l\'ajout au panier:', error);
    }
};

// Mettre à jour la quantité d'un article
const updateQuantity = async (vehiculeId, type, quantite) => {
    try {
        const result = await sendRequest('update_cart_quantity', {
            vehicule_id: vehiculeId,
            type: type,
            quantite: quantite
        });

        updateCartUI(result.cart);
    } catch (error) {
        console.error('Erreur lors de la mise à jour de la quantité:', error);
    }
};

// Mettre à jour la durée de location
const updateDuration = async (vehiculeId, duree) => {
    try {
        const result = await sendRequest('update_rental_duration', {
            vehicule_id: vehiculeId,
            duree: duree
        });

        updateCartUI(result.cart);
    } catch (error) {
        console.error('Erreur lors de la mise à jour de la durée:', error);
    }
};

// Supprimer un article du panier
const removeFromCart = async (vehiculeId, type) => {
    try {
        const result = await sendRequest('remove_from_cart', {
            vehicule_id: vehiculeId,
            type: type
        });

        updateCartUI(result.cart);
        showAlert('Article supprimé du panier', 'success');
        
        // Recharger la page si nous sommes sur la page panier et qu'il n'y a plus d'articles
        if (window.location.pathname.includes('panier.php') && result.cart.count.total === 0) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Erreur lors de la suppression de l\'article:', error);
    }
};

// Vider le panier
const emptyCart = async () => {
    try {
        const result = await sendRequest('empty_cart');
        updateCartUI(result.cart);
        showAlert('Panier vidé avec succès', 'success');
        
        // Recharger la page si nous sommes sur la page panier
        if (window.location.pathname.includes('panier.php')) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Erreur lors de la vidange du panier:', error);
    }
};

// Valider le panier
const validateCart = async () => {
    try {
        const result = await sendRequest('validate_cart');
        
        if (result.success) {
            showAlert('Commande validée avec succès', 'success');
            window.location.href = `/commande-confirmation.php?id=${result.commande_id}`;
        }
    } catch (error) {
        console.error('Erreur lors de la validation du panier:', error);
    }
};

// Mettre à jour l'interface utilisateur du panier
const updateCartUI = (cartState) => {
    // Mettre à jour le compteur du panier dans le header
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = cartState.count.total.toString();
        cartCount.style.display = cartState.count.total > 0 ? 'inline' : 'none';
    }

    // Si nous sommes sur la page du panier, mettre à jour les totaux
    if (window.location.pathname.includes('panier.php')) {
        // Mettre à jour les totaux
        const cartSubtotal = document.querySelector('.cart-subtotal');
        const cartTva = document.querySelector('.cart-tva');
        const cartTotal = document.querySelector('.cart-total');

        if (cartSubtotal) cartSubtotal.textContent = formatPrice(cartState.totals.sous_total);
        if (cartTva) cartTva.textContent = formatPrice(cartState.totals.tva);
        if (cartTotal) cartTotal.textContent = formatPrice(cartState.totals.total);

        // Mettre à jour les quantités
        document.querySelectorAll('.quantity-input').forEach(input => {
            const vehiculeId = input.dataset.vehiculeId;
            const type = input.dataset.type;
            const item = cartState.items[type]?.[vehiculeId];
            if (item) {
                input.value = item.quantite.toString();
            }
        });

        // Mettre à jour les durées de location
        document.querySelectorAll('.duration-input').forEach(input => {
            const vehiculeId = input.dataset.vehiculeId;
            const item = cartState.items.location?.[vehiculeId];
            if (item) {
                input.value = item.duree.toString();
            }
        });
    }
};

// Formater un prix en euros
const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

// Afficher une alerte
const showAlert = (message, type = 'info') => {
    const alertContainer = document.querySelector('.alert-container');
    if (!alertContainer) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertContainer.appendChild(alert);

    // Supprimer l'alerte après 5 secondes
    setTimeout(() => {
        alert.remove();
    }, 5000);
};

// Gestionnaires d'événements
document.addEventListener('DOMContentLoaded', () => {
    // Rafraîchir l'état initial du panier
    refreshCart();

    // Gérer les boutons "Ajouter au panier"
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const vehiculeId = button.dataset.vehiculeId;
            const type = button.dataset.type;
            const quantite = parseInt(button.dataset.quantite || 1);
            const duree = parseInt(button.dataset.duree || null);
            
            addToCart(vehiculeId, type, quantite, duree);
        });
    });

    // Gérer les changements de quantité
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', (e) => {
            const vehiculeId = input.dataset.vehiculeId;
            const type = input.dataset.type;
            const quantite = parseInt(input.value);
            
            updateQuantity(vehiculeId, type, quantite);
        });
    });

    // Gérer les changements de durée de location
    document.querySelectorAll('.duration-input').forEach(input => {
        input.addEventListener('change', (e) => {
            const vehiculeId = input.dataset.vehiculeId;
            const duree = parseInt(input.value);
            
            updateDuration(vehiculeId, duree);
        });
    });

    // Gérer les boutons de suppression
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const vehiculeId = button.dataset.vehiculeId;
            const type = button.dataset.type;
            
            if (confirm('Êtes-vous sûr de vouloir retirer cet article du panier ?')) {
                removeFromCart(vehiculeId, type);
            }
        });
    });

    // Gérer le bouton "Vider le panier"
    const emptyCartButton = document.querySelector('.empty-cart');
    if (emptyCartButton) {
        emptyCartButton.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
                emptyCart();
            }
        });
    }

    // Gérer le bouton "Valider la commande"
    const validateCartButton = document.querySelector('.validate-cart');
    if (validateCartButton) {
        validateCartButton.addEventListener('click', (e) => {
            e.preventDefault();
            validateCart();
        });
    }
}); 