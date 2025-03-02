<?php
/**
 * Page d'inscription
 * Permet aux utilisateurs de créer un nouveau compte
 */

// Définition du titre de la page
$pageTitle = "Inscription";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Vérifier si la table utilisateurs existe
$tableUtilisateursExists = tableExists($conn, 'utilisateurs');

// Initialisation des variables
$nom = $prenom = $email = $telephone = $adresse = $code_postal = $ville = "";
$errors = [];
$success = false;
$debugMode = isset($_GET['debug']) && $_GET['debug'] == 1;

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données
    $nom = cleanInput($_POST['nom'] ?? '');
    $prenom = cleanInput($_POST['prenom'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $telephone = cleanInput($_POST['telephone'] ?? '');
    $adresse = cleanInput($_POST['adresse'] ?? '');
    $code_postal = cleanInput($_POST['code_postal'] ?? '');
    $ville = cleanInput($_POST['ville'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
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
    } elseif ($tableUtilisateursExists) {
        // Vérifier si l'email existe déjà
        $query = "SELECT id_utilisateur FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        // Vérifier si la préparation a réussi
        if ($stmt === false) {
            if ($debugMode) {
                $errors[] = "Erreur de préparation de la requête SQL (vérification email): " . $conn->error;
            } else {
                $errors[] = "Une erreur est survenue lors de la vérification de l'email. Veuillez réessayer.";
            }
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Cet email est déjà utilisé";
            }
        }
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    }
    
    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Si pas d'erreurs, créer le compte
    if (empty($errors)) {
        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if ($tableUtilisateursExists) {
            // Vérifier si la colonne role existe dans la table utilisateurs
            $colonneRole = columnExists($conn, 'utilisateurs', 'role');
            
            // Préparation de la requête en fonction des colonnes existantes
            if ($colonneRole) {
                $role = 'client'; // Rôle par défaut
                $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                
                // Vérifier si la préparation a réussi
                if ($stmt === false) {
                    if ($debugMode) {
                        $errors[] = "Erreur de préparation de la requête SQL: " . $conn->error;
                    } else {
                        $errors[] = "Une erreur est survenue lors de la création du compte. Veuillez réessayer.";
                    }
                } else {
                    $stmt->bind_param("sssssssss", $nom, $prenom, $email, $hashed_password, $telephone, $adresse, $code_postal, $ville, $role);
                }
            } else {
                $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                
                // Vérifier si la préparation a réussi
                if ($stmt === false) {
                    if ($debugMode) {
                        $errors[] = "Erreur de préparation de la requête SQL: " . $conn->error;
                    } else {
                        $errors[] = "Une erreur est survenue lors de la création du compte. Veuillez réessayer.";
                    }
                } else {
                    $stmt->bind_param("ssssssss", $nom, $prenom, $email, $hashed_password, $telephone, $adresse, $code_postal, $ville);
                }
            }
            
            // Exécution de la requête seulement si la préparation a réussi
            if (!empty($errors)) {
                // Ne rien faire, les erreurs seront affichées
            } elseif ($stmt->execute()) {
                $success = true;
                // Réinitialisation des champs
                $nom = $prenom = $email = $telephone = $adresse = $code_postal = $ville = "";
                
                // Définir un message de succès
                setAlert("Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.", "success");
            } else {
                $errors[] = "Une erreur est survenue lors de la création du compte: " . $conn->error;
                if ($debugMode) {
                    $errors[] = "Erreur SQL: " . $stmt->error;
                }
            }
        } else {
            $errors[] = "La table 'utilisateurs' n'existe pas dans la base de données.";
        }
    }
}

// Afficher les informations de débogage si demandé
if ($debugMode) {
    debugDatabase();
}
?>

<div class="auth-container">
    <?php if ($success) { ?>
        <div class="auth-card">
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>Inscription réussie !</h2>
                <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.</p>
                <a href="login.php" class="btn btn-primary">Se connecter</a>
            </div>
        </div>
    <?php } else { ?>
        <div class="auth-card">
            <div class="auth-header">
                <h1>Créer un compte</h1>
                <p>Rejoignez Terancar pour profiter de tous nos services</p>
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
                    <p><a href="register.php?debug=1">Afficher les informations de débogage</a></p>
                </div>
            <?php } ?>
            
            <?php if ($debugMode) { ?>
                <div class="debug-info">
                    <h3>Informations de débogage</h3>
                    <p>Mode débogage activé. Les erreurs détaillées seront affichées.</p>
                    <?php debugDatabase(); ?>
                </div>
            <?php } ?>
            
            <form action="register.php<?php echo $debugMode ? '?debug=1' : ''; ?>" method="POST" class="auth-form">
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
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?php echo $telephone; ?>">
                </div>
                
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo $adresse; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="code_postal">Code postal</label>
                        <input type="text" id="code_postal" name="code_postal" value="<?php echo $code_postal; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <input type="text" id="ville" name="ville" value="<?php echo $ville; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" required>
                        <small>Au moins 8 caractères</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                    </div>
                </div>
                
                <div class="form-group terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">J'accepte les <a href="#">conditions générales</a> et la <a href="#">politique de confidentialité</a></label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
                </div>
                
                <div class="auth-links">
                    <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
                </div>
            </form>
            
            <div class="social-login">
                <p>Ou inscrivez-vous avec</p>
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
    <?php } ?>
</div>

<!-- Ajout des styles spécifiques à la page d'inscription -->
<style>
.auth-container {
    width: 100%;
    max-width: 700px;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
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

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"],
.form-group input[type="password"] {
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group small {
    color: #666;
    font-size: 0.8rem;
}

.terms {
    flex-direction: row;
    align-items: flex-start;
    gap: 0.8rem;
}

.terms label {
    font-weight: normal;
    font-size: 0.9rem;
}

.terms a {
    color: #007bff;
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
    color: #666;
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

.success-message {
    text-align: center;
    padding: 2rem 0;
}

.success-message i {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 1.5rem;
}

.success-message h2 {
    font-size: 1.8rem;
    color: #333;
    margin-bottom: 1rem;
}

.success-message p {
    color: #666;
    margin-bottom: 2rem;
}

.debug-info {
    background-color: #e2f0ff;
    color: #0c5460;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
    border: 1px solid #bee5eb;
}

.debug-info h3 {
    font-size: 1.2rem;
    color: #0c5460;
    margin-bottom: 0.5rem;
}

.debug-info p {
    color: #0c5460;
    margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>