<?php
/**
 * Page d'accueil
 * Affiche les derniers véhicules ajoutés et une bannière d'accueil
 */

// Définition du titre de la page
$pageTitle = "Accueil";

// Inclusion des fichiers nécessaires
include '../config/config.php'; // Connexion à la DB
include '../includes/header.php'; // En-tête du site

// Récupérer les 6 derniers véhicules ajoutés
$query = "SELECT * FROM vehicules ORDER BY id_vehicule DESC LIMIT 6";
$result = $conn->query($query);
?>

<!-- Bannière d'accueil -->
<section class="hero-banner">
    <div class="hero-content">
        <h1>Bienvenue chez DaCar</h1>
        <p>Votre partenaire de confiance pour l'achat et la location de véhicules de qualité</p>
        <div class="hero-buttons">
            <a href="catalogue.php" class="btn btn-primary">Voir notre catalogue</a>
            <a href="contact.php" class="btn btn-secondary">Nous contacter</a>
        </div>
    </div>
</section>

<!-- Section des derniers véhicules -->
<section class="latest-vehicles">
    <div class="section-header">
        <h2>Nos Derniers Véhicules</h2>
        <p>Découvrez les dernières additions à notre catalogue</p>
    </div>

    <div class="voitures">
        <?php 
        // Vérifier si des véhicules ont été trouvés
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { 
                // Récupérer l'image principale du véhicule (à implémenter)
                $imagePath = "/DaCar/assets/images/vehicles/" . $row['id_vehicule'] . "_1.jpg";
                // Image par défaut si non disponible
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                    $imagePath = "/DaCar/assets/images/no-image.jpg";
                }
        ?>
                <div class="voiture">
                    <div class="voiture-image">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $row['marque'] . ' ' . $row['modele']; ?>">
                    </div>
                    <div class="voiture-info">
                        <h3><?php echo $row['marque'] . " " . $row['modele']; ?></h3>
                        <div class="voiture-details">
                            <p class="price"><i class="fas fa-tag"></i> <?php echo number_format($row['prix'], 2); ?> €</p>
                            <p class="mileage"><i class="fas fa-road"></i> <?php echo number_format($row['kilometrage']); ?> km</p>
                            <?php if (isset($row['annee'])) { ?>
                                <p class="year"><i class="fas fa-calendar-alt"></i> <?php echo $row['annee']; ?></p>
                            <?php } ?>
                        </div>
                        <a href="vehicle-details.php?id=<?php echo $row['id_vehicule']; ?>" class="btn btn-view">
                            <i class="fas fa-eye"></i> Voir détails
                        </a>
                    </div>
                </div>
        <?php 
            }
        } else {
            echo "<p class='no-vehicles'>Aucun véhicule disponible pour le moment.</p>";
        }
        ?>
    </div>
    
    <div class="view-all">
        <a href="catalogue.php" class="btn btn-large">Voir tous nos véhicules</a>
    </div>
</section>

<!-- Section des services -->
<section class="services">
    <div class="section-header">
        <h2>Nos Services</h2>
        <p>DaCar vous propose une gamme complète de services</p>
    </div>
    
    <div class="services-grid">
        <div class="service-card">
            <i class="fas fa-car-side"></i>
            <h3>Vente de véhicules</h3>
            <p>Large sélection de véhicules neufs et d'occasion</p>
        </div>
        <div class="service-card">
            <i class="fas fa-key"></i>
            <h3>Location</h3>
            <p>Solutions de location flexibles adaptées à vos besoins</p>
        </div>
        <div class="service-card">
            <i class="fas fa-tools"></i>
            <h3>Entretien</h3>
            <p>Service d'entretien et de réparation professionnel</p>
        </div>
        <div class="service-card">
            <i class="fas fa-handshake"></i>
            <h3>Financement</h3>
            <p>Options de financement personnalisées</p>
        </div>
    </div>
</section>

<div class="container mt-4">
    <?php if (isset($_SESSION['alert'])): ?>
        <?php $alert = getAlert(); ?>
        <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $alert['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Outils de diagnostic -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Outils de diagnostic</h5>
        </div>
        <div class="card-body">
            <p>Utilisez ces outils pour vérifier que la connexion entre le front-end et le back-end fonctionne correctement.</p>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <a href="test-connection.php" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-database mr-2"></i> Test de connexion à la base de données
                    </a>
                </div>
                <div class="col-md-6 mb-2">
                    <a href="api-test.php" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-exchange-alt mr-2"></i> Test d'API et communication AJAX
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenu existant -->
    <!-- ... existing code ... -->
</div>

<?php include '../includes/footer.php'; // Pied de page ?>