<?php
session_start();



/**
 * Connecte l'utilisateur
 * @param string $email
 * @param string $password
 * @return array|bool
 */
function loginUser($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            // Stockage des informations en session
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            return $user;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        return false;
    }
}

/**
 * Inscrit un nouvel utilisateur
 * @param array $userData
 * @return bool
 */
function registerUser($userData) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, 'client')");
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $userData['nom'],
            $userData['email'],
            $hashedPassword
        ]);
    } catch (PDOException $e) {
        error_log("Erreur d'inscription : " . $e->getMessage());
        return false;
    }
}

/**
 * Déconnecte l'utilisateur
 */
function logout() {
    session_unset();
    session_destroy();
}

/**
 * Redirige les utilisateurs non connectés
 * @param string $redirect_url URL de redirection après connexion
 */
function requireLogin($redirect_url = null) {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $redirect_url ?? $_SERVER['REQUEST_URI'];
        header('Location: /pages/auth/login.php');
        exit;
    }
} 