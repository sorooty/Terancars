<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Redirection si déjà connecté
if (isLoggedIn()) {
    header('Location: ' . url(''));
    exit;
}

// Variables de la page
$pageTitle = "Connexion";
$pageDescription = "Connectez-vous à votre compte TeranCar";
$currentPage = 'login';
$additionalCss = ['css/auth.css'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    $errors = [];

    // Validation
    if (empty($email)) {
        $errors['email'] = "L'email est requis";
    }

    if (empty($password)) {
        $errors['password'] = "Le mot de passe est requis";
    }

    // Si pas d'erreurs, tentative de connexion
    if (empty($errors)) {
        try {
            $query = "SELECT id_utilisateur, email, mot_de_passe, role FROM utilisateurs WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id_utilisateur'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                if ($remember) {
                    // Création d'un token de connexion automatique
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                    
                    $query = "INSERT INTO auth_tokens (user_id, token, expiry) VALUES (:user_id, :token, :expiry)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        ':user_id' => $user['id_utilisateur'],
                        ':token' => $token,
                        ':expiry' => $expiry
                    ]);

                    // Stockage du token dans un cookie
                    setcookie('auth_token', $token, strtotime('+30 days'), '/', '', true, true);
                }

                // Redirection vers la page précédente ou l'accueil
                $redirect = $_SESSION['redirect_after_login'] ?? url('');
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            } else {
                $errors['login'] = "Email ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            $_SESSION['error_message'] = "Une erreur est survenue lors de la connexion. Veuillez réessayer.";
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
                <h1>Connexion</h1>
                <p>Accédez à votre compte TeranCar</p>
            </div>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error_message'] ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errors['login'])): ?>
                <div class="alert alert-danger">
                    <?= $errors['login'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('auth/login') ?>" class="auth-form">
                <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error-message"><?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                    <label for="password">Mot de passe</label>
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

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <div class="forgot-password">
                        <a href="<?= url('auth/reset-password') ?>">Mot de passe oublié ?</a>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </button>
                </div>

                <div class="auth-links">
                    <p>Pas encore de compte ? <a href="<?= url('auth/inscription') ?>">Inscrivez-vous !</a></p>
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