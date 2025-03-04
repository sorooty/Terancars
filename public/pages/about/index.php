<?php
// Inclusion du fichier d'initialisation
require_once '../../../includes/init.php';

// Variables de la page
$pageTitle = "À propos";
$pageDescription = "Découvrez l'histoire de Teran'Cars, où la Teranga sénégalaise rencontre l'excellence automobile";
$currentPage = 'about';
$additionalCss = ['css/about.css'];

// Début de la mise en mémoire tampon
ob_start();
?>

<section class="about-header">
    <div class="container">
        <h1>Notre Histoire</h1>
        <p>La rencontre entre la Teranga sénégalaise et l'excellence automobile</p>
    </div>
</section>

<section class="about-story">
    <div class="container">
        <div class="story-content">
            <div class="story-text">
                <h2>L'origine de Teran'Cars</h2>
                <p>Fondée en 2020, Teran'Cars est née d'une vision unique : allier l'hospitalité légendaire du Sénégal, la "Teranga", au monde de l'automobile. Notre nom, fusion de "Teranga" et "Cars", reflète notre engagement à offrir bien plus qu'un simple service automobile - nous créons une expérience chaleureuse et authentique.</p>
                
                <h2>Notre Vision</h2>
                <p>Chez Teran'Cars, nous croyons que l'achat ou la location d'un véhicule devrait être une expérience aussi agréable qu'enrichissante. Nous apportons la chaleur de l'accueil sénégalais dans chaque interaction, transformant une simple transaction en une relation durable.</p>
                
                <h2>Nos Valeurs</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-handshake"></i>
                        <h3>Teranga</h3>
                        <p>L'hospitalité légendaire du Sénégal guide chacune de nos interactions</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-star"></i>
                        <h3>Excellence</h3>
                        <p>Une sélection rigoureuse de véhicules et un service irréprochable</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-heart"></i>
                        <h3>Authenticité</h3>
                        <p>Des relations sincères et transparentes avec nos clients</p>
                    </div>
                </div>
            </div>
            
            <div class="story-stats">
                <div class="stat-card">
                    <span class="stat-number">3+</span>
                    <span class="stat-label">Années d'expérience</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Clients satisfaits</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">100+</span>
                    <span class="stat-label">Véhicules disponibles</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-team">
    <div class="container">
        <h2>Notre Équipe</h2>
        <p>Une équipe passionnée qui incarne les valeurs de la Teranga au quotidien</p>
        <div class="team-grid">
            <div class="team-member">
                <img src="<?= asset('images/team/ceo.jpg') ?>" alt="CEO">
                <h3>Abdoulaye Watt</h3>
                <p>CEO & Chef de projet</p>
            </div>
            <div class="team-member">
                <img src="<?= asset('images/team/coo.jpg') ?>" alt="COO">
                <h3>Omar ElGhazal</h3>
                <p>COO</p>
            </div>
            <div class="team-member">
                <img src="<?= asset('images/team/cto.jpg') ?>" alt="CTO">
                <h3>Seyni Baldé</h3>
                <p>CTO & Fullstack Engineer</p>
            </div>
            <div class="team-member">
                <img src="<?= asset('images/team/engineer.jpg') ?>" alt="Fullstack Engineer">
                <h3>Thierno Diallo</h3>
                <p>Fullstack Engineer & Logistics</p>
            </div>
            <div class="team-member">
                <img src="<?= asset('images/team/community.jpg') ?>" alt="Community Manager">
                <h3>Jean Tiemtoré</h3>
                <p>Community Manager & Front-end Dev</p>
            </div>
            <div class="team-member">
                <img src="<?= asset('images/team/designer.jpg') ?>" alt="Webdesigner">
                <h3>Hameg Sonia</h3>
                <p>Webdesigner & Front-end Dev</p>
            </div>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 