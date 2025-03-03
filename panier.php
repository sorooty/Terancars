<?php
require_once 'config/config.php';
require_once 'includes/cart_operations.php';

// Vérifier si le panier existe, sinon l'initialiser
if (!isset($_SESSION['panier'])) {
    initializeCart();
}

$pageTitle = "Mon Panier";
require_once 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="mb-4">Mon Panier</h1>

    <div class="alert-container"></div>

    <?php if (empty($_SESSION['panier']['achat']) && empty($_SESSION['panier']['location'])): ?>
        <div class="alert alert-info">
            Votre panier est vide. <a href="/vehicules.php">Découvrez nos véhicules</a>
        </div>
    <?php else: ?>
        <!-- Section Achats -->
        <?php if (!empty($_SESSION['panier']['achat'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">Véhicules à acheter</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Véhicule</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['panier']['achat'] as $id => $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['marque'] . ' ' . $item['modele']); ?></strong>
                                        </td>
                                        <td><?php echo formatPrice($item['prix']); ?></td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control quantity-input" 
                                                   value="<?php echo $item['quantite']; ?>" 
                                                   min="1" 
                                                   data-vehicule-id="<?php echo $id; ?>" 
                                                   data-type="achat">
                                        </td>
                                        <td><?php echo formatPrice($item['prix'] * $item['quantite']); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm remove-from-cart" 
                                                    data-vehicule-id="<?php echo $id; ?>" 
                                                    data-type="achat">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Section Locations -->
        <?php if (!empty($_SESSION['panier']['location'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">Véhicules à louer</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Véhicule</th>
                                    <th>Prix journalier</th>
                                    <th>Durée (jours)</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['panier']['location'] as $id => $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['marque'] . ' ' . $item['modele']); ?></strong>
                                        </td>
                                        <td><?php echo formatPrice($item['prix']); ?></td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control duration-input" 
                                                   value="<?php echo $item['duree']; ?>" 
                                                   min="1" 
                                                   data-vehicule-id="<?php echo $id; ?>">
                                        </td>
                                        <td><?php echo formatPrice($item['prix'] * $item['duree']); ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm remove-from-cart" 
                                                    data-vehicule-id="<?php echo $id; ?>" 
                                                    data-type="location">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Résumé du panier -->
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5 mb-0">Résumé de la commande</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>Sous-total:</td>
                                <td class="text-end cart-subtotal">
                                    <?php echo formatPrice($_SESSION['panier']['sous_total']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>TVA (20%):</td>
                                <td class="text-end cart-tva">
                                    <?php echo formatPrice($_SESSION['panier']['tva']); ?>
                                </td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total:</td>
                                <td class="text-end cart-total">
                                    <?php echo formatPrice($_SESSION['panier']['total']); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex justify-content-between">
            <button class="btn btn-outline-danger empty-cart">
                <i class="fas fa-trash"></i> Vider le panier
            </button>
            <?php if (isLoggedIn()): ?>
                <button class="btn btn-primary validate-cart">
                    <i class="fas fa-check"></i> Valider la commande
                </button>
            <?php else: ?>
                <a href="/login.php?redirect=panier.php" class="btn btn-primary">
                    Se connecter pour commander
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Inclure le JavaScript du panier -->
<script src="/assets/js/cart.js"></script>

<?php require_once 'includes/footer.php'; ?> 