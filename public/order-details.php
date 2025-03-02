<?php
/**
 * Page de détails d'une commande
 * Permet aux utilisateurs de visualiser les détails d'une commande spécifique
 * Adapté à la structure de base de données optimisée
 */

// Définition du titre de la page
$pageTitle = "Détails de la commande";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'profile.php';
    setAlert("Veuillez vous connecter pour accéder à vos commandes", "warning");
    redirect('login.php');
    exit();
}

// Vérifier si l'ID de commande est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setAlert("Identifiant de commande invalide", "danger");
    redirect('profile.php');
    exit();
}

$orderId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];
$debugMode = isset($_GET['debug']) && $_GET['debug'] == 1;

// Vérifier si les tables nécessaires existent
$tableCommandesExists = tableExists($conn, 'commandes');
$tableDetailsCommandeExists = tableExists($conn, 'details_commande');
$tableVehiculesExists = tableExists($conn, 'vehicules');

// Récupérer les informations de la commande
$order = null;
$orderDetails = [];
$errors = [];

if ($tableCommandesExists) {
    // Récupérer les informations de base de la commande
    $query = "SELECT * FROM commandes WHERE id_commande = ? AND id_utilisateur = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        
        // Récupérer les détails de la commande si la table existe
        if ($tableDetailsCommandeExists && $tableVehiculesExists) {
            $query = "SELECT d.*, v.marque, v.modele, v.annee, v.prix, v.image 
                      FROM details_commande d 
                      JOIN vehicules v ON d.id_vehicule = v.id_vehicule 
                      WHERE d.id_commande = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($detail = $result->fetch_assoc()) {
                $orderDetails[] = $detail;
            }
        } else {
            $errors[] = "Impossible de récupérer les détails de la commande : tables manquantes";
        }
    } else {
        $errors[] = "Commande non trouvée ou vous n'êtes pas autorisé à y accéder";
        setAlert("Commande non trouvée ou vous n'êtes pas autorisé à y accéder", "danger");
        redirect('profile.php');
        exit();
    }
} else {
    $errors[] = "La table des commandes n'existe pas dans la base de données";
}

// Afficher les informations de débogage si demandé
if ($debugMode) {
    debugDatabase();
}
?>

