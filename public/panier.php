<?php
/**
 * Page de panier
 * Gère l'ajout, la suppression et l'affichage des véhicules dans le panier
 * Prend en charge à la fois les achats et les locations
 */

// Définition du titre de la page
$pageTitle = "Votre panier";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Initialisation du panier dans la session si nécessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [
        'achat' => [],
        'location' => []
    ];
}

// Vérifier si la table vehicules existe
$tableExists = tableExists('vehicules');
$debugMode = isset($_GET['debug']) ? true : false;

// Traitement des actions sur le panier
if (isset($_GET['ajouter']) && is_numeric($_GET['ajouter'])) {
    $vehiculeId = (int)$_GET['ajouter'];
    $type = isset($_GET['type']) && in_array($_GET['type'], ['achat', 'location']) ? $_GET['type'] : 'achat';
        
        // Vérifier si le véhicule existe
    if ($tableExists) {
        $query = "SELECT * FROM vehicules WHERE id_vehicule = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $vehiculeId);
            $stmt->execute();
            $result = $stmt->get_result();
            
        if ($result->num_rows > 0) {
                $vehicule = $result->fetch_assoc();
                
                // Vérifier si le véhicule est déjà dans le panier
            if (!in_array($vehiculeId, $_SESSION['panier'][$type])) {
                // Ajouter au panier
                $_SESSION['panier'][$type][] = $vehiculeId;
                setAlert("Le véhicule a été ajouté à votre panier.", "success");
            } else {
                setAlert("Ce véhicule est déjà dans votre panier.", "warning");
            }
        } else {
            setAlert("Ce véhicule n'existe pas.", "danger");
        }
        $stmt->close();
    } else {
        setAlert("Impossible d'accéder aux informations des véhicules.", "danger");
    }
    
    // Rediriger vers la page du panier
    header("Location: panier.php");
    exit;
    }
    
    // Supprimer un véhicule du panier
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $vehiculeId = (int)$_GET['supprimer'];
    $type = isset($_GET['type']) && in_array($_GET['type'], ['achat', 'location']) ? $_GET['type'] : 'achat';
    
    // Rechercher l'index du véhicule dans le panier
    $index = array_search($vehiculeId, $_SESSION['panier'][$type]);
    
    if ($index !== false) {
        // Supprimer du panier
        unset($_SESSION['panier'][$type][$index]);
        // Réindexer le tableau
        $_SESSION['panier'][$type] = array_values($_SESSION['panier'][$type]);
        setAlert("Le véhicule a été retiré de votre panier.", "success");
                } else {
        setAlert("Ce véhicule n'est pas dans votre panier.", "warning");
            }
            
            // Rediriger vers la page du panier
    header("Location: panier.php");
            exit;
        }

// Vider le panier
if (isset($_GET['vider'])) {
    $type = isset($_GET['type']) && in_array($_GET['type'], ['achat', 'location']) ? $_GET['type'] : 'all';
    
    if ($type === 'all') {
        $_SESSION['panier'] = [
            'achat' => [],
            'location' => []
        ];
        setAlert("Votre panier a été vidé.", "success");
                } else {
        $_SESSION['panier'][$type] = [];
        setAlert("Votre panier de " . ($type === 'achat' ? "vente" : "location") . " a été vidé.", "success");
            }
            
            // Rediriger vers la page du panier
    header("Location: panier.php");
            exit;
        }

// Récupérer les informations des véhicules dans le panier
$vehiculesAchat = [];
$vehiculesLocation = [];
$totalAchat = 0;
$totalLocation = 0;

if ($tableExists) {
    // Récupérer les véhicules d'achat
    if (!empty($_SESSION['panier']['achat'])) {
        $ids = implode(',', array_map('intval', $_SESSION['panier']['achat']));
        $query = "SELECT * FROM vehicules WHERE id_vehicule IN ($ids)";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $vehiculesAchat[] = $row;
                $totalAchat += $row['prix'];
            }
        }
    }
    
    // Récupérer les véhicules de location
    if (!empty($_SESSION['panier']['location'])) {
        $ids = implode(',', array_map('intval', $_SESSION['panier']['location']));
        $query = "SELECT * FROM vehicules WHERE id_vehicule IN ($ids)";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Vérifier si le prix_location existe, sinon utiliser un pourcentage du prix d'achat
                $prixLocation = isset($row['prix_location']) ? $row['prix_location'] : ($row['prix'] * 0.02);
                $row['prix_location'] = $prixLocation;
                $vehiculesLocation[] = $row;
                $totalLocation += $prixLocation;
            }
        }
    }
}
?>

