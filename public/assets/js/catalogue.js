document.addEventListener('DOMContentLoaded', () => {
    // Gestion du formulaire de filtres
    const filtresForm = document.getElementById('filtres-form');
    const resetButton = document.getElementById('reset-filtres');

    if (filtresForm) {
        // Mise à jour automatique lors du changement de valeur
        filtresForm.querySelectorAll('select, input').forEach(input => {
            input.addEventListener('change', () => {
                if (input.type !== 'number' || input.value !== '') {
                    filtresForm.submit();
                }
            });
        });

        // Gestion du bouton reset
        resetButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = window.location.pathname;
        });

        // Validation des champs de prix et d'année
        const prixMin = document.querySelector('input[name="prix_min"]');
        const prixMax = document.querySelector('input[name="prix_max"]');
        const anneeMin = document.querySelector('input[name="annee_min"]');
        const anneeMax = document.querySelector('input[name="annee_max"]');

        // Validation du prix
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

        // Validation de l'année
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
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        observer.observe(card);
    });
}); 