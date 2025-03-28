<?php
// Inclusion du fichier d'initialisation
require_once __DIR__ . '/../../../includes/init.php';

// Vérifier si le panier n'est pas vide
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ' . url('panier'));
    exit;
}

// Variables de la page
$pageTitle = 'Paiement';
$pageDescription = 'Procédez au paiement de votre commande';
$currentPage = 'checkout';
$additionalCss = ['css/checkout.css'];

// Traitement du formulaire de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Début de la transaction
        $db->beginTransaction();

        // 1. Créer la commande
        $stmt = $db->prepare("
            INSERT INTO commandes (date_commande, montant_total, statut)
            VALUES (NOW(), :montant, 'en attente')
        ");
        $stmt->execute(['montant' => $_POST['montant_total']]);
        $commandeId = $db->lastInsertId();

        // 2. Créer le paiement
        $stmt = $db->prepare("
            INSERT INTO paiements (id_commande, reference_transaction, mode_paiement, montant, statut, date_paiement)
            VALUES (:id_commande, :reference, :mode, :montant, 'en_attente', NOW())
        ");
        $stmt->execute([
            'id_commande' => $commandeId,
            'reference' => uniqid('PAY-'),
            'mode' => $_POST['mode_paiement'],
            'montant' => $_POST['montant_total']
        ]);

        // 3. Vider le panier
        $_SESSION['cart'] = [];
        
        // Valider la transaction
        $db->commit();

        // Rediriger vers la page de confirmation
        $_SESSION['success_message'] = 'Votre commande a été enregistrée avec succès.';
        header('Location: ' . url('confirmation'));
        exit;

    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $db->rollBack();
        $_SESSION['error_message'] = 'Une erreur est survenue lors du traitement de votre commande.';
    }
}

// Calcul du total du panier
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $vehicle = getVehicleById($item['id_vehicule']);
    if ($vehicle) {
        if ($item['type'] === 'achat') {
            $total += $vehicle['prix'] * $item['cart_quantity'];
        } else {
            if (isset($item['date_debut_location']) && isset($item['date_fin_location'])) {
                $dateDebut = new DateTime($item['date_debut_location']);
                $dateFin = new DateTime($item['date_fin_location']);
                $nbJours = $dateDebut->diff($dateFin)->days + 1;
                $total += $vehicle['tarif_location_journalier'] * $item['cart_quantity'] * $nbJours;
            }
        }
    }
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <div class="checkout-container">
        <h1>Paiement</h1>

        <div class="checkout-content">
            <div class="order-summary">
                <h2>Récapitulatif de la commande</h2>
                <div class="summary-items">
                    <?php foreach ($_SESSION['cart'] as $item): 
                        $vehicle = getVehicleById($item['id_vehicule']);
                        if ($vehicle):
                    ?>
                        <div class="summary-item">
                            <img src="<?= getVehicleMainImage($item['id_vehicule']) ?>" 
                                 alt="<?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?>"
                                 class="item-thumbnail">
                            <div class="item-details">
                                <h3><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></h3>
                                <p class="item-type"><?= $item['type'] === 'achat' ? 'Achat' : 'Location' ?></p>
                                <p class="item-price">
                                    <?php if ($item['type'] === 'achat'): ?>
                                        <?= number_format($vehicle['prix'], 2, ',', ' ') ?> €
                                    <?php else: ?>
                                        <?= number_format($vehicle['tarif_location_journalier'], 2, ',', ' ') ?> € / jour
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="item-quantity">
                                x<?= $item['cart_quantity'] ?>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; ?>
                </div>
                <div class="summary-total">
                    <span>Total à payer :</span>
                    <span class="total-amount"><?= number_format($total, 2, ',', ' ') ?> €</span>
                </div>
            </div>

            <div class="payment-form">
                <h2>Informations de paiement</h2>
                <form method="post" id="payment-form">
                    <input type="hidden" name="montant_total" value="<?= $total ?>">
                    
                    <div class="form-group">
                        <label for="mode_paiement">Mode de paiement</label>
                        <select name="mode_paiement" id="mode_paiement" required>
                            <option value="carte">Carte bancaire</option>
                            <option value="paypal">PayPal</option>
                            <option value="virement">Virement bancaire</option>
                        </select>
                    </div>

                    <div id="carte-fields" class="payment-fields">
                        <div class="form-group">
                            <label for="card_number">Numéro de carte</label>
                            <input type="text" id="card_number" pattern="[0-9]{16}" maxlength="16" placeholder="1234 5678 9012 3456">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Date d'expiration</label>
                                <input type="text" id="expiry" pattern="[0-9]{2}/[0-9]{2}" placeholder="MM/AA">
                            </div>
                            
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" pattern="[0-9]{3,4}" maxlength="4" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-lock"></i> Payer <?= number_format($total, 2, ',', ' ') ?> €
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 