<!-- Affichage des alertes -->
<?php echo getAlert(); ?>

<!-- Contenu principal -->
<div class="container panier-container">
    <h1>Votre Panier</h1>
    
    <?php if (empty($_SESSION['panier']['achat']) && empty($_SESSION['panier']['location'])) { ?>
        <div class="panier-vide">
            <i class="fas fa-shopping-cart fa-4x"></i>
            <p>Votre panier est vide.</p>
            <a href="catalogue.php" class="btn btn-primary">Parcourir notre catalogue</a>
        </div>
    <?php } else { ?>
        <!-- Onglets pour basculer entre achat et location -->
        <div class="panier-tabs">
            <button class="tab-btn active" data-target="achat-tab">Achats (<?php echo count($_SESSION['panier']['achat']); ?>)</button>
            <button class="tab-btn" data-target="location-tab">Locations (<?php echo count($_SESSION['panier']['location']); ?>)</button>
        </div>
        
        <!-- Section des achats -->
        <div id="achat-tab" class="panier-tab-content active">
            <?php if (empty($vehiculesAchat)) { ?>
                <div class="panier-vide">
                    <p>Vous n'avez aucun véhicule dans votre panier d'achat.</p>
                </div>
            <?php } else { ?>
                <div class="panier-actions">
                    <a href="panier.php?vider=1&type=achat" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier d\'achat ?');">
                        <i class="fas fa-trash"></i> Vider le panier
                    </a>
                </div>
                
        <div class="panier-items">
                    <?php foreach ($vehiculesAchat as $vehicule) { 
                        // Récupérer l'image principale du véhicule
                        $imagePath = "/DaCar/assets/images/vehicles/" . $vehicule['id_vehicule'] . "_1.jpg";
                        // Image par défaut si non disponible
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $imagePath = "/DaCar/assets/images/no-image.jpg";
                        }
                    ?>
                        <div class="panier-item">
                            <div class="item-image">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>">
                    </div>
                    <div class="item-details">
                                <h3><?php echo $vehicule['marque'] . " " . $vehicule['modele']; ?></h3>
                                <div class="item-specs">
                                    <span><i class="fas fa-road"></i> <?php echo number_format($vehicule['kilometrage']); ?> km</span>
                                    <?php if (isset($vehicule['annee'])) { ?>
                                        <span><i class="fas fa-calendar-alt"></i> <?php echo $vehicule['annee']; ?></span>
                                    <?php } ?>
                                    <?php if (isset($vehicule['carburant'])) { ?>
                                        <span><i class="fas fa-gas-pump"></i> <?php echo $vehicule['carburant']; ?></span>
                                    <?php } ?>
                                </div>
                                    </div>
                            <div class="item-price">
                                <span class="price"><?php echo formatPrice($vehicule['prix']); ?></span>
                                </div>
                            <div class="item-actions">
                                <a href="vehicle-details.php?id=<?php echo $vehicule['id_vehicule']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="panier.php?supprimer=<?php echo $vehicule['id_vehicule']; ?>&type=achat" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir retirer ce véhicule du panier ?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            
            <div class="panier-summary">
                    <div class="summary-row">
                        <span>Total:</span>
                        <span class="total-price"><?php echo formatPrice($totalAchat); ?></span>
                    </div>
                    <div class="summary-actions">
                        <a href="catalogue.php" class="btn btn-secondary">Continuer mes achats</a>
                        <a href="checkout.php?type=achat" class="btn btn-primary">Procéder au paiement</a>
                    </div>
                </div>
            <?php } ?>
                </div>
                
        <!-- Section des locations -->
        <div id="location-tab" class="panier-tab-content">
            <?php if (empty($vehiculesLocation)) { ?>
                <div class="panier-vide">
                    <p>Vous n'avez aucun véhicule dans votre panier de location.</p>
                </div>
            <?php } else { ?>
                <div class="panier-actions">
                    <a href="panier.php?vider=1&type=location" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier de location ?');">
                        <i class="fas fa-trash"></i> Vider le panier
                    </a>
                </div>
                
                <div class="panier-items">
                    <?php foreach ($vehiculesLocation as $vehicule) { 
                        // Récupérer l'image principale du véhicule
                        $imagePath = "/DaCar/assets/images/vehicles/" . $vehicule['id_vehicule'] . "_1.jpg";
                        // Image par défaut si non disponible
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $imagePath = "/DaCar/assets/images/no-image.jpg";
                        }
                    ?>
                        <div class="panier-item">
                            <div class="item-image">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>">
                            </div>
                            <div class="item-details">
                                <h3><?php echo $vehicule['marque'] . " " . $vehicule['modele']; ?></h3>
                                <div class="item-specs">
                                    <span><i class="fas fa-road"></i> <?php echo number_format($vehicule['kilometrage']); ?> km</span>
                                    <?php if (isset($vehicule['annee'])) { ?>
                                        <span><i class="fas fa-calendar-alt"></i> <?php echo $vehicule['annee']; ?></span>
                                    <?php } ?>
                                    <?php if (isset($vehicule['carburant'])) { ?>
                                        <span><i class="fas fa-gas-pump"></i> <?php echo $vehicule['carburant']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="item-price">
                                <span class="price"><?php echo formatPrice($vehicule['prix_location']); ?> / jour</span>
                            </div>
                            <div class="item-actions">
                                <a href="vehicle-details.php?id=<?php echo $vehicule['id_vehicule']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="panier.php?supprimer=<?php echo $vehicule['id_vehicule']; ?>&type=location" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir retirer ce véhicule du panier ?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="panier-summary">
                    <div class="summary-row">
                        <span>Total par jour:</span>
                        <span class="total-price"><?php echo formatPrice($totalLocation); ?></span>
                    </div>
                    <div class="summary-actions">
                        <a href="catalogue.php" class="btn btn-secondary">Continuer mes locations</a>
                        <a href="checkout.php?type=location" class="btn btn-primary">Procéder à la réservation</a>
            </div>
        </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<!-- JavaScript pour les onglets -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.panier-tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Désactiver tous les onglets
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activer l'onglet cliqué
            this.classList.add('active');
            const targetId = this.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');
        });
    });
});
</script>

