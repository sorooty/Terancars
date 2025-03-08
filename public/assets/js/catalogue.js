document.addEventListener('DOMContentLoaded', function() {
    // Éléments du formulaire
    const filtresForm = document.getElementById('filtres-form');
    const marqueSelect = document.getElementById('marque');
    const modeleSelect = document.getElementById('modele');
    const resetBtn = document.getElementById('reset-filtres');
    const trierSelect = document.getElementById('trier');

    // Gestion du changement de marque
    marqueSelect?.addEventListener('change', async function() {
        const marqueId = this.value;
        if (!marqueId) {
            modeleSelect.innerHTML = '<option value="">Tous les modèles</option>';
            modeleSelect.disabled = true;
            return;
        }

        try {
            const response = await fetch(`/DaCar/api/modeles.php?marque_id=${marqueId}`);
            const modeles = await response.json();

            modeleSelect.innerHTML = '<option value="">Tous les modèles</option>';
            modeles.forEach(modele => {
                const option = document.createElement('option');
                option.value = modele.id;
                option.textContent = modele.nom;
                modeleSelect.appendChild(option);
            });
            modeleSelect.disabled = false;
        } catch (error) {
            console.error('Erreur lors du chargement des modèles:', error);
            modeleSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            modeleSelect.disabled = true;
        }
    });

    // Gestion des inputs de prix et année
    const prixInputs = document.querySelectorAll('input[type="number"][id^="prix_"]');
    const anneeInputs = document.querySelectorAll('input[type="number"][id^="annee_"]');

    function validateRange(minInput, maxInput) {
        const min = parseInt(minInput.value) || 0;
        const max = parseInt(maxInput.value) || Infinity;

        if (min > max && max !== 0) {
            maxInput.value = min;
        }
    }

    prixInputs.forEach(input => {
        input.addEventListener('change', () => {
            const isMin = input.id.includes('min');
            const otherInput = document.getElementById(`prix_${isMin ? 'max' : 'min'}`);
            validateRange(
                isMin ? input : otherInput,
                isMin ? otherInput : input
            );
        });
    });

    anneeInputs.forEach(input => {
        input.addEventListener('change', () => {
            const isMin = input.id.includes('min');
            const otherInput = document.getElementById(`annee_${isMin ? 'max' : 'min'}`);
            validateRange(
                isMin ? input : otherInput,
                isMin ? otherInput : input
            );
        });
    });

    // Gestion du tri
    trierSelect?.addEventListener('change', function() {
        filtresForm.submit();
    });

    // Réinitialisation des filtres
    resetBtn?.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Réinitialiser tous les champs
        filtresForm.reset();
        
        // Désactiver le select des modèles
        if (modeleSelect) {
            modeleSelect.innerHTML = '<option value="">Tous les modèles</option>';
            modeleSelect.disabled = true;
        }

        // Soumettre le formulaire
        filtresForm.submit();
    });

    // Gestion des favoris
    const favoriteBtns = document.querySelectorAll('.favorite-btn');
    
    favoriteBtns.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const vehiculeId = this.dataset.vehiculeId;
            const icon = this.querySelector('i');
            
            try {
                const response = await fetch('/DaCar/api/favoris.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ vehicule_id: vehiculeId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Toggle de la classe pour l'icône
                    icon.classList.toggle('fas');
                    icon.classList.toggle('far');
                    
                    // Feedback visuel
                    const toast = document.createElement('div');
                    toast.className = 'toast';
                    toast.textContent = data.message;
                    document.body.appendChild(toast);
                    
                    // Supprimer le toast après 3 secondes
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                }
            } catch (error) {
                console.error('Erreur lors de la gestion des favoris:', error);
            }
        });
    });

    // Animation des cartes au scroll
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const vehiculeCards = document.querySelectorAll('.vehicule-card');
    vehiculeCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        observer.observe(card);
    });
}); 