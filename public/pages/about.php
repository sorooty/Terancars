<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = "À propos de TeranCar";
$pageDescription = "Découvrez l'histoire et les valeurs de TeranCar, votre partenaire de confiance pour l'achat et la location de véhicules.";
$currentPage = 'about';

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="about-container">
    <div class="about-header">
        <h1>À propos de TeranCar</h1>
        <p class="subtitle">Votre partenaire de confiance depuis 2024</p>
    </div>

    <div class="about-content">
        <section class="about-section">
            <h2>Notre Histoire</h2>
            <p>TeranCar est né d'une vision simple : rendre l'achat et la location de véhicules plus accessible et transparent pour tous. Fondée en 2024, notre entreprise s'est rapidement imposée comme un acteur majeur du marché automobile en France.</p>
        </section>

        <section class="about-section">
            <h2>Notre Mission</h2>
            <p>Nous nous engageons à :</p>
            <ul>
                <li>Offrir un service client exceptionnel</li>
                <li>Proposer une large gamme de véhicules de qualité</li>
                <li>Garantir des prix transparents et compétitifs</li>
                <li>Assurer une expérience d'achat et de location simple et agréable</li>
            </ul>
        </section>

        <section class="about-section">
            <h2>Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Confiance</h3>
                    <p>Nous construisons des relations durables basées sur la confiance et la transparence.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-star"></i>
                    <h3>Excellence</h3>
                    <p>Nous visons l'excellence dans chaque aspect de notre service.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-heart"></i>
                    <h3>Passion</h3>
                    <p>Notre passion pour l'automobile guide chacune de nos actions.</p>
                </div>
            </div>
        </section>

        <section class="about-section">
            <h2>Notre Équipe</h2>
            <p>Notre équipe est composée de professionnels passionnés et expérimentés, dédiés à vous offrir le meilleur service possible. Chaque membre de notre équipe partage notre engagement envers l'excellence et la satisfaction client.</p>
        </section>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 