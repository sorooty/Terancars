document.addEventListener('DOMContentLoaded', () => {
    // Gestion du formulaire de filtres
    const filtresForm = document.getElementById('filtres-form');
    const resetButton = document.getElementById('reset-filtres');
    const allInputs = filtresForm.querySelectorAll('input, select');

    // Fonction pour mettre à jour les filtres
    function updateFilters() {
        const formData = new FormData(filtresForm);
        const params = new URLSearchParams(formData);
        
        // Supprimer les paramètres vides
        for (const [key, value] of params.entries()) {
            if (!value) params.delete(key);
        }
        
        // Rediriger vers la nouvelle URL avec les filtres
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }

    // Écouter les changements sur les selects
    filtresForm.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', updateFilters);
    });

    // Gestion des inputs de prix et d'année
    const rangeInputs = filtresForm.querySelectorAll('.range-inputs input');
    rangeInputs.forEach(input => {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const group = this.closest('.range-inputs');
                const min = group.querySelector('[name$="_min"]');
                const max = group.querySelector('[name$="_max"]');
                
                // Validation des valeurs min/max
                if (min.value && max.value && parseInt(min.value) > parseInt(max.value)) {
                    alert('La valeur minimale ne peut pas être supérieure à la valeur maximale.');
                    this.value = '';
                    return;
                }
                
                updateFilters();
            }, 500); // Délai de 500ms avant mise à jour
        });
    });

    // Gestion de la recherche de modèle
    const modeleInput = filtresForm.querySelector('#modele');
    if (modeleInput) {
        let searchTimer;
        modeleInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                updateFilters();
            }, 500); // Délai de 500ms avant mise à jour
        });
    }

    // Réinitialisation des filtres
    resetButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Réinitialiser tous les champs
        allInputs.forEach(input => {
            if (input.type === 'number' || input.type === 'text') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });
        
        // Rediriger vers la page sans filtres
        window.location.href = window.location.pathname;
    });

    // Gestion des favoris
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const vehicleId = button.dataset.id;
            try {
                const response = await fetch('/DaCar/public/ajax/toggle-favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id_vehicule=${vehicleId}`
                });
                
                const data = await response.json();
                if (data.success) {
                    button.classList.toggle('active');
                    const icon = button.querySelector('i');
                    if (button.classList.contains('active')) {
                        icon.classList.add('fas');
                        icon.classList.remove('far');
                    } else {
                        icon.classList.add('far');
                        icon.classList.remove('fas');
                    }
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        });
    });

    // Animation des cartes de véhicules
    const vehiculeCards = document.querySelectorAll('.vehicule-card');
    vehiculeCards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.5s ease forwards ${index * 0.1}s`;
    });
}); 