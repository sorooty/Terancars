<?php
/**
 * Page de paiement
 * Gère le processus de paiement pour les achats et les réservations de location
 */

// Définition du titre de la page
$pageTitle = "Paiement";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    setAlert("Vous devez être connecté pour accéder à la page de paiement.", "warning");
    redirect("login.php?redirect=checkout.php");
    exit;
}

// Vérifier le type de commande (achat ou location)
$type = isset($_GET['type']) && $_GET['type'] === 'location' ? 'location' : 'achat';

// Vérifier si le panier est vide
if (empty($_SESSION['panier'][$type])) {
    setAlert("Votre panier est vide.", "warning");
    redirect("panier.php");
    exit;
}

// Vérifier si les tables nécessaires existent
$vehiculesTableExists = tableExists($conn, 'vehicules');
$commandesTableExists = tableExists($conn, 'commandes');
$locationsTableExists = tableExists($conn, 'locations');
$debugMode = isset($_GET['debug']) ? true : false;

// Récupérer les informations de l'utilisateur connecté
$user = getCurrentUser();

// Récupérer les véhicules du panier
$vehicules = [];
$total = 0;

if ($vehiculesTableExists) {
    $ids = implode(',', array_map('intval', $_SESSION['panier'][$type]));
    $query = "SELECT * FROM vehicules WHERE id_vehicule IN ($ids)";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($type === 'location') {
                // Vérifier si le prix_location existe, sinon utiliser un pourcentage du prix d'achat
                $prixLocation = isset($row['prix_location']) ? $row['prix_location'] : ($row['prix'] * 0.02);
                $row['prix_location'] = $prixLocation;
                $total += $prixLocation;
            } else {
                $total += $row['prix'];
            }
            $vehicules[] = $row;
        }
    }
}

// Traitement du formulaire de paiement
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des champs
    if (empty($_POST['card_number'])) {
        $errors[] = "Le numéro de carte est requis.";
    } elseif (!preg_match('/^\d{16}$/', str_replace(' ', '', $_POST['card_number']))) {
        $errors[] = "Le numéro de carte doit contenir 16 chiffres.";
    }
    
    if (empty($_POST['card_name'])) {
        $errors[] = "Le nom sur la carte est requis.";
    }
    
    if (empty($_POST['expiry_date'])) {
        $errors[] = "La date d'expiration est requise.";
    } elseif (!preg_match('/^\d{2}\/\d{2}$/', $_POST['expiry_date'])) {
        $errors[] = "La date d'expiration doit être au format MM/YY.";
    }
    
    if (empty($_POST['cvv'])) {
        $errors[] = "Le code de sécurité est requis.";
    } elseif (!preg_match('/^\d{3,4}$/', $_POST['cvv'])) {
        $errors[] = "Le code de sécurité doit contenir 3 ou 4 chiffres.";
    }
    
    // Pour les locations, vérifier les dates
    if ($type === 'location') {
        if (empty($_POST['date_debut'])) {
            $errors[] = "La date de début de location est requise.";
        }
        
        if (empty($_POST['date_fin'])) {
            $errors[] = "La date de fin de location est requise.";
        }
        
        if (!empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
            $dateDebut = new DateTime($_POST['date_debut']);
            $dateFin = new DateTime($_POST['date_fin']);
            
            if ($dateDebut >= $dateFin) {
                $errors[] = "La date de fin doit être postérieure à la date de début.";
            }
            
            $today = new DateTime();
            if ($dateDebut < $today) {
                $errors[] = "La date de début ne peut pas être dans le passé.";
            }
            
            // Calculer le nombre de jours
            $interval = $dateDebut->diff($dateFin);
            $nbJours = $interval->days;
            
            if ($nbJours < 1) {
                $errors[] = "La durée minimale de location est d'un jour.";
            }
            
            // Calculer le total pour la période
            $totalPeriode = $total * $nbJours;
        }
    }
    
    // Si pas d'erreurs, procéder au paiement
    if (empty($errors)) {
        // Simuler un paiement réussi
        $paiementReussi = true;
        
        if ($paiementReussi) {
            // Enregistrer la commande ou la location dans la base de données
            if ($type === 'achat' && $commandesTableExists) {
                // Générer un numéro de commande unique
                $numeroCommande = 'CMD-' . time() . '-' . $user['id_utilisateur'];
                
                // Insérer la commande
                $query = "INSERT INTO commandes (numero_commande, id_utilisateur, date_commande, montant_total, statut) 
                          VALUES (?, ?, NOW(), ?, 'payée')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sid", $numeroCommande, $user['id_utilisateur'], $total);
                
                if ($stmt->execute()) {
                    $idCommande = $conn->insert_id;
                    
                    // Insérer les détails de la commande
                    foreach ($vehicules as $vehicule) {
                        $query = "INSERT INTO details_commande (id_commande, id_vehicule, prix_unitaire, quantite) 
                                  VALUES (?, ?, ?, 1)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("iid", $idCommande, $vehicule['id_vehicule'], $vehicule['prix']);
                        $stmt->execute();
                        
                        // Mettre à jour le stock du véhicule
                        $query = "UPDATE vehicules SET en_stock = 0 WHERE id_vehicule = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $vehicule['id_vehicule']);
                        $stmt->execute();
                    }
                    
                    // Vider le panier d'achat
                    $_SESSION['panier']['achat'] = [];
                    
                    // Message de succès
                    setAlert("Votre commande a été traitée avec succès. Numéro de commande : " . $numeroCommande, "success");
                    $success = true;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'enregistrement de votre commande.";
                    if ($debugMode) {
                        $errors[] = "Erreur SQL : " . $conn->error;
                    }
                }
            } elseif ($type === 'location' && $locationsTableExists) {
                // Générer un numéro de réservation unique
                $numeroReservation = 'LOC-' . time() . '-' . $user['id_utilisateur'];
                
                // Insérer la réservation
                $query = "INSERT INTO locations (numero_reservation, id_utilisateur, date_reservation, date_debut, date_fin, montant_total, statut) 
                          VALUES (?, ?, NOW(), ?, ?, ?, 'confirmée')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sissd", $numeroReservation, $user['id_utilisateur'], $_POST['date_debut'], $_POST['date_fin'], $totalPeriode);
                
                if ($stmt->execute()) {
                    $idLocation = $conn->insert_id;
                    
                    // Insérer les détails de la location
                    foreach ($vehicules as $vehicule) {
                        $prixJournalier = $vehicule['prix_location'];
                        $query = "INSERT INTO details_location (id_location, id_vehicule, prix_journalier) 
                                  VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("iid", $idLocation, $vehicule['id_vehicule'], $prixJournalier);
                        $stmt->execute();
                    }
                    
                    // Vider le panier de location
                    $_SESSION['panier']['location'] = [];
                    
                    // Message de succès
                    setAlert("Votre réservation a été confirmée. Numéro de réservation : " . $numeroReservation, "success");
                    $success = true;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'enregistrement de votre réservation.";
                    if ($debugMode) {
                        $errors[] = "Erreur SQL : " . $conn->error;
                    }
                }
            } else {
                $errors[] = "Impossible d'enregistrer votre " . ($type === 'achat' ? "commande" : "réservation") . ". Table non disponible.";
            }
        } else {
            $errors[] = "Le paiement a échoué. Veuillez vérifier vos informations bancaires et réessayer.";
        }
    }
}
?>

