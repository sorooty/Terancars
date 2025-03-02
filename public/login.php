<?php
/**
 * Page de connexion
 * Permet aux utilisateurs de se connecter à leur compte
 * Adapté à la structure de base de données optimisée
 */

// Définition du titre de la page
$pageTitle = "Connexion";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Vérifier si la table utilisateurs existe
$tableUtilisateursExists = tableExists($conn, 'utilisateurs');

// Initialisation des variables
$email = "";
$password = "";
$errors = [];
$success = false;
$debugMode = isset($_GET['debug']) && $_GET['debug'] == 1;

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validation des champs
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }
    
    // Si pas d'erreurs, vérifier les identifiants
    if (empty($errors) && $tableUtilisateursExists) {
        // Préparation de la requête
        $query = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Vérifier le mot de passe
            if (password_verify($password, $user['mot_de_passe'])) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id_utilisateur'];
                
                // Vérifier si le champ prénom existe
                if (isset($user['prenom'])) {
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                } else {
                    $_SESSION['user_name'] = $user['nom'];
                }
                
                // Vérifier si le champ role existe
                if (isset($user['role'])) {
                    $_SESSION['user_role'] = $user['role'];
                } else {
                    $_SESSION['user_role'] = 'client'; // Rôle par défaut
                }
                
                // Si "Se souvenir de moi" est coché, créer un cookie
                if ($remember) {
                    $token = bin2hex(random_bytes(32)); // Générer un token sécurisé
                    $expiry = time() + (30 * 24 * 60 * 60); // 30 jours
                    
                    // Stocker le token en base de données (à implémenter)
                    // $query = "INSERT INTO user_tokens (user_id, token, expiry) VALUES (?, ?, ?)";
                    
                    // Créer le cookie
                    setcookie('remember_token', $token, $expiry, '/', '', true, true);
                }
                
                // Enregistrer la connexion dans l'historique (optionnel)
                $ip = $_SERVER['REMOTE_ADDR'];
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                
                // Créer une notification pour l'utilisateur
                setAlert("Connexion réussie. Bienvenue " . $_SESSION['user_name'] . " !", "success");
                
                // Redirection
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    redirect($redirect);
                } else {
                    redirect('index.php');
                }
                exit();
            } else {
                $errors[] = "Mot de passe incorrect";
            }
        } else {
            $errors[] = "Aucun compte trouvé avec cet email";
        }
    } elseif (!$tableUtilisateursExists) {
        $errors[] = "La table 'utilisateurs' n'existe pas dans la base de données.";
    }
}

// Afficher les informations de débogage si demandé
if ($debugMode) {
    debugDatabase();
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Connectez-vous à votre compte Terancar</p>
        </div>
        
        <?php if (!empty($errors)) { ?>
            <div class="error-list">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        
        <?php if (!$tableUtilisateursExists && !$debugMode) { ?>
            <div class="error-list">
                <p>La configuration de la base de données n'est pas complète. Veuillez contacter l'administrateur.</p>
                <p><a href="login.php?debug=1">Afficher les informations de débogage</a></p>
            </div>
        <?php } ?>
        
        <form action="login.php<?php echo $debugMode ? '?debug=1' : ''; ?>" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </div>
            
            <div class="auth-links">
                <a href="forgot-password.php" class="forgot-password">Mot de passe oublié ?</a>
                <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </form>
        
        <div class="social-login">
            <p>Ou connectez-vous avec</p>
            <div class="social-buttons">
                <a href="#" class="btn btn-social btn-facebook">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="#" class="btn btn-social btn-google">
                    <i class="fab fa-google"></i> Google
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Ajout des styles spécifiques à la page de connexion -->
<style>
.auth-container {
    width: 100%;
    max-width: 500px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.auth-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: #666;
    font-size: 1rem;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #555;
}

.form-group input[type="email"],
.form-group input[type="password"] {
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.remember-me {
    flex-direction: row;
    align-items: center;
    gap: 0.5rem;
}

.btn-block {
    width: 100%;
    padding: 0.8rem;
    font-size: 1rem;
}

.auth-links {
    text-align: center;
    margin-top: 1.5rem;
}

.auth-links a {
    color: #007bff;
}

.auth-links p {
    margin-top: 0.8rem;
    color: #666;
}

.forgot-password {
    font-size: 0.9rem;
}

.social-login {
    margin-top: 2rem;
    text-align: center;
    border-top: 1px solid #eee;
    padding-top: 1.5rem;
}

.social-login p {
    color: #666;
    margin-bottom: 1rem;
}

.social-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-social {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
}

.btn-facebook {
    background-color: #3b5998;
    color: #fff;
}

.btn-facebook:hover {
    background-color: #2d4373;
    color: #fff;
}

.btn-google {
    background-color: #db4437;
    color: #fff;
}

.btn-google:hover {
    background-color: #c53727;
    color: #fff;
}

.error-list {
    background-color: #f8d7da;
    color: #721c24;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
}

.error-list ul {
    list-style-type: disc;
    margin-left: 1.5rem;
}
</style>

<?php include '../includes/footer.php'; ?>