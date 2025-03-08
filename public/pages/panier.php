<?php
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = "Mon panier";
$pageDescription = "Gérez votre panier d'achats et de locations";
$currentPage = 'panier';
$additionalCss = ['css/panier.css'];
$additionalJs = ['js/panier.js'];

// Initialisation du panier si nécessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [
        'achat' => [],
        'location' => []
    ];
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'remove':
                if (isset($_POST['type'], $_POST['vehicule_id'])) {
                    $type = $_POST['type'];
                    $vehiculeId = intval($_POST['vehicule_id']);
                    
                    if (isset($_SESSION['panier'][$type][$vehiculeId])) {
                        unset($_SESSION['panier'][$type][$vehiculeId]);
                        $_SESSION['success_message'] = "Le véhicule a été retiré du panier";
                    }
                }
                break;

            case 'update':
                if (isset($_POST['type'], $_POST['vehicule_id'], $_POST['quantite'])) {
                    $type = $_POST['type'];
                    $vehiculeId = intval($_POST['vehicule_id']);
                    $quantite = max(1, intval($_POST['quantite']));
                    
                    if (isset($_SESSION['panier'][$type][$vehiculeId])) {
                        $_SESSION['panier'][$type][$vehiculeId]['quantite'] = $quantite;
                        $_SESSION['success_message'] = "La quantité a été mise à jour";
                    }
                }
                break;

            case 'commander':
                try {
                    $db->beginTransaction();

                    // Création de la commande
                    $query = "INSERT INTO commandes (id_client, date_commande, montant_total, statut) 
                             VALUES (:id_client, NOW(), :montant_total, 'en attente')";
                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        ':id_client' => $_SESSION['user_id'],
                        ':montant_total' => calculateTotal()
                    ]);

                    $commandeId = $db->lastInsertId();

                    // Ajout des détails de la commande
                    foreach (['achat', 'location'] as $type) {
                        foreach ($_SESSION['panier'][$type] as $vehiculeId => $item) {
                            $query = "INSERT INTO details_commandes (id_commande, id_produit, quantite, prix_unitaire) 
                                     VALUES (:id_commande, :id_produit, :quantite, :prix_unitaire)";
                            $stmt = $db->prepare($query);
                            $stmt->execute([
                                ':id_commande' => $commandeId,
                                ':id_produit' => $vehiculeId,
                                ':quantite' => $item['quantite'],
                                ':prix_unitaire' => $type === 'achat' ? $item['prix'] : $item['tarif_location_journalier']
                            ]);

                            // Mise à jour du stock
                            $query = "UPDATE vehicules SET stock = stock - :quantite 
                                     WHERE id_vehicule = :id_vehicule AND stock >= :quantite";
                            $stmt = $db->prepare($query);
                            $stmt->execute([
                                ':quantite' => $item['quantite'],
                                ':id_vehicule' => $vehiculeId
                            ]);

                            // Enregistrement du mouvement de stock
                            $query = "INSERT INTO mouvements_stock (id_produit, type_mouvement, quantite) 
                                     VALUES (:id_produit, 'retrait', :quantite)";
                            $stmt = $db->prepare($query);
                            $stmt->execute([
                                ':id_produit' => $vehiculeId,
                                ':quantite' => $item['quantite']
                            ]);
                        }
                    }

                    $db->commit();
                    
                    // Vider le panier
                    $_SESSION['panier'] = ['achat' => [], 'location' => []];
                    $_SESSION['success_message'] = "Votre commande a été enregistrée avec succès !";
                    header('Location: ' . url('commandes'));
                    exit;

                } catch (PDOException $e) {
                    $db->rollBack();
                    error_log("Erreur lors de la commande: " . $e->getMessage());
                    $_SESSION['error_message'] = "Une erreur est survenue lors de la commande. Veuillez réessayer.";
                }
                break;
        }

        // Redirection pour éviter la resoumission du formulaire
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// Récupération des informations des véhicules dans le panier
$vehiculesAchat = [];
$vehiculesLocation = [];

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

