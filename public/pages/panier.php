<?php
require_once __DIR__ . '/../../includes/init.php';

// Vérification de la connexion utilisateur
if (!isLoggedIn()) {
    $_SESSION['error_message'] = "Veuillez vous connecter pour accéder au panier.";
    header('Location: ' . url('pages/auth/login.php'));
    exit;
}

// Initialisation du panier si nécessaire
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [
        'achat' => [],
        'location' => []
    ];
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? '';
    $vehiculeId = isset($_POST['vehicule_id']) ? intval($_POST['vehicule_id']) : 0;

    switch ($action) {
        case 'ajouter':
            if ($vehiculeId > 0) {
                // Vérification du stock
                $query = "SELECT * FROM vehicules WHERE id_vehicule = ? AND stock > 0";
                $stmt = $db->prepare($query);
                $stmt->execute([$vehiculeId]);
                $vehicule = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($vehicule) {
                    if ($type === 'achat') {
                        $_SESSION['panier']['achat'][$vehiculeId] = [
                            'quantite' => 1,
                            'prix' => $vehicule['prix']
                        ];
                    } else if ($type === 'location' && $vehicule['disponible_location']) {
                        $_SESSION['panier']['location'][$vehiculeId] = [
                            'quantite' => 1,
                            'tarif_location_journalier' => $vehicule['tarif_location_journalier'],
                            'date_debut' => $_POST['date_debut'] ?? '',
                            'date_fin' => $_POST['date_fin'] ?? ''
                        ];
                    }
                    $_SESSION['success_message'] = "Véhicule ajouté au panier avec succès.";
                    }
                }
                break;

        case 'supprimer':
            if (isset($_SESSION['panier'][$type][$vehiculeId])) {
                unset($_SESSION['panier'][$type][$vehiculeId]);
                $_SESSION['success_message'] = "Article retiré du panier.";
            }
            break;

        case 'modifier_quantite':
            $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 1;
            if ($quantite > 0 && isset($_SESSION['panier'][$type][$vehiculeId])) {
                        $_SESSION['panier'][$type][$vehiculeId]['quantite'] = $quantite;
                }
                break;

            case 'commander':
                try {
                    $db->beginTransaction();

                    // Création de la commande
                    $montantTotal = calculateTotal();
                    $query = "INSERT INTO commandes (id_utilisateur, date_commande, montant_total, statut) 
                             VALUES (?, NOW(), ?, 'en attente')";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$_SESSION['user_id'], $montantTotal]);
                    $commandeId = $db->lastInsertId();

                    // Ajout des détails de la commande
                    foreach (['achat', 'location'] as $type) {
                        foreach ($_SESSION['panier'][$type] as $vehiculeId => $item) {
                        // Vérification du stock
                        $query = "SELECT stock FROM vehicules WHERE id_vehicule = ? AND stock >= ?";
                            $stmt = $db->prepare($query);
                        $stmt->execute([$vehiculeId, $item['quantite']]);
                        if (!$stmt->fetch()) {
                            throw new Exception("Stock insuffisant pour le véhicule #" . $vehiculeId);
                        }

                            // Mise à jour du stock
                        $query = "UPDATE vehicules SET stock = stock - ? WHERE id_vehicule = ?";
                            $stmt = $db->prepare($query);
                        $stmt->execute([$item['quantite'], $vehiculeId]);

                        // Si c'est une location, créer l'entrée dans la table locations
                        if ($type === 'location') {
                            $query = "INSERT INTO locations (id_utilisateur, id_vehicule, date_debut, date_fin, tarif_total, statut_location) 
                                     VALUES (?, ?, ?, ?, ?, 'active')";
                            $stmt = $db->prepare($query);
                            $stmt->execute([
                                $_SESSION['user_id'],
                                $vehiculeId,
                                $item['date_debut'],
                                $item['date_fin'],
                                $item['tarif_location_journalier'] * $item['quantite']
                            ]);
                        }
                        }
                    }

                    $db->commit();
                    $_SESSION['panier'] = ['achat' => [], 'location' => []];
                    $_SESSION['success_message'] = "Votre commande a été enregistrée avec succès !";
                header('Location: ' . url('pages/commandes.php'));
                    exit;

            } catch (Exception $e) {
                    $db->rollBack();
                $_SESSION['error_message'] = "Erreur lors de la commande : " . $e->getMessage();
                }
                break;
        }

    // Redirection pour éviter la resoumission
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
}

// Récupération des véhicules du panier
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
                if ($type === 'achat') {
                    $total += $item['prix'] * $item['quantite'];
                } else {
                    // Pour la location, calculer le nombre de jours
                    $dateDebut = new DateTime($item['date_debut']);
                    $dateFin = new DateTime($item['date_fin']);
                    $nbJours = $dateDebut->diff($dateFin)->days + 1;
                    $total += $item['tarif_location_journalier'] * $item['quantite'] * $nbJours;
                }
            }
        }
    }
    return $total;
}

