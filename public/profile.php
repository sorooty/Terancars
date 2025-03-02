<?php
/**
 * Page de profil utilisateur
 * Permet aux utilisateurs de visualiser et modifier leurs informations personnelles
 * Adapté à la structure de base de données optimisée
 */

// Définition du titre de la page
$pageTitle = "Mon Profil";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'profile.php';
    setAlert("Veuillez vous connecter pour accéder à votre profil", "warning");
    redirect('login.php');
    exit();
}

// Vérifier si la table utilisateurs existe
$tableUtilisateursExists = tableExists($conn, 'utilisateurs');
$debugMode = isset($_GET['debug']) && $_GET['debug'] == 1;

// Récupérer les informations de l'utilisateur
$user = null;
$errors = [];
$success = false;

if ($tableUtilisateursExists) {
    $userId = $_SESSION['user_id'];
    $query = "SELECT * FROM utilisateurs WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $errors[] = "Utilisateur non trouvé dans la base de données";
    }
}

// Traitement du formulaire de mise à jour du profil
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Récupération et nettoyage des données
    $nom = cleanInput($_POST['nom'] ?? '');
    $prenom = cleanInput($_POST['prenom'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $telephone = cleanInput($_POST['telephone'] ?? '');
    $adresse = cleanInput($_POST['adresse'] ?? '');
    $code_postal = cleanInput($_POST['code_postal'] ?? '');
    $ville = cleanInput($_POST['ville'] ?? '');
    
    // Validation des champs
    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    }
    
    // Vérifier si l'email existe déjà (sauf si c'est le même que l'utilisateur actuel)
    if (!empty($email) && $email !== $user['email']) {
        $query = "SELECT id_utilisateur FROM utilisateurs WHERE email = ? AND id_utilisateur != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $email, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Cet email est déjà utilisé par un autre compte";
        }
    }
    
    // Si pas d'erreurs, mettre à jour le profil
    if (empty($errors)) {
        // Construire la requête en fonction des colonnes disponibles
        $query = "UPDATE utilisateurs SET nom = ?, email = ?";
        $types = "ss";
        $params = [$nom, $email];
        
        // Ajouter les champs optionnels s'ils existent dans la table
        if (columnExists($conn, 'utilisateurs', 'prenom')) {
            $query .= ", prenom = ?";
            $types .= "s";
            $params[] = $prenom;
        }
        
        if (columnExists($conn, 'utilisateurs', 'telephone')) {
            $query .= ", telephone = ?";
            $types .= "s";
            $params[] = $telephone;
        }
        
        if (columnExists($conn, 'utilisateurs', 'adresse')) {
            $query .= ", adresse = ?";
            $types .= "s";
            $params[] = $adresse;
        }
        
        if (columnExists($conn, 'utilisateurs', 'code_postal')) {
            $query .= ", code_postal = ?";
            $types .= "s";
            $params[] = $code_postal;
        }
        
        if (columnExists($conn, 'utilisateurs', 'ville')) {
            $query .= ", ville = ?";
            $types .= "s";
            $params[] = $ville;
        }
        
        $query .= " WHERE id_utilisateur = ?";
        $types .= "i";
        $params[] = $userId;
        
        // Exécuter la requête
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            // Mettre à jour les informations de session
            $_SESSION['user_name'] = isset($prenom) && !empty($prenom) ? $prenom . ' ' . $nom : $nom;
            
            // Mettre à jour les données utilisateur
            $user['nom'] = $nom;
            $user['email'] = $email;
            if (isset($prenom)) $user['prenom'] = $prenom;
            if (isset($telephone)) $user['telephone'] = $telephone;
            if (isset($adresse)) $user['adresse'] = $adresse;
            if (isset($code_postal)) $user['code_postal'] = $code_postal;
            if (isset($ville)) $user['ville'] = $ville;
            
            $success = true;
            setAlert("Votre profil a été mis à jour avec succès", "success");
        } else {
            $errors[] = "Erreur lors de la mise à jour du profil: " . $conn->error;
        }
    }
}

// Traitement du formulaire de changement de mot de passe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation des champs
    if (empty($current_password)) {
        $errors[] = "Le mot de passe actuel est requis";
    }
    
    if (empty($new_password)) {
        $errors[] = "Le nouveau mot de passe est requis";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères";
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Vérifier le mot de passe actuel
    if (empty($errors)) {
        if (password_verify($current_password, $user['mot_de_passe'])) {
            // Hasher le nouveau mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe
            $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE id_utilisateur = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $hashed_password, $userId);
            
            if ($stmt->execute()) {
                $success = true;
                setAlert("Votre mot de passe a été modifié avec succès", "success");
            } else {
                $errors[] = "Erreur lors de la modification du mot de passe: " . $conn->error;
            }
        } else {
            $errors[] = "Le mot de passe actuel est incorrect";
        }
    }
}

// Afficher les informations de débogage si demandé
if ($debugMode) {
    debugDatabase();
}
?>

