<!-- 
 * En-tête du site
 * Ce fichier contient la structure HTML de l'en-tête présent sur toutes les pages
 * Inclut les métadonnées, liens CSS, la barre de navigation et le logo
 -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>TeranCar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/CSS/style.css">
    <!-- Meta tags pour améliorer le SEO -->
    <meta name="description" content="Terancar - Vente et location de véhicules de qualité. Trouvez votre voiture idéale parmi notre large sélection.">
    <meta name="keywords" content="voiture, automobile, achat voiture, location voiture, véhicules, Terancar">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png">
    <!-- Meta tags pour les réseaux sociaux -->
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description" content="Terancar - Vente et location de véhicules de qualité. Trouvez votre voiture idéale parmi notre large sélection.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta name="twitter:card" content="summary_large_image">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="alerts-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="page-content">
    </div>
</body>
</html>
