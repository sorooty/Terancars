<?php
require_once __DIR__ . '/../../../includes/auth.php';

// Redirection si déjà connecté
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Tous les champs sont requis";
    } else {
        if ($user = loginUser($email, $password)) {
            // Redirection après connexion
            $redirect = $_SESSION['redirect_after_login'] ?? '/';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}

// Titre de la page
$pageTitle = "Connexion";
$pageDescription = "Connectez-vous à votre compte";
$additionalCss = ['css/auth.css'];

// Inclusion du header
require_once __DIR__ . '/../../../includes/template.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Accédez à votre compte</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </div>

            <div class="auth-links">
                <a href="/pages/auth/reset-password.php">Mot de passe oublié ?</a>
                <a href="/pages/auth/inscription.php">Créer un compte</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?> 