<?php
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = "Nous contacter";
$pageDescription = "Contactez TeranCar pour toute question ou demande d'information";
$currentPage = 'contact';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $errors = [];

    // Validation
    if (empty($nom)) $errors['nom'] = "Le nom est requis";
    if (empty($prenom)) $errors['prenom'] = "Le prénom est requis";
    if (empty($email)) $errors['email'] = "L'email est requis";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "L'email n'est pas valide";
    if (empty($sujet)) $errors['sujet'] = "Le sujet est requis";
    if (empty($message)) $errors['message'] = "Le message est requis";

    // Si pas d'erreurs, enregistrement dans la base de données
    if (empty($errors)) {
        try {
            $query = "INSERT INTO messages (nom, prenom, email, telephone, sujet, message) 
                     VALUES (:nom, :prenom, :email, :telephone, :sujet, :message)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':sujet' => $sujet,
                ':message' => $message
            ]);

            $_SESSION['success_message'] = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
            header('Location: ' . url('contact'));
            exit;
        } catch (PDOException $e) {
            error_log("Erreur d'insertion du message: " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer.";
        }
    }
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <div class="contact-page">
        <div class="contact-header">
            <h1>Contactez-nous</h1>
            <p>Notre équipe est à votre disposition pour répondre à toutes vos questions.</p>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message'] ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'] ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="contact-content">
            <div class="contact-form">
                <form method="POST" action="<?= url('contact') ?>">
                    <div class="form-row">
                        <div class="form-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
                            <?php if (isset($errors['nom'])): ?>
                                <span class="error-message"><?= $errors['nom'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group <?= isset($errors['prenom']) ? 'has-error' : '' ?>">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($prenom ?? '') ?>" required>
                            <?php if (isset($errors['prenom'])): ?>
                                <span class="error-message"><?= $errors['prenom'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <span class="error-message"><?= $errors['email'] ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($telephone ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group <?= isset($errors['sujet']) ? 'has-error' : '' ?>">
                        <label for="sujet">Sujet *</label>
                        <input type="text" id="sujet" name="sujet" value="<?= htmlspecialchars($sujet ?? '') ?>" required>
                        <?php if (isset($errors['sujet'])): ?>
                            <span class="error-message"><?= $errors['sujet'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= isset($errors['message']) ? 'has-error' : '' ?>">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" rows="6" required><?= htmlspecialchars($message ?? '') ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <span class="error-message"><?= $errors['message'] ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>

            <div class="contact-info">
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Notre adresse</h3>
                    <p>123 Avenue des Champs-Élysées<br>75008 Paris, France</p>
                </div>

                <div class="info-card">
                    <i class="fas fa-phone"></i>
                    <h3>Téléphone</h3>
                    <p>+33 1 23 45 67 89</p>
                </div>

                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@terancar.fr</p>
                </div>

                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3>Horaires d'ouverture</h3>
                    <p>Lundi - Vendredi : 9h - 19h<br>
                       Samedi : 10h - 18h<br>
                       Dimanche : Fermé</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 