<div class="profile-container">
    <div class="profile-header">
        <h1>Mon Profil</h1>
        <p>Gérez vos informations personnelles et vos préférences</p>
    </div>
    
    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error) { ?>
                    <li><?php echo $error; ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    
    <?php if ($success) { ?>
        <div class="alert alert-success">
            <p>Vos informations ont été mises à jour avec succès.</p>
        </div>
    <?php } ?>
    
    <?php if (!$tableUtilisateursExists && !$debugMode) { ?>
        <div class="alert alert-danger">
            <p>La configuration de la base de données n'est pas complète. Veuillez contacter l'administrateur.</p>
            <p><a href="profile.php?debug=1">Afficher les informations de débogage</a></p>
        </div>
    <?php } ?>
    
    <?php if ($user) { ?>
        <div class="profile-tabs">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Informations personnelles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab">Sécurité</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders" role="tab">Mes commandes</a>
                </li>
                <?php if (columnExists($conn, 'utilisateurs', 'role') && $user['role'] === 'admin') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/dashboard.php">Administration</a>
                    </li>
                <?php } ?>
            </ul>
            
            <div class="tab-content" id="profileTabsContent">
                <!-- Onglet Informations personnelles -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <form action="profile.php<?php echo $debugMode ? '?debug=1' : ''; ?>" method="POST" class="profile-form">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" value="<?php echo $user['nom'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" value="<?php echo $user['prenom'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo $user['email'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="telephone">Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" value="<?php echo $user['telephone'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" value="<?php echo $user['adresse'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="code_postal">Code postal</label>
                                <input type="text" id="code_postal" name="code_postal" value="<?php echo $user['code_postal'] ?? ''; ?>">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="ville">Ville</label>
                                <input type="text" id="ville" name="ville" value="<?php echo $user['ville'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <input type="hidden" name="update_profile" value="1">
                            <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                        </div>
                    </form>
                </div>
                
                <!-- Onglet Sécurité -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <form action="profile.php<?php echo $debugMode ? '?debug=1' : ''; ?>" method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Nouveau mot de passe</label>
                            <input type="password" id="new_password" name="new_password" required>
                            <small class="form-text text-muted">Le mot de passe doit contenir au moins 8 caractères.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="form-actions">
                            <input type="hidden" name="change_password" value="1">
                            <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                        </div>
                    </form>
                </div>
                
                <!-- Onglet Commandes -->
                <div class="tab-pane fade" id="orders" role="tabpanel">
                    <?php
                    // Vérifier si la table commandes existe
                    if (tableExists($conn, 'commandes')) {
                        // Récupérer les commandes de l'utilisateur
                        $query = "SELECT c.*, COUNT(d.id_detail) as nb_articles 
                                  FROM commandes c 
                                  LEFT JOIN details_commande d ON c.id_commande = d.id_commande 
                                  WHERE c.id_utilisateur = ? 
                                  GROUP BY c.id_commande 
                                  ORDER BY c.date_commande DESC";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            ?>
                            <div class="orders-list">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>N° Commande</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Articles</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = $result->fetch_assoc()) { ?>
                                            <tr>
                                                <td>#<?php echo $order['id_commande']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($order['date_commande'])); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php 
                                                        echo match($order['statut'] ?? '') {
                                                            'en attente' => 'warning',
                                                            'confirmée' => 'info',
                                                            'expédiée' => 'primary',
                                                            'livrée' => 'success',
                                                            'annulée' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($order['statut'] ?? 'Inconnue'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $order['nb_articles']; ?> article(s)</td>
                                                <td><?php echo formatPrice($order['montant_total']); ?></td>
                                                <td>
                                                    <a href="order-details.php?id=<?php echo $order['id_commande']; ?>" class="btn btn-sm btn-outline-primary">Détails</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            echo '<div class="alert alert-info">Vous n\'avez pas encore passé de commande.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning">Le système de commandes n\'est pas encore disponible.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Ajout des styles spécifiques à la page de profil -->
<style>
.profile-container {
    max-width: 900px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.profile-header p {
    color: #666;
    font-size: 1rem;
}

.profile-tabs {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.nav-tabs {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    flex-wrap: wrap;
}

.nav-tabs .nav-item {
    margin-bottom: -1px;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    color: #495057;
    padding: 0.8rem 1.5rem;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #007bff;
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.tab-content {
    padding: 2rem;
}

.profile-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -10px;
    margin-left: -10px;
}

.form-group {
    margin-bottom: 1rem;
    flex: 0 0 100%;
    max-width: 100%;
    padding-right: 10px;
    padding-left: 10px;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
}

.col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
}

.form-group label {
    font-weight: 500;
    color: #555;
    margin-bottom: 0.5rem;
    display: block;
}

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
}

.form-actions {
    margin-top: 1rem;
}

.btn {
    display: inline-block;
    font-weight: 500;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    padding: 0.8rem 1.5rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: all 0.15s ease-in-out;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0069d9;
    border-color: #0062cc;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

.btn-outline-primary {
    color: #007bff;
    border-color: #007bff;
    background-color: transparent;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.alert {
    position: relative;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeeba;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 0.25em 0.4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-primary {
    color: #fff;
    background-color: #007bff;
}

.badge-secondary {
    color: #fff;
    background-color: #6c757d;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-warning {
    color: #212529;
    background-color: #ffc107;
}

.badge-info {
    color: #fff;
    background-color: #17a2b8;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .col-md-4,
    .col-md-6,
    .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .nav-tabs .nav-link {
        padding: 0.5rem 1rem;
    }
    
    .tab-content {
        padding: 1.5rem;
    }
}
</style>

<script>
// Script pour gérer les onglets
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.nav-link');
    const tabContents = document.querySelectorAll('.tab-pane');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Supprimer la classe active de tous les onglets
            tabs.forEach(t => t.classList.remove('active'));
            
            // Ajouter la classe active à l'onglet cliqué
            this.classList.add('active');
            
            // Masquer tous les contenus d'onglet
            tabContents.forEach(content => {
                content.classList.remove('show', 'active');
            });
            
            // Afficher le contenu de l'onglet correspondant
            const target = this.getAttribute('href').substring(1);
            const targetContent = document.getElementById(target);
            if (targetContent) {
                targetContent.classList.add('show', 'active');
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?> 