<?php
// Inclusion du fichier d'initialisation
require_once __DIR__ . '/../../../includes/init.php';

// Initialiser le panier dans la session s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Variables de la page
$pageTitle = 'Mon Panier';
$pageDescription = 'Gérez votre panier d\'achats et de locations chez Teran\'Cars';
$currentPage = 'panier';
$additionalCss = ['css/panier.css'];
$additionalJs = ['js/panier.js'];

// Traitement des actions sur le panier
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'remove':
            if (isset($_POST['cart_id'])) {
                foreach ($_SESSION['cart'] as $key => $item) {
                    if ($item['cart_id'] == $_POST['cart_id']) {
                        unset($_SESSION['cart'][$key]);
                        break;
                    }
                }
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            break;
        case 'update':
            if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['cart_id'] == $_POST['cart_id']) {
                        $item['cart_quantity'] = max(1, intval($_POST['quantity']));
                        break;
                    }
                }
            }
            break;
        case 'clear':
            $_SESSION['cart'] = [];
            break;
    }
    // Redirection pour éviter la resoumission du formulaire
    header('Location: ' . url('panier'));
    exit;
}

// Récupération des articles du panier
$cartItems = [];
foreach ($_SESSION['cart'] as $cartItem) {
    $vehicle = getVehicleById($cartItem['id_vehicule']);
    if ($vehicle) {
        $cartItems[] = array_merge($vehicle, [
            'cart_quantity' => $cartItem['cart_quantity'],
            'cart_id' => $cartItem['cart_id'],
            'type' => $cartItem['type']
        ]);
    }
}

// Séparation des articles par type
$vehiculesAchat = array_filter($cartItems, function($item) {
    return $item['type'] === 'achat';
});
$vehiculesLocation = array_filter($cartItems, function($item) {
    return $item['type'] === 'location';
});

// Calcul du total
$total = 0;
foreach ($vehiculesAchat as $item) {
    $total += $item['prix'] * $item['cart_quantity'];
}
foreach ($vehiculesLocation as $item) {
    if (isset($item['date_debut_location']) && isset($item['date_fin_location'])) {
        $dateDebut = new DateTime($item['date_debut_location']);
        $dateFin = new DateTime($item['date_fin_location']);
        $nbJours = $dateDebut->diff($dateFin)->days + 1;
        $total += $item['tarif_location_journalier'] * $item['cart_quantity'] * $nbJours;
    }
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <h1>Mon Panier</h1>

    <?php if (empty($cartItems)): ?>
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
                        <?php foreach ($vehiculesAchat as $item): ?>
                            <div class="cart-item">
                                <img src="<?= getVehicleMainImage($item['id_vehicule']) ?>" 
                                     alt="<?= htmlspecialchars($item['marque'] . ' ' . $item['modele']) ?>"
                                     class="item-image">
                                
                                <div class="item-details">
                                    <h3><?= htmlspecialchars($item['marque'] . ' ' . $item['modele']) ?></h3>
                                    <div class="item-specs">
                                        <span><i class="fas fa-calendar"></i> <?= $item['annee'] ?></span>
                                        <span><i class="fas fa-gas-pump"></i> <?= ucfirst($item['carburant']) ?></span>
                                    </div>
                                    <p class="item-price"><?= number_format($item['prix'], 2, ',', ' ') ?> €</p>
                                </div>

                                <div class="item-quantity">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <button type="button" class="quantity-btn minus">-</button>
                                        <input type="number" name="quantity" 
                                               value="<?= $item['cart_quantity'] ?>" 
                                               min="1" max="<?= $item['stock'] ?>" 
                                               class="quantity-input">
                                        <button type="button" class="quantity-btn plus">+</button>
                                    </form>
                                </div>

                                <div class="item-total">
                                    <?= number_format($item['prix'] * $item['cart_quantity'], 2, ',', ' ') ?> €
                                </div>

                                <form method="post" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
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
                        <?php foreach ($vehiculesLocation as $item): ?>
                            <div class="cart-item">
                                <img src="<?= getVehicleMainImage($item['id_vehicule']) ?>" 
                                     alt="<?= htmlspecialchars($item['marque'] . ' ' . $item['modele']) ?>"
                                     class="item-image">
                                
                                <div class="item-details">
                                    <h3><?= htmlspecialchars($item['marque'] . ' ' . $item['modele']) ?></h3>
                                    <div class="item-specs">
                                        <span><i class="fas fa-calendar"></i> <?= $item['annee'] ?></span>
                                        <span><i class="fas fa-gas-pump"></i> <?= ucfirst($item['carburant']) ?></span>
                                    </div>
                                    <p class="item-price"><?= number_format($item['tarif_location_journalier'], 2, ',', ' ') ?> € / jour</p>
                                    
                                    <div class="item-dates">
                                        <div class="date-group">
                                            <label>Date de début</label>
                                            <input type="date" name="date_debut" 
                                                   value="<?= $item['date_debut_location'] ?? '' ?>"
                                                   min="<?= date('Y-m-d') ?>"
                                                   required
                                                   data-cart-id="<?= $item['cart_id'] ?>">
                                        </div>
                                        <div class="date-group">
                                            <label>Date de fin</label>
                                            <input type="date" name="date_fin"
                                                   value="<?= $item['date_fin_location'] ?? '' ?>"
                                                   min="<?= date('Y-m-d') ?>"
                                                   required
                                                   data-cart-id="<?= $item['cart_id'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="item-quantity">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                        <button type="button" class="quantity-btn minus">-</button>
                                        <input type="number" name="quantity" 
                                               value="<?= $item['cart_quantity'] ?>" 
                                               min="1" max="<?= $item['stock'] ?>" 
                                               class="quantity-input">
                                        <button type="button" class="quantity-btn plus">+</button>
                                    </form>
                                </div>

                                <div class="item-total">
                                    <?php
                                    if (isset($item['date_debut_location']) && isset($item['date_fin_location'])) {
                                        $dateDebut = new DateTime($item['date_debut_location']);
                                        $dateFin = new DateTime($item['date_fin_location']);
                                        $nbJours = $dateDebut->diff($dateFin)->days + 1;
                                        echo number_format($item['tarif_location_journalier'] * $item['cart_quantity'] * $nbJours, 2, ',', ' ') . ' €';
                                    } else {
                                        echo "Dates requises";
                                    }
                                    ?>
                                </div>

                                <form method="post" class="remove-form">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
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
                    <span class="total-price"><?= number_format($total, 2, ',', ' ') ?> €</span>
                </div>
                
                <div class="cart-actions">
                    <a href="<?= url('catalogue') ?>" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Continuer mes achats
                    </a>
                    <a href="<?= url('checkout') ?>" class="btn btn-primary">
                        <i class="fas fa-lock"></i> Procéder au paiement
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