<div class="order-details-container">
    <div class="order-details-header">
        <h1>Détails de la commande #<?php echo $orderId; ?></h1>
        <a href="profile.php#orders" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Retour aux commandes
        </a>
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
    
    <?php if (!$tableCommandesExists && !$debugMode) { ?>
        <div class="alert alert-danger">
            <p>La configuration de la base de données n'est pas complète. Veuillez contacter l'administrateur.</p>
            <p><a href="order-details.php?id=<?php echo $orderId; ?>&debug=1">Afficher les informations de débogage</a></p>
        </div>
    <?php } ?>
    
    <?php if ($order) { ?>
        <div class="order-summary">
            <div class="order-info">
                <div class="order-info-item">
                    <span class="label">Date de commande :</span>
                    <span class="value"><?php echo date('d/m/Y à H:i', strtotime($order['date_commande'])); ?></span>
                </div>
                <div class="order-info-item">
                    <span class="label">Statut :</span>
                    <span class="value status-badge status-<?php echo strtolower($order['statut'] ?? 'inconnu'); ?>">
                        <?php echo ucfirst($order['statut'] ?? 'Inconnu'); ?>
                    </span>
                </div>
                <div class="order-info-item">
                    <span class="label">Total :</span>
                    <span class="value price"><?php echo formatPrice($order['montant_total']); ?></span>
                </div>
            </div>
            
            <?php if (isset($order['adresse_livraison']) && !empty($order['adresse_livraison'])) { ?>
                <div class="shipping-info">
                    <h3>Adresse de livraison</h3>
                    <p><?php echo nl2br(htmlspecialchars($order['adresse_livraison'])); ?></p>
                </div>
            <?php } ?>
            
            <?php if (isset($order['notes']) && !empty($order['notes'])) { ?>
                <div class="order-notes">
                    <h3>Notes</h3>
                    <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
                </div>
            <?php } ?>
        </div>
        
        <?php if (!empty($orderDetails)) { ?>
            <div class="order-items">
                <h2>Articles commandés</h2>
                <div class="items-list">
                    <?php foreach ($orderDetails as $item) { ?>
                        <div class="order-item">
                            <div class="item-image">
                                <?php if (isset($item['image']) && !empty($item['image'])) { ?>
                                    <img src="../assets/images/vehicles/<?php echo $item['image']; ?>" alt="<?php echo $item['marque'] . ' ' . $item['modele']; ?>">
                                <?php } else { ?>
                                    <div class="no-image">Pas d'image</div>
                                <?php } ?>
                            </div>
                            <div class="item-details">
                                <h3><?php echo $item['marque'] . ' ' . $item['modele'] . ' (' . $item['annee'] . ')'; ?></h3>
                                <div class="item-meta">
                                    <span class="item-price"><?php echo formatPrice($item['prix_unitaire'] ?? $item['prix']); ?></span>
                                    <?php if (isset($item['quantite']) && $item['quantite'] > 1) { ?>
                                        <span class="item-quantity">Quantité : <?php echo $item['quantite']; ?></span>
                                    <?php } ?>
                                </div>
                                <?php if (isset($item['options']) && !empty($item['options'])) { ?>
                                    <div class="item-options">
                                        <strong>Options :</strong> <?php echo $item['options']; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="item-total">
                                <?php 
                                $itemTotal = isset($item['prix_total']) ? $item['prix_total'] : 
                                            (isset($item['quantite']) ? $item['prix_unitaire'] * $item['quantite'] : $item['prix']);
                                echo formatPrice($itemTotal); 
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="order-totals">
                    <div class="totals-row">
                        <span class="label">Sous-total :</span>
                        <span class="value"><?php echo formatPrice($order['montant_total'] - ($order['frais_livraison'] ?? 0)); ?></span>
                    </div>
                    <?php if (isset($order['frais_livraison']) && $order['frais_livraison'] > 0) { ?>
                        <div class="totals-row">
                            <span class="label">Frais de livraison :</span>
                            <span class="value"><?php echo formatPrice($order['frais_livraison']); ?></span>
                        </div>
                    <?php } ?>
                    <div class="totals-row total">
                        <span class="label">Total :</span>
                        <span class="value"><?php echo formatPrice($order['montant_total']); ?></span>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning">
                <p>Aucun détail disponible pour cette commande.</p>
            </div>
        <?php } ?>
        
        <?php if (in_array(strtolower($order['statut'] ?? ''), ['en attente', 'confirmée'])) { ?>
            <div class="order-actions">
                <a href="cancel-order.php?id=<?php echo $orderId; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">
                    Annuler la commande
                </a>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<!-- Ajout des styles spécifiques à la page de détails de commande -->
<style>
.order-details-container {
    max-width: 900px;
    margin: 3rem auto;
    padding: 0 1rem;
}

.order-details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.order-details-header h1 {
    font-size: 1.8rem;
    color: #333;
    margin: 0;
}

.order-summary {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.order-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.order-info-item {
    display: flex;
    flex-direction: column;
}

.order-info-item .label {
    font-weight: 500;
    color: #666;
    margin-bottom: 0.5rem;
}

.order-info-item .value {
    font-size: 1.1rem;
    color: #333;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-weight: 500;
    font-size: 0.9rem;
}

.status-en.attente {
    background-color: #fff3cd;
    color: #856404;
}

.status-confirmée {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-expédiée {
    background-color: #cce5ff;
    color: #004085;
}

.status-livrée {
    background-color: #d4edda;
    color: #155724;
}

.status-annulée {
    background-color: #f8d7da;
    color: #721c24;
}

.status-inconnu {
    background-color: #e2e3e5;
    color: #383d41;
}

.price {
    font-weight: 600;
}

.shipping-info, .order-notes {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
}

.shipping-info h3, .order-notes h3 {
    font-size: 1.2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.order-items {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.order-items h2 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 1.5rem;
}

.items-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.order-item {
    display: flex;
    border-bottom: 1px solid #eee;
    padding-bottom: 1.5rem;
}

.order-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.item-image {
    width: 100px;
    height: 100px;
    overflow: hidden;
    border-radius: 4px;
    margin-right: 1.5rem;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    color: #6c757d;
    font-size: 0.8rem;
    text-align: center;
}

.item-details {
    flex: 1;
}

.item-details h3 {
    font-size: 1.2rem;
    color: #333;
    margin: 0 0 0.5rem 0;
}

.item-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.item-price {
    font-weight: 500;
}

.item-quantity {
    color: #666;
}

.item-options {
    font-size: 0.9rem;
    color: #666;
}

.item-total {
    font-weight: 600;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    margin-left: 1.5rem;
}

.order-totals {
    margin-top: 2rem;
    border-top: 1px solid #eee;
    padding-top: 1.5rem;
}

.totals-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.totals-row.total {
    font-weight: 600;
    font-size: 1.2rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.order-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 2rem;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

@media (max-width: 768px) {
    .order-item {
        flex-direction: column;
    }
    
    .item-image {
        width: 100%;
        height: 200px;
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .item-total {
        margin-left: 0;
        margin-top: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?> 