<?php
// Inclusion du fichier d'initialisation
require_once '../../../includes/init.php';

// Variables de la page
$pageTitle = "Contact";
$pageDescription = "Contactez Teran'Cars pour toute question concernant nos véhicules";
$currentPage = 'contact';
$additionalCss = ['css/contact.css'];

// Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Ajouter le traitement du formulaire
}

// Début de la mise en mémoire tampon
ob_start();
?>

<section class="contact-header">
    <div class="container">
        <h1>Contactez-nous</h1>
        <p>Notre équipe est à votre disposition pour répondre à toutes vos questions</p>
    </div>
</section>

<section class="contact-content">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Notre adresse</h3>
                    <p>123 Avenue des Véhicules<br>75000 Paris, France</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-phone"></i>
                    <h3>Téléphone</h3>
                    <p>+33 1 23 45 67 89</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@terancars.fr</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3>Horaires d'ouverture</h3>
                    <p>Lundi - Vendredi : 9h - 18h<br>Samedi : 10h - 16h</p>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Envoyez-nous un message</h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="nom">Nom complet</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone">
                    </div>
                    
                    <div class="form-group">
                        <label for="sujet">Sujet</label>
                        <select id="sujet" name="sujet" required>
                            <option value="">Choisissez un sujet</option>
                            <option value="information">Demande d'information</option>
                            <option value="rdv">Prise de rendez-vous</option>
                            <option value="devis">Demande de devis</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Envoyer le message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="map-section">
    <div class="container">
        <h2>Notre localisation</h2>
        <div class="map-container">
            <!-- Remplacer par une vraie carte Google Maps -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.292292615509614!3d48.85837007928757!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1647874587931!5m2!1sfr!2sfr" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 