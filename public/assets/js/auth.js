document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage/masquage des mots de passe
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Validation du formulaire d'inscription
    const inscriptionForm = document.querySelector('form[action*="inscription"]');
    if (inscriptionForm) {
        inscriptionForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                const error = document.createElement('div');
                error.className = 'alert alert-danger';
                error.textContent = 'Les mots de passe ne correspondent pas';
                
                // Suppression des anciennes alertes
                const oldAlerts = document.querySelectorAll('.alert');
                oldAlerts.forEach(alert => alert.remove());
                
                // Ajout de la nouvelle alerte
                this.insertBefore(error, this.firstChild);
                
                // Focus sur le champ de confirmation
                confirmPassword.focus();
            }
        });
    }

    // Animation des messages d'erreur/succès
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.animation = 'slideIn 0.3s ease forwards';
        
        // Auto-suppression après 5 secondes
        if (!alert.classList.contains('alert-danger')) {
            setTimeout(() => {
                alert.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
    });
}); 