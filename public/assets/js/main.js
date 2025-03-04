// Slider des offres
document.addEventListener('DOMContentLoaded', function() {
    const sliderWrapper = document.querySelector('.offers-wrapper');
    const prevButton = document.querySelector('.slider-arrow.prev');
    const nextButton = document.querySelector('.slider-arrow.next');
    const cardWidth = 280 + 16; // Largeur de la carte + gap

    if (sliderWrapper && prevButton && nextButton) {
        prevButton.addEventListener('click', () => {
            sliderWrapper.scrollLeft -= cardWidth;
        });

        nextButton.addEventListener('click', () => {
            sliderWrapper.scrollLeft += cardWidth;
        });

        // Vérifier si on peut scroller
        function checkScroll() {
            const canScrollLeft = sliderWrapper.scrollLeft > 0;
            const canScrollRight = sliderWrapper.scrollLeft < (sliderWrapper.scrollWidth - sliderWrapper.clientWidth);

            prevButton.style.opacity = canScrollLeft ? '1' : '0.5';
            nextButton.style.opacity = canScrollRight ? '1' : '0.5';
        }

        sliderWrapper.addEventListener('scroll', checkScroll);
        window.addEventListener('resize', checkScroll);
        checkScroll(); // Vérification initiale
    }

    // Animation au défilement
    function reveal() {
        const reveals = document.querySelectorAll('.section-title, .offer-card, .brand-logo');
        
        reveals.forEach(element => {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', reveal);
    reveal(); // Vérification initiale
});

// Ajout de classes pour les animations au chargement
document.addEventListener('DOMContentLoaded', function() {
    const hero = document.querySelector('.hero-content');
    if (hero) {
        hero.classList.add('fade-in');
    }
}); 