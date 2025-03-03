// Fonction utilitaire pour les requêtes AJAX
async function sendRequest(action, data = {}) {
    try {
        const formData = new FormData();
        formData.append('action', action);
        
        // Ajouter toutes les données au FormData
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });

        const response = await fetch('includes/ajax_handler.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.error) {
            throw new Error(result.error);
        }
        
        return result;
    } catch (error) {
        console.error('Erreur:', error);
        throw error;
    }
}

// Recherche de véhicules
async function searchVehicles(filters = {}) {
    try {
        const result = await sendRequest('search_vehicles', filters);
        displayVehicles(result.vehicles);
    } catch (error) {
        showError("Erreur lors de la recherche des véhicules");
    }
}

// Affichage des véhicules
function displayVehicles(vehicles) {
    const container = document.getElementById('vehicles-container');
    if (!container) return;

    container.innerHTML = vehicles.map(vehicle => `
        <div class="vehicle-card">
            <h3>${vehicle.marque} ${vehicle.modele}</h3>
            <p>Année: ${vehicle.annee}</p>
            <p>Prix: ${formatPrice(vehicle.prix)}</p>
            <p>Kilométrage: ${vehicle.kilometrage} km</p>
            <div class="vehicle-actions">
                ${vehicle.disponible_location ? 
                    `<button onclick="initLocation(${vehicle.id_vehicule})">Louer</button>` : ''}
                <button onclick="initRdv(${vehicle.id_vehicule})">Prendre RDV</button>
                <button onclick="toggleFavorite(${vehicle.id_vehicule})">
                    <i class="fas fa-heart"></i>
                </button>
            </div>
        </div>
    `).join('');
}

// Gestion des favoris
async function toggleFavorite(produitId) {
    try {
        const result = await sendRequest('add_to_favorites', { produit_id: produitId });
        if (result.success) {
            const button = event.target.closest('button');
            button.classList.toggle('active', result.status === 'added');
            showSuccess(result.status === 'added' ? 'Ajouté aux favoris' : 'Retiré des favoris');
        }
    } catch (error) {
        showError("Vous devez être connecté pour ajouter aux favoris");
    }
}

// Création de rendez-vous
async function initRdv(vehiculeId) {
    const dateRdv = document.getElementById('rdv-date').value;
    if (!dateRdv) {
        showError("Veuillez sélectionner une date");
        return;
    }

    try {
        const result = await sendRequest('create_rdv', {
            vehicule_id: vehiculeId,
            date_rdv: dateRdv
        });
        
        if (result.success) {
            showSuccess("Rendez-vous créé avec succès");
            // Fermer le modal ou rediriger
        }
    } catch (error) {
        showError("Erreur lors de la création du rendez-vous");
    }
}

// Création de location
async function initLocation(vehiculeId) {
    const dateDebut = document.getElementById('location-debut').value;
    const dateFin = document.getElementById('location-fin').value;
    const tarifTotal = calculateTarifTotal(); // À implémenter selon vos besoins

    if (!dateDebut || !dateFin) {
        showError("Veuillez sélectionner les dates");
        return;
    }

    try {
        const result = await sendRequest('create_location', {
            vehicule_id: vehiculeId,
            date_debut: dateDebut,
            date_fin: dateFin,
            tarif_total: tarifTotal
        });
        
        if (result.success) {
            showSuccess("Location créée avec succès");
            // Rediriger vers la page de confirmation
        }
    } catch (error) {
        showError("Erreur lors de la création de la location");
    }
}

// Envoi de message de contact
async function submitContact(event) {
    event.preventDefault();
    const form = event.target;
    const formData = {
        nom: form.nom.value,
        prenom: form.prenom.value,
        email: form.email.value,
        telephone: form.telephone.value,
        sujet: form.sujet.value,
        message: form.message.value
    };

    try {
        const result = await sendRequest('submit_contact', formData);
        if (result.success) {
            showSuccess("Message envoyé avec succès");
            form.reset();
        }
    } catch (error) {
        showError("Erreur lors de l'envoi du message");
    }
}

// Ajout d'avis
async function submitReview(event) {
    event.preventDefault();
    const form = event.target;
    const formData = {
        produit_id: form.produit_id.value,
        note: form.note.value,
        commentaire: form.commentaire.value
    };

    try {
        const result = await sendRequest('add_review', formData);
        if (result.success) {
            showSuccess("Avis ajouté avec succès");
            form.reset();
        }
    } catch (error) {
        showError("Erreur lors de l'ajout de l'avis");
    }
}

// Création de ticket support
async function createSupportTicket(event) {
    event.preventDefault();
    const form = event.target;
    const formData = {
        sujet: form.sujet.value,
        message: form.message.value
    };

    try {
        const result = await sendRequest('create_support_ticket', formData);
        if (result.success) {
            showSuccess("Ticket créé avec succès");
            form.reset();
        }
    } catch (error) {
        showError("Erreur lors de la création du ticket");
    }
}

// Chargement des accessoires
async function loadAccessories(categorie = null) {
    try {
        const result = await sendRequest('get_accessories', { categorie });
        displayAccessories(result.accessories);
    } catch (error) {
        showError("Erreur lors du chargement des accessoires");
    }
}

// Affichage des accessoires
function displayAccessories(accessories) {
    const container = document.getElementById('accessories-container');
    if (!container) return;

    container.innerHTML = accessories.map(accessory => `
        <div class="accessory-card">
            <h3>${accessory.nom}</h3>
            <p>${accessory.description}</p>
            <p>Prix: ${formatPrice(accessory.prix)}</p>
            <p>Stock: ${accessory.stock}</p>
            <button onclick="addToCart(${accessory.id_accessoire})">
                Ajouter au panier
            </button>
        </div>
    `).join('');
}

// Fonctions utilitaires
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

function showSuccess(message) {
    // Implémenter selon votre système de notification
    alert(message);
}

function showError(message) {
    // Implémenter selon votre système de notification
    alert(message);
}

// Initialisation des événements
document.addEventListener('DOMContentLoaded', function() {
    // Formulaire de recherche de véhicules
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const filters = {
                marque: this.marque.value,
                prix_min: this.prix_min.value,
                prix_max: this.prix_max.value,
                disponible_location: this.disponible_location.checked
            };
            searchVehicles(filters);
        });
    }

    // Formulaire de contact
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', submitContact);
    }

    // Formulaire d'avis
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', submitReview);
    }

    // Formulaire de support
    const supportForm = document.getElementById('support-form');
    if (supportForm) {
        supportForm.addEventListener('submit', createSupportTicket);
    }

    // Chargement initial des véhicules
    searchVehicles();

    // Chargement initial des accessoires
    loadAccessories();
}); 