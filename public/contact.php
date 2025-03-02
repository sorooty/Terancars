<?php
/**
 * Page de contact
 * Permet aux utilisateurs de contacter l'équipe du site
 */

// Définition du titre de la page
$pageTitle = "Contact";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Initialisation des variables
$nom = $prenom = $email = $telephone = $sujet = $message = "";
$errors = [];
$success = false;

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données
    $nom = cleanInput($_POST['nom'] ?? '');
    $prenom = cleanInput($_POST['prenom'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $telephone = cleanInput($_POST['telephone'] ?? '');
    $sujet = cleanInput($_POST['sujet'] ?? '');
    $message = cleanInput($_POST['message'] ?? '');
    
    // Validation des champs
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }
    
    if (empty($sujet)) {
        $errors[] = "Le sujet est requis";
    }
    
    if (empty($message)) {
        $errors[] = "Le message est requis";
    }
    
    // Si pas d'erreurs, enregistrer le message
    if (empty($errors)) {
        // Vérifier si la table messages existe
        if (!tableExists($conn, 'messages')) {
            // Créer la table si elle n'existe pas
            $createTable = "CREATE TABLE IF NOT EXISTS messages (
                id_message INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL,
                telephone VARCHAR(20),
                sujet VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->query($createTable);
        }
        
        // Préparation de la requête
        $query = "INSERT INTO messages (nom, prenom, email, telephone, sujet, message) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        // Vérifier si la préparation a réussi
        if ($stmt === false) {
            $errors[] = "Erreur de préparation de la requête : " . $conn->error;
        } else {
            $stmt->bind_param("ssssss", $nom, $prenom, $email, $telephone, $sujet, $message);
            
            // Exécution de la requête
            if ($stmt->execute()) {
                $success = true;
                // Réinitialisation des champs
                $nom = $prenom = $email = $telephone = $sujet = $message = "";
            } else {
                $errors[] = "Une erreur est survenue lors de l'envoi du message : " . $stmt->error;
            }
        }
    }
}

// Pré-remplir le sujet si passé en paramètre
if (isset($_GET['sujet']) && $_GET['sujet'] == 'vehicule' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT marque, modele FROM vehicules WHERE id_vehicule = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($vehicule = $result->fetch_assoc()) {
        $sujet = "Demande d'informations - " . $vehicule['marque'] . " " . $vehicule['modele'];
    }
}
?>

<div class="contact-container">
    <div class="contact-header">
        <h1>Contactez-nous</h1>
        <p>Nous sommes à votre écoute pour toute question ou demande d'information</p>
    </div>
    
    <div class="contact-content">
        <div class="contact-info">
            <div class="info-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Notre adresse</h3>
                <p>123 Avenue des Véhicules<br>75000 Paris, France</p>
            </div>
            
            <div class="info-card">
                <i class="fas fa-phone-alt"></i>
                <h3>Téléphone</h3>
                <p>+33 1 23 45 67 89</p>
                <p>Du lundi au vendredi, 9h-18h</p>
            </div>
            
            <div class="info-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>contact@dacar.fr</p>
                <p>Réponse sous 24h</p>
            </div>
            
            <div class="info-card">
                <i class="fas fa-clock"></i>
                <h3>Horaires d'ouverture</h3>
                <p>Lundi - Vendredi: 9h - 18h</p>
                <p>Samedi: 10h - 17h</p>
                <p>Dimanche: Fermé</p>
            </div>
        </div>
        
        <div class="contact-form-container">
            <?php if ($success) { ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <p>Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.</p>
                </div>
            <?php } else { ?>
                <?php if (!empty($errors)) { ?>
                    <div class="error-list">
                        <ul>
                            <?php foreach ($errors as $error) { ?>
                                <li><?php echo $error; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                
                <form action="contact.php" method="POST" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" value="<?php echo $nom; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" value="<?php echo $prenom; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" value="<?php echo $telephone; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="sujet">Sujet *</label>
                        <input type="text" id="sujet" name="sujet" value="<?php echo $sujet; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required><?php echo $message; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Envoyer le message</button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Ajout des styles spécifiques à la page de contact conformes à la charte graphique -->
<style>
.contact-container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto 3rem auto;
}

.contact-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem 0;
}

.contact-header h1 {
    font-size: 2.2rem;
    color: #042345; /* Bleu de la charte graphique */
    margin-bottom: 0.5rem;
}

.contact-header p {
    color: #666;
    font-size: 1.1rem;
}

.contact-content {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}

.contact-info {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

.info-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    text-align: center;
    border-top: 3px solid #B68FB2; /* Violet de la charte graphique */
}

.info-card i {
    font-size: 2rem;
    color: #042345; /* Bleu de la charte graphique */
    margin-bottom: 1rem;
}

.info-card h3 {
    font-size: 1.2rem;
    color: #042345; /* Bleu de la charte graphique */
    margin-bottom: 0.8rem;
}

.info-card p {
    color: #666;
    margin-bottom: 0.3rem;
}

.contact-form-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.contact-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.contact-form .form-group {
    margin-bottom: 1.5rem;
}

.contact-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #042345; /* Bleu de la charte graphique */
}

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: inherit;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.contact-form input:focus,
.contact-form textarea:focus {
    outline: none;
    border-color: #B68FB2; /* Violet de la charte graphique */
}

.contact-form .form-actions {
    margin-top: 1rem;
}

.contact-form button {
    background-color: #042345; /* Bleu de la charte graphique */
    color: white;
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.contact-form button:hover {
    background-color: #B68FB2; /* Violet de la charte graphique */
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 1rem;
    border-radius: 4px;
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.success-message i {
    color: #28a745;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.error-list {
    background-color: #f8d7da;
    color: #721c24;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
}

.error-list ul {
    list-style-position: inside;
    margin-left: 1rem;
}

@media (max-width: 768px) {
    .contact-content {
        grid-template-columns: 1fr;
    }
    
    .contact-form .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../includes/footer.php'; ?>