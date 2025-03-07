<?php
// Inclusion du fichier d'initialisation
require_once '../../../includes/init.php';

// Variables de la page
$pageTitle = "Contact";
$pageDescription = "Contactez Teran'Cars pour toute question concernant nos services de transport et de location";
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
                    <p>97 Route de la Corniche<br>Dakar, Sénégal</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-phone"></i>
                    <h3>Téléphone</h3>
                    <p>+221 78 123 45 67<br>+221 33 823 45 67</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@terancars.sn<br>support@terancars.sn</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3>Horaires d'ouverture</h3>
                    <p>
                        Lundi - Vendredi : 8h - 18h<br>
                        Samedi : 9h - 13h<br>
                        Dimanche : Fermé
                    </p>
                </div>

                <div class="social-media">
                    <h3>Suivez-nous</h3>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/Terancars" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/terancars_sn" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/terancars" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Envoyez-nous un message</h2>
                <p>Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais.</p>
                
                <form action="" method="POST" id="contactForm">
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
                            <option value="">Sélectionnez un sujet</option>
                            <option value="achat">Achat de véhicule</option>
                            <option value="location">Location de véhicule</option>
                            <option value="service">Service après-vente</option>
                            <option value="information">Demande d'information</option>
                            <option value="autre">Autre demande</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="map-section">
    <div class="container">
        <h2>Notre localisation</h2>
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3858.6101950917187!2d-17.51091192489129!3d14.734616985767762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTTCsDQ0JzA0LjYiTiAxN8KwMzAnMzAuMCJX!5e0!3m2!1sfr!2ssn!4v1741254154296!5m2!1sfr!2ssn" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
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