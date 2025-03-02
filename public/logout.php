<?php
/**
 * Page de déconnexion
 * Détruit la session utilisateur et redirige vers la page d'accueil
 */

// Inclusion du fichier de configuration
include '../config/config.php';

// Vérifier si l'utilisateur est connecté
if (isLoggedIn()) {
    // Récupérer le nom d'utilisateur avant de détruire la session
    $userName = $_SESSION['user_name'];
    
    // Supprimer le cookie de connexion automatique s'il existe
    if (isset($_COOKIE['remember_token'])) {
        // Supprimer le token de la base de données (à implémenter)
        // $query = "DELETE FROM user_tokens WHERE token = ?";
        
        // Supprimer le cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Détruire la session
    session_unset();
    session_destroy();
    
    // Démarrer une nouvelle session pour les messages flash
    session_start();
    
    // Définir un message de succès
    setAlert("Vous avez été déconnecté avec succès. À bientôt $userName !", "success");
}

// Rediriger vers la page d'accueil
redirect('index.php');
exit();
?> 