// Variables de la page
$pageTitle = "Mon panier";
$pageDescription = "Gérez votre panier d'achats et de locations";
$currentPage = 'panier';

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="panier-container">
    <h1 class="page-title">Mon Panier</h1>

            <?php if (empty($vehiculesAchat) && empty($vehiculesLocation)): ?>
        <div class="panier-vide">
            <i class="fas fa-shopping-cart"></i>
                <p>Votre panier est vide</p>
            <a href="<?= url('pages/catalogue/index.php') ?>" class="btn btn-primary">
                <i class="fas fa-car"></i> Découvrir nos véhicules
            </a>
        </div>
    <?php else: ?>
        <!-- Section Achats -->
        <?php if (!empty($vehiculesAchat)): ?>
            <div class="panier-section">
                <h2>Véhicules à acheter</h2>
                <div class="panier-items">
                    <?php foreach ($vehiculesAchat as $vehicule): ?>
                        <div class="panier-item">
                            <div class="item-image">
                                <?php
                                $vehicleId = $vehicule['id_vehicule'];
                                $imageSrc = getVehicleMainImage($vehicleId);
                                ?>
                                <img src="<?= $imageSrc ?>" 
                                     alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>">
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                                <div class="item-specs">
                                    <span><i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?></span>
                                    <span><i class="fas fa-gas-pump"></i> <?= ucfirst($vehicule['carburant']) ?></span>
                                </div>
                                <div class="item-price">
                                    <span class="price"><?= number_format($vehicule['prix'], 2, ',', ' ') ?> €</span>
                                </div>
                                <div class="item-actions">
                                    <div class="quantity-control">
                                        <button class="btn-quantity" onclick="updateQuantity('achat', <?= $vehicule['id_vehicule'] ?>, 'decrease')">-</button>
                                        <input type="number" value="<?= $_SESSION['panier']['achat'][$vehicule['id_vehicule']]['quantite'] ?>" 
                                               min="1" max="<?= $vehicule['stock'] ?>"
                                               onchange="updateQuantity('achat', <?= $vehicule['id_vehicule'] ?>, 'set', this.value)">
                                        <button class="btn-quantity" onclick="updateQuantity('achat', <?= $vehicule['id_vehicule'] ?>, 'increase')">+</button>
                                    </div>
                                    <button class="btn btn-danger" onclick="removeFromCart('achat', <?= $vehicule['id_vehicule'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Section Locations -->
        <?php if (!empty($vehiculesLocation)): ?>
            <div class="panier-section">
                <h2>Véhicules à louer</h2>
                <div class="panier-items">
                    <?php foreach ($vehiculesLocation as $vehicule): ?>
                        <div class="panier-item">
                            <div class="item-image">
                                <?php
                                $vehicleId = $vehicule['id_vehicule'];
                                $imageSrc = getVehicleMainImage($vehicleId);
                                ?>
                                <img src="<?= $imageSrc ?>" 
                                     alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>">
                            </div>
                            <div class="item-details">
                                <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                                <div class="item-specs">
                                    <span><i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?></span>
                                    <span><i class="fas fa-gas-pump"></i> <?= ucfirst($vehicule['carburant']) ?></span>
                                </div>
                                <div class="item-dates">
                                    <div class="date-group">
                                        <label>Du:</label>
                                        <input type="date" value="<?= $_SESSION['panier']['location'][$vehicule['id_vehicule']]['date_debut'] ?>"
                                               onchange="updateDates(<?= $vehicule['id_vehicule'] ?>, 'debut', this.value)">
                                    </div>
                                    <div class="date-group">
                                        <label>Au:</label>
                                        <input type="date" value="<?= $_SESSION['panier']['location'][$vehicule['id_vehicule']]['date_fin'] ?>"
                                               onchange="updateDates(<?= $vehicule['id_vehicule'] ?>, 'fin', this.value)">
                                    </div>
                                </div>
                                <div class="item-price">
                                    <span class="price"><?= number_format($vehicule['tarif_location_journalier'], 2, ',', ' ') ?> €/jour</span>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-danger" onclick="removeFromCart('location', <?= $vehicule['id_vehicule'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Résumé et validation -->
            <div class="panier-summary">
                <div class="summary-content">
                <h3>Résumé de la commande</h3>
                <div class="summary-details">
                    <?php if (!empty($vehiculesAchat)): ?>
                        <div class="summary-line">
                            <span>Total achats:</span>
                            <span><?= number_format(array_sum(array_map(function($item) { 
                                return $item['prix'] * $item['quantite']; 
                            }, $_SESSION['panier']['achat'])), 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($vehiculesLocation)): ?>
                        <div class="summary-line">
                            <span>Total locations:</span>
                            <span><?= number_format(array_sum(array_map(function($item) {
                                $dateDebut = new DateTime($item['date_debut']);
                                $dateFin = new DateTime($item['date_fin']);
                                $nbJours = $dateDebut->diff($dateFin)->days + 1;
                                return $item['tarif_location_journalier'] * $item['quantite'] * $nbJours;
                            }, $_SESSION['panier']['location'])), 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-total">
                        <span>Total:</span>
                        <span><?= number_format(calculateTotal(), 2, ',', ' ') ?> €</span>
                    </div>
                </div>
                <button class="btn btn-primary btn-commander" onclick="commander()">
                    <i class="fas fa-check"></i> Valider la commande
                </button>
            </div>
            </div>
        <?php endif; ?>
    </div>

<style>
.panier-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-title {
    color: var(--primary-color);
    margin-bottom: 2rem;
    font-size: 2.5rem;
    text-align: center;
}

.panier-vide {
    text-align: center;
    padding: 4rem 0;
}

.panier-vide i {
    font-size: 4rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.panier-vide p {
    color: var(--text-muted);
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.panier-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.panier-section h2 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
}

.panier-items {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.panier-item {
    display: flex;
    gap: 2rem;
    padding: 1.5rem;
    background: var(--light);
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.panier-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.item-image {
    width: 200px;
    height: 150px;
    overflow: hidden;
    border-radius: 8px;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.item-details h3 {
    color: var(--primary-color);
    font-size: 1.4rem;
    margin: 0;
}

.item-specs {
    display: flex;
    gap: 1.5rem;
    color: var(--text-muted);
}

.item-specs span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.item-price {
    font-size: 1.3rem;
    color: var(--primary-color);
    font-weight: 600;
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: auto;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-quantity {
    width: 30px;
    height: 30px;
    border: none;
    background: var(--light);
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-quantity:hover {
    background: var(--border-color);
}

.quantity-control input {
    width: 60px;
    text-align: center;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    padding: 0.3rem;
}

.item-dates {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.date-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.date-group label {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.date-group input {
    padding: 0.5rem;
    border: 1px solid var(--border-color);
    border-radius: 5px;
}

.panier-summary {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    position: sticky;
    bottom: 2rem;
}

.summary-content {
    max-width: 500px;
    margin: 0 auto;
}

.summary-content h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    text-align: center;
}

.summary-details {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    color: var(--text-color);
}

.summary-total {
    display: flex;
    justify-content: space-between;
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--primary-color);
    padding-top: 1rem;
    border-top: 2px solid var(--border-color);
}

.btn-commander {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
}

@media (max-width: 992px) {
    .panier-item {
        flex-direction: column;
    }

    .item-image {
        width: 100%;
        height: 200px;
    }

    .item-actions {
        flex-wrap: wrap;
    }
}

@media (max-width: 768px) {
    .panier-container {
        padding: 1rem;
    }

    .item-dates {
        flex-direction: column;
    }

    .summary-content {
        padding: 1rem;
    }
}
</style>

<script>
function updateQuantity(type, vehiculeId, action, value = null) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'modifier_quantite';
    form.appendChild(actionInput);

    const typeInput = document.createElement('input');
    typeInput.name = 'type';
    typeInput.value = type;
    form.appendChild(typeInput);

    const vehiculeInput = document.createElement('input');
    vehiculeInput.name = 'vehicule_id';
    vehiculeInput.value = vehiculeId;
    form.appendChild(vehiculeInput);

    const quantityInput = document.createElement('input');
    quantityInput.name = 'quantite';
    
    const currentQuantity = parseInt(document.querySelector(`input[onchange*="updateQuantity('${type}', ${vehiculeId}"]`).value);
    
    switch(action) {
        case 'increase':
            quantityInput.value = currentQuantity + 1;
            break;
        case 'decrease':
            quantityInput.value = Math.max(1, currentQuantity - 1);
            break;
        case 'set':
            quantityInput.value = parseInt(value) || 1;
            break;
    }
    
    form.appendChild(quantityInput);
    document.body.appendChild(form);
    form.submit();
}

function removeFromCart(type, vehiculeId) {
    if (confirm('Voulez-vous vraiment retirer cet article du panier ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';

        const actionInput = document.createElement('input');
        actionInput.name = 'action';
        actionInput.value = 'supprimer';
        form.appendChild(actionInput);

        const typeInput = document.createElement('input');
        typeInput.name = 'type';
        typeInput.value = type;
        form.appendChild(typeInput);

        const vehiculeInput = document.createElement('input');
        vehiculeInput.name = 'vehicule_id';
        vehiculeInput.value = vehiculeId;
        form.appendChild(vehiculeInput);

        document.body.appendChild(form);
        form.submit();
    }
}

function updateDates(vehiculeId, type, value) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'modifier_dates';
    form.appendChild(actionInput);

    const vehiculeInput = document.createElement('input');
    vehiculeInput.name = 'vehicule_id';
    vehiculeInput.value = vehiculeId;
    form.appendChild(vehiculeInput);

    const dateInput = document.createElement('input');
    dateInput.name = type === 'debut' ? 'date_debut' : 'date_fin';
    dateInput.value = value;
    form.appendChild(dateInput);

    document.body.appendChild(form);
    form.submit();
}

function commander() {
    if (confirm('Voulez-vous confirmer votre commande ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';

        const actionInput = document.createElement('input');
        actionInput.name = 'action';
        actionInput.value = 'commander';
        form.appendChild(actionInput);

        document.body.appendChild(form);
        form.submit();
    }
}

// Initialisation des dates minimales
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.min = today;
    });
});
</script>

<?php
$pageContent = ob_get_clean();
require_once ROOT_PATH . '/includes/template.php';
?> 