// Calcul des totaux
function calculateTotal() {
    $total = 0;
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $type => $items) {
            foreach ($items as $item) {
                $total += $type === 'achat' 
                    ? $item['prix'] * $item['quantite']
                    : $item['tarif_location_journalier'] * $item['quantite'];
            }
        }
    }
    return $total;
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <div class="panier-page">
        <div class="panier-header">
            <h1>Mon panier</h1>
            <?php if (empty($vehiculesAchat) && empty($vehiculesLocation)): ?>
                <p>Votre panier est vide</p>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($vehiculesAchat)): ?>
            <div class="panier-section">
                <h2>Véhicules à acheter</h2>
                <div class="panier-items">
                    <?php foreach ($vehiculesAchat as $vehicule): ?>
                        <div class="panier-item">
                            <div class="item-image">
                                <img src="<?= asset('images/vehicules/' . $vehicule['id_vehicule'] . '.jpg') ?>" 
                                     alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>"
                                     onerror="this.src='<?= asset('images/vehicules/default.jpg') ?>'">
                            </div>
                            
                            <div class="item-info">
                                <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                                <p class="item-price"><?= formatPrice($vehicule['prix']) ?></p>
                                
                                <form method="POST" class="item-quantity">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="type" value="achat">
                                    <input type="hidden" name="vehicule_id" value="<?= $vehicule['id_vehicule'] ?>">
                                    <label for="quantite_<?= $vehicule['id_vehicule'] ?>">Quantité:</label>
                                    <input type="number" 
                                           id="quantite_<?= $vehicule['id_vehicule'] ?>" 
                                           name="quantite" 
                                           value="<?= $_SESSION['panier']['achat'][$vehicule['id_vehicule']]['quantite'] ?>" 
                                           min="1" 
                                           max="<?= $vehicule['stock'] ?>">
                                    <button type="submit" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </div>

                            <form method="POST" class="item-actions">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="type" value="achat">
                                <input type="hidden" name="vehicule_id" value="<?= $vehicule['id_vehicule'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                    Retirer
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($vehiculesLocation)): ?>
            <div class="panier-section">
                <h2>Véhicules à louer</h2>
                <div class="panier-items">
                    <?php foreach ($vehiculesLocation as $vehicule): ?>
                        <div class="panier-item">
                            <div class="item-image">
                                <img src="<?= asset('images/vehicules/' . $vehicule['id_vehicule'] . '.jpg') ?>" 
                                     alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>"
                                     onerror="this.src='<?= asset('images/vehicules/default.jpg') ?>'">
                            </div>
                            
                            <div class="item-info">
                                <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                                <p class="item-price"><?= formatPrice($vehicule['tarif_location_journalier']) ?> / jour</p>
                                
                                <form method="POST" class="item-quantity">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="type" value="location">
                                    <input type="hidden" name="vehicule_id" value="<?= $vehicule['id_vehicule'] ?>">
                                    <label for="quantite_<?= $vehicule['id_vehicule'] ?>">Nombre de jours:</label>
                                    <input type="number" 
                                           id="quantite_<?= $vehicule['id_vehicule'] ?>" 
                                           name="quantite" 
                                           value="<?= $_SESSION['panier']['location'][$vehicule['id_vehicule']]['quantite'] ?>" 
                                           min="1" 
                                           max="30">
                                    <button type="submit" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </div>

                            <form method="POST" class="item-actions">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="type" value="location">
                                <input type="hidden" name="vehicule_id" value="<?= $vehicule['id_vehicule'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                    Retirer
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($vehiculesAchat) || !empty($vehiculesLocation)): ?>
            <div class="panier-summary">
                <div class="summary-content">
                    <div class="summary-total">
                        <span>Total:</span>
                        <span class="total-amount"><?= formatPrice(calculateTotal()) ?></span>
                    </div>

                    <div class="summary-actions">
                        <a href="<?= url('auth/login') ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-shopping-cart"></i>
                            Se connecter pour commander
                        </a>
                        <a href="<?= url('catalogue') ?>" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-left"></i>
                            Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Votre panier est vide</p>
                <a href="<?= url('catalogue') ?>" class="btn btn-primary">
                    <i class="fas fa-car"></i>
                    Découvrir nos véhicules
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once ROOT_PATH . '/includes/template.php';
?> 