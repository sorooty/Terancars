<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Redirection si déjà connecté
if (isLoggedIn()) {
    header('Location: ' . url(''));
    exit;
}

// Variables de la page
$pageTitle = "Inscription";
$pageDescription = "Créez votre compte TeranCar pour accéder à nos services";
$currentPage = 'inscription';
$additionalCss = ['css/auth.css'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $errors = [];

    // Validation
    if (empty($nom)) {
        $errors['nom'] = "Le nom est requis";
    }

    if (empty($email)) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "L'email n'est pas valide";
    } else {
        // Vérification si l'email existe déjà
        $query = "SELECT id_utilisateur FROM utilisateurs WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = "Cet email est déjà utilisé";
        }
    }

    if (empty($password)) {
        $errors['password'] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
    }

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    }

    // Si pas d'erreurs, création du compte
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO utilisateurs (nom, email, telephone, mot_de_passe, role) 
                     VALUES (:nom, :email, :telephone, :mot_de_passe, 'client')";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':mot_de_passe' => $hashedPassword
            ]);

            // Récupération de l'ID de l'utilisateur créé
            $userId = $db->lastInsertId();

            // Connexion automatique
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'client';

            $_SESSION['success_message'] = "Votre compte a été créé avec succès !";
            header('Location: ' . url(''));
            exit;
        } catch (PDOException $e) {
            error_log("Erreur d'inscription: " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue lors de la création du compte. Veuillez réessayer.";
        }
    }
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Créer un compte</h1>
                <p>Rejoignez TeranCar et profitez de nos services</p>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error_message'] ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('auth/inscription') ?>" class="auth-form">
                <div class="form-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
                    <label for="nom">Nom complet *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>" required>
                    <?php if (isset($errors['nom'])): ?>
                        <span class="error-message"><?= $errors['nom'] ?></span>
                    <?php endif; ?>
                </div>

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

                <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                    <label for="password">Mot de passe *</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" data-target="password">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error-message"><?= $errors['password'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>">
                    <label for="confirm_password">Confirmer le mot de passe *</label>
                    <div class="password-input">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="toggle-password" data-target="confirm_password">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error-message"><?= $errors['confirm_password'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i>
                        Créer mon compte
                    </button>
                </div>

                <div class="auth-links">
                    <p>Déjà inscrit ? <a href="<?= url('auth/login') ?>">Connectez-vous</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?>
