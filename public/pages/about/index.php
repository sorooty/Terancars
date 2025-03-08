<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

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
                <p>Teran'Cars est une entreprise de transport et de mobilité basée à Dakar, au Sénégal. Notre nom, fusion de "Teranga" (l'hospitalité légendaire sénégalaise) et "Cars", reflète notre engagement à offrir des solutions de transport fiables et efficaces, imprégnées de la chaleur de l'accueil sénégalais.</p>
                
                <h2>Notre Vision</h2>
                <p>Nous sommes engagés à fournir des solutions de transport fiables et efficaces à nos clients. Notre approche combine l'excellence du service avec la tradition d'hospitalité sénégalaise, créant ainsi une expérience unique pour chaque client.</p>
                
                <h2>Nos Services</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-car"></i>
                        <h3>Location de véhicules</h3>
                        <p>Une flotte moderne et diversifiée pour tous vos besoins</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-truck"></i>
                        <h3>Services de transport</h3>
                        <p>Solutions de transport adaptées à vos exigences</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-route"></i>
                        <h3>Solutions de mobilité</h3>
                        <p>Des options flexibles pour votre mobilité quotidienne</p>
                    </div>
                </div>
            </div>
            
            <div class="story-stats">
                <div class="stat-card">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Support client disponible</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">100+</span>
                    <span class="stat-label">Véhicules disponibles</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number">1000+</span>
                    <span class="stat-label">Clients satisfaits</span>
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
            <?php
            $team_members = [
                [
                    'name' => 'Abdoulaye Watt',
                    'role' => 'CEO & Chef de projet',
                ],
                [
                    'name' => 'Omar ElGhazal',
                    'role' => 'COO',
                ],
                [
                    'name' => 'Seyni Baldé',
                    'role' => 'CTO & Fullstack Engineer',
                ],
                [
                    'name' => 'Thierno Diallo',
                    'role' => 'Fullstack Engineer & Logistics',
                ],
                [
                    'name' => 'Jean Tiemtoré',
                    'role' => 'Community Manager & Front-end Dev',
                ],
                [
                    'name' => 'Hameg Sonia',
                    'role' => 'Webdesigner & Front-end Dev',
                ]
            ];

            foreach ($team_members as $member): ?>
                <div class="team-member">
                    <div class="member-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3><?= htmlspecialchars($member['name']) ?></h3>
                    <p><?= htmlspecialchars($member['role']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 