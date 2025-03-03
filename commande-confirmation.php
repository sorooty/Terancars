<?php
require_once 'config/config.php';
require_once 'includes/database_operations.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('/login.php');
}

// Vérifier si l'ID de la commande est fourni
if (!isset($_GET['id'])) {
    redirect('/panier.php');
}

$commandeId = intval($_GET['id']);

// Récupérer les détails de la commande
$stmt = $conn->prepare("
    SELECT c.*, u.nom, u.prenom, u.email 
    FROM commandes c 
    JOIN utilisateurs u ON c.id_client = u.id_utilisateur 
    WHERE c.id_commande = ? AND c.id_client = ?
");
$stmt->bind_param("ii", $commandeId, $_SESSION['user_id']);
$stmt->execute();
$commande = $stmt->get_result()->fetch_assoc();

// Vérifier si la commande existe et appartient à l'utilisateur
if (!$commande) {
    redirect('/panier.php');
}

// Récupérer les détails des produits de la commande
$stmt = $conn->prepare("
    SELECT dc.*, v.marque, v.modele 
    FROM details_commandes dc 
    JOIN vehicules v ON dc.id_produit = v.id_vehicule 
    WHERE dc.id_commande = ?
");
$stmt->bind_param("i", $commandeId);
$stmt->execute();
$detailsCommande = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Récupérer les locations associées à la commande
$stmt = $conn->prepare("
    SELECT l.*, v.marque, v.modele 
    FROM locations l 
    JOIN vehicules v ON l.id_produit = v.id_vehicule 
    WHERE l.id_commande = ?
");
$stmt->bind_param("i", $commandeId);
$stmt->execute();
$locations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = "Confirmation de commande";
require_once 'includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-check-circle"></i> 
                        Commande #<?php echo $commandeId; ?> confirmée
                    </h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        Merci pour votre commande ! Un email de confirmation a été envoyé à <?php echo htmlspecialchars($commande['email']); ?>
                    </div>

                    <!-- Informations client -->
                    <div class="mb-4">
                        <h2 class="h5">Informations client</h2>
                        <p class="mb-1">
                            <strong>Nom :</strong> 
                            <?php echo htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']); ?>
                        </p>
                        <p class="mb-1">
                            <strong>Email :</strong> 
                            <?php echo htmlspecialchars($commande['email']); ?>
                        </p>
                        <p class="mb-1">
                            <strong>Date de commande :</strong> 
                            <?php echo date('d/m/Y H:i', strtotime($commande['date_commande'])); ?>
                        </p>
                    </div>

                    <!-- Détails de la commande -->
                    <?php if (!empty($detailsCommande)): ?>
                        <div class="mb-4">
                            <h2 class="h5">Véhicules achetés</h2>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Véhicule</th>
                                            <th>Prix unitaire</th>
                                            <th>Quantité</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detailsCommande as $detail): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($detail['marque'] . ' ' . $detail['modele']); ?>
                                                </td>
                                                <td><?php echo formatPrice($detail['prix_unitaire']); ?></td>
                                                <td><?php echo $detail['quantite']; ?></td>
                                                <td>
                                                    <?php echo formatPrice($detail['prix_unitaire'] * $detail['quantite']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Détails des locations -->
                    <?php if (!empty($locations)): ?>
                        <div class="mb-4">
                            <h2 class="h5">Véhicules loués</h2>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Véhicule</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($locations as $location): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($location['marque'] . ' ' . $location['modele']); ?>
                                                </td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($location['date_debut'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($location['date_fin'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo formatPrice($location['tarif_total']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Résumé des coûts -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Sous-total:</td>
                                    <td class="text-end">
                                        <?php echo formatPrice($commande['montant_total'] / 1.2); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>TVA (20%):</td>
                                    <td class="text-end">
                                        <?php echo formatPrice($commande['montant_total'] - ($commande['montant_total'] / 1.2)); ?>
                                    </td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total:</td>
                                    <td class="text-end">
                                        <?php echo formatPrice($commande['montant_total']); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="/vehicules.php" class="btn btn-outline-primary">
                            <i class="fas fa-car"></i> Continuer les achats
                        </a>
                        <a href="/mes-commandes.php" class="btn btn-primary">
                            <i class="fas fa-list"></i> Voir mes commandes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 