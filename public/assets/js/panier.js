document.addEventListener('DOMContentLoaded', function() {
    // Gestion des boutons de quantité
    document.querySelectorAll('.quantity-form').forEach(form => {
        const minusBtn = form.querySelector('.minus');
        const plusBtn = form.querySelector('.plus');
        const input = form.querySelector('.quantity-input');

        minusBtn.addEventListener('click', () => {
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
                form.submit();
            }
        });

        plusBtn.addEventListener('click', () => {
            const currentValue = parseInt(input.value);
            if (currentValue < 99) {
                input.value = currentValue + 1;
                form.submit();
            }
        });

        // Mise à jour automatique lors de la modification manuelle
        input.addEventListener('change', () => {
            const value = parseInt(input.value);
            if (value < 1) input.value = 1;
            if (value > 99) input.value = 99;
            form.submit();
        });
    });

    // Confirmation pour vider le panier
    document.querySelector('.clear-form')?.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir vider votre panier ?')) {
            e.preventDefault();
        }
    });

    // Confirmation pour supprimer un article
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir retirer cet article du panier ?')) {
                e.preventDefault();
            }
        });
    });
}); 