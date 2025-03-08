document.addEventListener('DOMContentLoaded', function() {
    // Sélecteurs
    const searchInput = document.getElementById('search-input');
    const sortButtons = document.querySelectorAll('.sort-btn');
    const vehicleCards = document.querySelectorAll('.vehicle-card');
    const catalogueGrid = document.querySelector('.catalogue-grid');

    // Fonction de recherche
    function filterVehicles() {
        const searchTerm = searchInput.value.toLowerCase();
        
        vehicleCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const specs = card.querySelector('.vehicle-specs').textContent.toLowerCase();
            const shouldShow = title.includes(searchTerm) || specs.includes(searchTerm);
            
            card.style.display = shouldShow ? 'block' : 'none';
        });
    }

    // Fonction de tri
    function sortVehicles(sortType) {
        const cards = Array.from(vehicleCards);
        
        cards.sort((a, b) => {
            const priceA = parseFloat(a.querySelector('.vehicle-price').textContent.replace(/[^0-9]/g, ''));
            const priceB = parseFloat(b.querySelector('.vehicle-price').textContent.replace(/[^0-9]/g, ''));
            
            switch(sortType) {
                case 'price-asc':
                    return priceA - priceB;
                case 'price-desc':
                    return priceB - priceA;
                case 'newest':
                    const yearA = parseInt(a.querySelector('.vehicle-year').textContent);
                    const yearB = parseInt(b.querySelector('.vehicle-year').textContent);
                    return yearB - yearA;
                default:
                    return 0;
            }
        });

        // Réinsertion des éléments triés
        cards.forEach(card => catalogueGrid.appendChild(card));
    }

    // Event listeners
    searchInput.addEventListener('input', filterVehicles);

    sortButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Mise à jour des classes active
            sortButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Tri des véhicules
            sortVehicles(button.dataset.sort);
        });
    });
}); 