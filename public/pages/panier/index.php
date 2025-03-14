<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = 'Mon Panier';
$pageDescription = 'Gérez votre panier d\'achats et de locations chez Teran\'Cars';
$currentPage = 'panier';
$additionalCss = ['css/panier.css'];
$additionalJs = ['js/panier.js'];

// Initialisation du panier s'il n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [
        'achat' => [],
        'location' => []
    ];
}

// Traitement des actions sur le panier
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'remove':
            if (isset($_POST['id'])) {
                $type = isset($_POST['type']) ? $_POST['type'] : 'achat';
                unset($_SESSION['panier'][$type][$_POST['id']]);
            }
            break;
        case 'update':
            if (isset($_POST['id']) && isset($_POST['quantity']) && isset($_POST['type'])) {
                $type = $_POST['type'];
                $_SESSION['panier'][$type][$_POST['id']]['quantite'] = max(1, intval($_POST['quantity']));
            }
            break;
        case 'clear':
            $_SESSION['panier'] = [
                'achat' => [],
                'location' => []
            ];
            break;
    }
    // Redirection pour éviter la resoumission du formulaire
    header('Location: ' . url('panier'));
    exit;
}

// Récupération des véhicules du panier
$vehiculesAchat = [];
$vehiculesLocation = [];

// Récupération des véhicules à l'achat
if (!empty($_SESSION['panier']['achat'])) {
    $ids = array_keys($_SESSION['panier']['achat']);
    if (!empty($ids)) {
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "SELECT * FROM vehicules WHERE id_vehicule IN ($placeholders)";
        $stmt = $db->prepare($query);
        $stmt->execute($ids);
        $vehiculesAchat = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Récupération des véhicules en location
if (!empty($_SESSION['panier']['location'])) {
    $ids = array_keys($_SESSION['panier']['location']);
    if (!empty($ids)) {
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "SELECT * FROM vehicules WHERE id_vehicule IN ($placeholders)";
        $stmt = $db->prepare($query);
        $stmt->execute($ids);
        $vehiculesLocation = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Calcul du total
$total = 0;
foreach ($vehiculesAchat as $vehicule) {
    $id = $vehicule['id_vehicule'];
    if (isset($_SESSION['panier']['achat'][$id])) {
        $total += $vehicule['prix'] * $_SESSION['panier']['achat'][$id]['quantite'];
    }
}
foreach ($vehiculesLocation as $vehicule) {
    $id = $vehicule['id_vehicule'];
    if (isset($_SESSION['panier']['location'][$id])) {
        $item = $_SESSION['panier']['location'][$id];
        if (isset($item['date_debut']) && isset($item['date_fin'])) {
            $dateDebut = new DateTime($item['date_debut']);
            $dateFin = new DateTime($item['date_fin']);
            $nbJours = $dateDebut->diff($dateFin)->days + 1;
            $total += $vehicule['tarif_location_journalier'] * $item['quantite'] * $nbJours;
        }
    }
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <h1>Mon Panier</h1>

    <?php if (empty($vehiculesAchat) && empty($vehiculesLocation)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Votre panier est vide</p>
            <a href="<?= url('catalogue') ?>" class="btn btn-primary">
                Parcourir le catalogue
            </a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <?php if (!empty($vehiculesAchat)): ?>
                <div class="cart-section">
                    <h2>Véhicules à acheter</h2>
                    <div class="cart-items">
                        <?php foreach ($vehiculesAchat as $vehicule): ?>
                            <?php $id = $vehicule['id_vehicule']; ?>
                            <div class="cart-item">
                                <img src="<?= asset('images/vehicules/' . getVehicleImage($vehicule['marque'], $vehicule['modele'])) ?>" 
                                     alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>"
                                     class="item-image">
                                
                                <div class="item-details">
                                    <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                                    <div class="item-specs">
                                        <span><i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?></span>
                                        <span><i class="fas fa-gas-pump"></i> <?= ucfirst($vehicule['carburant']) ?></span>
                                    </div>
                                    <p class="item-price"><?= formatPrice($vehicule['prix']) ?></p>
                                </div>

                                <div class="item-quantity">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="type" value="achat">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <button type="button" class="quantity-btn minus">-</button>
                                        <input type="number" name="quantity" 
                                               value="<?= $_SESSION['panier']['achat'][$id]['quantite'] ?>" 
                                               min="1" max="<?= $vehicule['stock'] ?>" 
                                               class="quantity-input">
                                        <button type="button" class="quantity-btn plus">+</button>
                                    </form>
                                </div>

                                <div class="item-total">
                                    <?= formatPrice($vehicule['prix'] * $_SESSION['panier']['achat'][$id]['quantite']) ?>
                                </div>

                                <form method="post" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="type" value="achat">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <button type="submit" class="remove-btn" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($vehiculesLocation)): ?>
                <div class="cart-section">
                    <h2>Véhicules à louer</h2>
                    <div class="cart-items">
                        <?php foreach ($vehiculesLocation as $vehicule): ?>
                            <?php $id = $vehicule['id_vehicule']; ?>
                            <div class="cart-item">
                                <img src="<?= asset('images/vehicules/' . getVehicleImage($vehicule['marque'], $vehicule['modele'])) ?>" 
                                     alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>"
                                     class="item-image">
                                
                                <div class="item-details">
                                    <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                                    <div class="item-specs">
                                        <span><i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?></span>
                                        <span><i class="fas fa-gas-pump"></i> <?= ucfirst($vehicule['carburant']) ?></span>
                                    </div>
                                    <p class="item-price"><?= formatPrice($vehicule['tarif_location_journalier']) ?> / jour</p>
                                    
                                    <div class="item-dates">
                                        <div class="date-group">
                                            <label>Date de début</label>
                                            <input type="date" name="date_debut" 
                                                   value="<?= $_SESSION['panier']['location'][$id]['date_debut'] ?? '' ?>"
                                                   min="<?= date('Y-m-d') ?>"
                                                   required>
                                        </div>
                                        <div class="date-group">
                                            <label>Date de fin</label>
                                            <input type="date" name="date_fin"
                                                   value="<?= $_SESSION['panier']['location'][$id]['date_fin'] ?? '' ?>"
                                                   min="<?= date('Y-m-d') ?>"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="item-quantity">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="type" value="location">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <button type="button" class="quantity-btn minus">-</button>
                                        <input type="number" name="quantity" 
                                               value="<?= $_SESSION['panier']['location'][$id]['quantite'] ?>" 
                                               min="1" max="<?= $vehicule['stock'] ?>" 
                                               class="quantity-input">
                                        <button type="button" class="quantity-btn plus">+</button>
                                    </form>
                                </div>

                                <div class="item-total">
                                    <?php
                                    $item = $_SESSION['panier']['location'][$id];
                                    if (isset($item['date_debut']) && isset($item['date_fin'])) {
                                        $dateDebut = new DateTime($item['date_debut']);
                                        $dateFin = new DateTime($item['date_fin']);
                                        $nbJours = $dateDebut->diff($dateFin)->days + 1;
                                        echo formatPrice($vehicule['tarif_location_journalier'] * $item['quantite'] * $nbJours);
                                    } else {
                                        echo "Dates requises";
                                    }
                                    ?>
                                </div>

                                <form method="post" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="type" value="location">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <button type="submit" class="remove-btn" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cart-summary">
                <h2>Récapitulatif</h2>
                <div class="summary-row total">
                    <span>Total</span>
                    <span class="total-price"><?= formatPrice($total) ?></span>
                </div>
                
                <div class="cart-actions">
                    <form method="post" class="clear-form">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline">Vider le panier</button>
                    </form>
                    <a href="<?= url('checkout') ?>" class="btn btn-primary">
                        Procéder au paiement
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 