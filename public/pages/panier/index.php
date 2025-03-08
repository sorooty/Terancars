<?php
// Protection de la page
require_once 'protect.php';

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
    $_SESSION['panier'] = [];
}

// Traitement des actions sur le panier
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'remove':
            if (isset($_POST['id'])) {
                unset($_SESSION['panier'][$_POST['id']]);
            }
            break;
        case 'update':
            if (isset($_POST['id']) && isset($_POST['quantity'])) {
                $quantity = max(1, min(10, intval($_POST['quantity'])));
                $_SESSION['panier'][$_POST['id']]['quantity'] = $quantity;
            }
            break;
        case 'clear':
            $_SESSION['panier'] = [];
            break;
    }
    // Redirection pour éviter la resoumission du formulaire
    header('Location: ' . url('pages/panier/'));
    exit;
}

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $item) {
    $total += $item['prix'] * $item['quantity'];
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <h1>Mon Panier</h1>

    <?php if (empty($_SESSION['panier'])): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Votre panier est vide</p>
            <a href="<?= url('pages/catalogue/') ?>" class="btn btn-primary">
                Parcourir le catalogue
            </a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($_SESSION['panier'] as $id => $item): ?>
                    <div class="cart-item">
                        <img src="<?= asset('images/vehicules/' . $item['image']) ?>" 
                             alt="<?= htmlspecialchars($item['marque'] . ' ' . $item['modele']) ?>"
                             class="item-image">
                        
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item['marque'] . ' ' . $item['modele']) ?></h3>
                            <p class="item-type"><?= $item['type'] === 'location' ? 'Location' : 'Achat' ?></p>
                            <p class="item-price"><?= formatPrice($item['prix']) ?></p>
                        </div>

                        <div class="item-quantity">
                            <form method="post" class="quantity-form">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="10" onchange="this.form.submit()">
                            </form>
                        </div>

                        <div class="item-total">
                            <?= formatPrice($item['prix'] * $item['quantity']) ?>
                        </div>

                        <form method="post" class="remove-form">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <button type="submit" class="remove-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2>Récapitulatif</h2>
                <div class="summary-row">
                    <span>Total</span>
                    <span class="total-price"><?= formatPrice($total) ?></span>
                </div>
                
                <div class="cart-actions">
                    <form method="post" class="clear-form">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline">Vider le panier</button>
                    </form>
                    <a href="<?= url('pages/checkout/') ?>" class="btn btn-primary">
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