<style>
/* Styles pour la page panier */
.panier-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.panier-vide {
    text-align: center;
    padding: 3rem 0;
    color: #666;
}

.panier-vide i {
    margin-bottom: 1rem;
    color: #999;
}

.panier-tabs {
    display: flex;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #ddd;
}

.tab-btn {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.tab-btn.active {
    border-bottom-color: #4a6cf7;
    color: #4a6cf7;
}

.panier-tab-content {
    display: none;
}

.panier-tab-content.active {
    display: block;
}

.panier-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1rem;
}

.panier-items {
    margin-bottom: 2rem;
}

.panier-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #eee;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.3s;
}

.panier-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.item-image {
    width: 120px;
    height: 80px;
    overflow: hidden;
    border-radius: 4px;
    margin-right: 1rem;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
}

.item-details h3 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
}

.item-specs {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #666;
}

.item-price {
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0 1.5rem;
}

.item-actions {
    display: flex;
    gap: 0.5rem;
}

.panier-summary {
    background-color: #f9f9f9;
    padding: 1.5rem;
    border-radius: 8px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    font-size: 1.2rem;
    font-weight: 600;
}

.total-price {
    color: #4a6cf7;
}

.summary-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 1.5rem;
}

@media (max-width: 768px) {
    .panier-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .item-image {
        width: 100%;
        height: 150px;
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .item-price, .item-actions {
        margin-top: 1rem;
        align-self: flex-end;
    }
    
    .summary-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .summary-actions .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php include '../includes/footer.php'; ?>