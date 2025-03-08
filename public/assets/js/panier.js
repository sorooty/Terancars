document.addEventListener('DOMContentLoaded', () => {
    // Gestion des quantités
    const quantityInputs = document.querySelectorAll('.item-quantity input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', handleQuantityChange);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleQuantityChange.call(input);
            }
        });
    });

    // Gestion des suppressions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', handleDelete);
    });

    // Gestion du formulaire de commande
    const orderForm = document.querySelector('#order-form');
    if (orderForm) {
        orderForm.addEventListener('submit', handleOrder);
    }
});

function handleQuantityChange() {
    const vehicleId = this.dataset.vehicleId;
    const newQuantity = parseInt(this.value);
    const maxQuantity = parseInt(this.dataset.maxQuantity);

    // Validation de la quantité
    if (isNaN(newQuantity) || newQuantity < 1) {
        this.value = 1;
        showAlert('La quantité doit être supérieure à 0', 'danger');
        return;
    }

    if (newQuantity > maxQuantity) {
        this.value = maxQuantity;
        showAlert(`La quantité maximum disponible est de ${maxQuantity}`, 'danger');
        return;
    }

    // Mise à jour de la quantité via AJAX
    fetch('/DaCar/public/pages/panier.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&id_vehicule=${vehicleId}&quantity=${newQuantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay(data);
            showAlert('Quantité mise à jour avec succès', 'success');
        } else {
            showAlert(data.message || 'Erreur lors de la mise à jour', 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Une erreur est survenue', 'danger');
    });
}

function handleDelete(e) {
            e.preventDefault();
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
        return;
    }

    const vehicleId = this.dataset.vehicleId;

    fetch('/DaCar/public/pages/panier.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&id_vehicule=${vehicleId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const itemElement = this.closest('.panier-item');
            itemElement.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => {
                itemElement.remove();
                updateCartDisplay(data);
                showAlert('Article supprimé avec succès', 'success');
                
                // Vérifier si le panier est vide
                if (document.querySelectorAll('.panier-item').length === 0) {
                    location.reload(); // Recharger pour afficher le message de panier vide
                }
            }, 300);
        } else {
            showAlert(data.message || 'Erreur lors de la suppression', 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Une erreur est survenue', 'danger');
    });
}

function handleOrder(e) {
                e.preventDefault();

    if (!confirm('Voulez-vous confirmer votre commande ?')) {
        return;
    }

    fetch('/DaCar/public/pages/panier.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=command'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Commande effectuée avec succès !', 'success');
            setTimeout(() => {
                window.location.href = '/DaCar/public/pages/commandes.php';
            }, 1500);
        } else {
            showAlert(data.message || 'Erreur lors de la commande', 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Une erreur est survenue', 'danger');
    });
}

function updateCartDisplay(data) {
    // Mise à jour du total
    const totalElement = document.querySelector('.total-amount');
    if (totalElement && data.total !== undefined) {
        totalElement.textContent = `${data.total.toFixed(2)} €`;
    }

    // Mise à jour du compteur dans le header si présent
    const cartCounter = document.querySelector('#cart-counter');
    if (cartCounter && data.itemCount !== undefined) {
        cartCounter.textContent = data.itemCount;
        if (data.itemCount === 0) {
            cartCounter.style.display = 'none';
        } else {
            cartCounter.style.display = 'inline-block';
        }
    }
}

function showAlert(message, type = 'success') {
    const alertsContainer = document.querySelector('.alerts-container');
    if (!alertsContainer) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertsContainer.appendChild(alert);

    // Auto-suppression pour les alertes de succès
    if (type === 'success') {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 3000);
    }
}

// Ajout de l'animation de sortie pour les éléments supprimés
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
`;
document.head.appendChild(style); 