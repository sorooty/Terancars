document.addEventListener('DOMContentLoaded', () => {
    // Gestion du formulaire de filtres
    const filtresForm = document.getElementById('filtres-form');
    const resetButton = document.getElementById('reset-filtres');

    if (filtresForm) {
        // Empêcher la soumission automatique sur les changements
        filtresForm.querySelectorAll('select, input').forEach(input => {
            input.addEventListener('change', (e) => {
                e.preventDefault(); // Empêche la soumission automatique
            });
        });

        // Validation des champs de prix et d'année
        const prixMin = document.querySelector('input[name="prix_min"]');
        const prixMax = document.querySelector('input[name="prix_max"]');
        const anneeMin = document.querySelector('input[name="annee_min"]');
        const anneeMax = document.querySelector('input[name="annee_max"]');

        // Validation du prix
        if (prixMin && prixMax) {
            prixMin.addEventListener('change', () => {
                if (prixMax.value && parseInt(prixMin.value) > parseInt(prixMax.value)) {
                    prixMin.value = prixMax.value;
                }
            });

            prixMax.addEventListener('change', () => {
                if (prixMin.value && parseInt(prixMax.value) < parseInt(prixMin.value)) {
                    prixMax.value = prixMin.value;
                }
            });
        }

        // Validation de l'année
        if (anneeMin && anneeMax) {
            anneeMin.addEventListener('change', () => {
                if (anneeMax.value && parseInt(anneeMin.value) > parseInt(anneeMax.value)) {
                    anneeMin.value = anneeMax.value;
                }
            });

            anneeMax.addEventListener('change', () => {
                if (anneeMin.value && parseInt(anneeMax.value) < parseInt(anneeMin.value)) {
                    anneeMax.value = anneeMin.value;
                }
            });
        }

        // Gestion du bouton reset
        resetButton.addEventListener('click', (e) => {
            e.preventDefault();
            filtresForm.querySelectorAll('select, input').forEach(input => {
                input.value = '';
            });
            filtresForm.submit();
        });
    }

    // Gestion des favoris
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    favoriteButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const vehicleId = button.dataset.id;
            try {
                const response = await fetch('/ajax/toggle-favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `vehicle_id=${vehicleId}`
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
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '50px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    vehiculeCards.forEach(card => {
        observer.observe(card);
    });
});