<!-- Affichage des alertes -->
<?php echo getAlert(); ?>

<!-- Contenu principal -->
<div class="container checkout-container">
    <h1><?php echo $type === 'achat' ? "Finaliser votre commande" : "Réserver votre location"; ?></h1>
    
    <?php if ($success) { ?>
        <div class="checkout-success">
            <i class="fas fa-check-circle fa-4x"></i>
            <h2>Merci pour votre <?php echo $type === 'achat' ? "commande" : "réservation"; ?> !</h2>
            <p>Votre <?php echo $type === 'achat' ? "commande" : "réservation"; ?> a été traitée avec succès.</p>
            <div class="success-actions">
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                <a href="catalogue.php" class="btn btn-secondary">Continuer vos achats</a>
            </div>
        </div>
    <?php } else { ?>
        <?php if (!empty($errors)) { ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        
        <div class="checkout-content">
            <!-- Récapitulatif de la commande -->
            <div class="checkout-summary">
                <h2>Récapitulatif</h2>
                
                <div class="summary-items">
                    <?php foreach ($vehicules as $vehicule) { 
                        // Récupérer l'image principale du véhicule
                        $imagePath = "/DaCar/assets/images/vehicles/" . $vehicule['id_vehicule'] . "_1.jpg";
                        // Image par défaut si non disponible
                        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                            $imagePath = "/DaCar/assets/images/no-image.jpg";
                        }
                    ?>
                        <div class="summary-item">
                            <div class="item-image">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $vehicule['marque'] . ' ' . $vehicule['modele']; ?>">
                            </div>
                            <div class="item-details">
                                <h3><?php echo $vehicule['marque'] . " " . $vehicule['modele']; ?></h3>
                                <div class="item-specs">
                                    <?php if (isset($vehicule['annee'])) { ?>
                                        <span><i class="fas fa-calendar-alt"></i> <?php echo $vehicule['annee']; ?></span>
                                    <?php } ?>
                                    <?php if (isset($vehicule['carburant'])) { ?>
                                        <span><i class="fas fa-gas-pump"></i> <?php echo $vehicule['carburant']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="item-price">
                                <?php if ($type === 'location') { ?>
                                    <span class="price"><?php echo formatPrice($vehicule['prix_location']); ?> / jour</span>
                                <?php } else { ?>
                                    <span class="price"><?php echo formatPrice($vehicule['prix']); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                
                <?php if ($type === 'location') { ?>
                    <div class="location-dates">
                        <h3>Période de location</h3>
                        <div class="dates-form">
                            <div class="form-group">
                                <label for="date_debut">Date de début</label>
                                <input type="date" id="date_debut" name="date_debut" min="<?php echo date('Y-m-d'); ?>" class="form-control" form="payment-form" required>
                            </div>
                            <div class="form-group">
                                <label for="date_fin">Date de fin</label>
                                <input type="date" id="date_fin" name="date_fin" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" class="form-control" form="payment-form" required>
                            </div>
                        </div>
                        <div class="location-total">
                            <p>Prix journalier: <span id="prix-journalier"><?php echo formatPrice($total); ?></span></p>
                            <p>Nombre de jours: <span id="nb-jours">0</span></p>
                            <p class="total">Total: <span id="total-location"><?php echo formatPrice(0); ?></span></p>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="summary-total">
                        <p>Total: <span><?php echo formatPrice($total); ?></span></p>
                    </div>
                <?php } ?>
            </div>
            
            <!-- Formulaire de paiement -->
            <div class="checkout-payment">
                <h2>Informations de paiement</h2>
                
                <form id="payment-form" method="post" action="">
                    <div class="form-group">
                        <label for="card_number">Numéro de carte</label>
                        <input type="text" id="card_number" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="card_name">Nom sur la carte</label>
                        <input type="text" id="card_name" name="card_name" class="form-control" placeholder="John Doe" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="expiry_date">Date d'expiration</label>
                            <input type="text" id="expiry_date" name="expiry_date" class="form-control" placeholder="MM/YY" required>
                        </div>
                        
                        <div class="form-group half">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="panier.php" class="btn btn-secondary">Retour au panier</a>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $type === 'achat' ? "Payer maintenant" : "Confirmer la réservation"; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<!-- JavaScript pour le calcul du prix de location -->
<?php if ($type === 'location') { ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const nbJours = document.getElementById('nb-jours');
    const totalLocation = document.getElementById('total-location');
    const prixJournalier = <?php echo $total; ?>;
    
    function calculerTotal() {
        if (dateDebut.value && dateFin.value) {
            const debut = new Date(dateDebut.value);
            const fin = new Date(dateFin.value);
            
            // Vérifier que la date de fin est après la date de début
            if (fin > debut) {
                // Calculer le nombre de jours
                const diffTime = Math.abs(fin - debut);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                nbJours.textContent = diffDays;
                totalLocation.textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(prixJournalier * diffDays);
            } else {
                nbJours.textContent = '0';
                totalLocation.textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(0);
            }
        }
    }
    
    dateDebut.addEventListener('change', calculerTotal);
    dateFin.addEventListener('change', calculerTotal);
});
</script>
<?php } ?>

<style>
/* Styles pour la page de paiement */
.checkout-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.checkout-success {
    text-align: center;
    padding: 3rem 0;
    color: #333;
}

.checkout-success i {
    color: #28a745;
    margin-bottom: 1.5rem;
}

.checkout-success h2 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
}

.success-actions {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.checkout-summary, .checkout-payment {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.checkout-summary h2, .checkout-payment h2 {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.8rem;
    border-bottom: 1px solid #eee;
}

.summary-items {
    margin-bottom: 1.5rem;
}

.summary-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item .item-image {
    width: 80px;
    height: 60px;
    overflow: hidden;
    border-radius: 4px;
    margin-right: 1rem;
}

.summary-item .item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.summary-item .item-details {
    flex: 1;
}

.summary-item .item-details h3 {
    font-size: 1rem;
    margin: 0 0 0.3rem;
}

.summary-item .item-specs {
    display: flex;
    gap: 1rem;
    font-size: 0.85rem;
    color: #666;
}

.summary-item .item-price {
    font-weight: 600;
    font-size: 1.1rem;
}

.summary-total, .location-total {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
    text-align: right;
    font-size: 1.1rem;
}

.summary-total p, .location-total p {
    margin-bottom: 0.5rem;
}

.summary-total span, .location-total .total {
    font-weight: 600;
    font-size: 1.3rem;
    color: #4a6cf7;
}

.location-dates {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.location-dates h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.dates-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-row {
    display: flex;
    gap: 1rem;
}

.form-group.half {
    flex: 1;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
}

@media (max-width: 992px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
    
    .checkout-summary {
        order: 1;
    }
    
    .checkout-payment {
        order: 0;
    }
}

@media (max-width: 768px) {
    .dates-form {
        flex-direction: column;
    }
    
    .form-row {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
        text-align: center;
    }
}
</style>

<?php include '../includes/footer.php'; ?> 