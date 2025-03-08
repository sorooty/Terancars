<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Récupération des paramètres de filtrage
$marque = isset($_GET['marque']) ? htmlspecialchars($_GET['marque']) : '';
$modele = isset($_GET['modele']) ? htmlspecialchars($_GET['modele']) : '';
$anneeMin = isset($_GET['annee_min']) ? intval($_GET['annee_min']) : null;
$anneeMax = isset($_GET['annee_max']) ? intval($_GET['annee_max']) : null;
$prixMin = isset($_GET['prix_min']) ? intval($_GET['prix_min']) : null;
$prixMax = isset($_GET['prix_max']) ? intval($_GET['prix_max']) : null;
$carburant = isset($_GET['carburant']) ? htmlspecialchars($_GET['carburant']) : '';
$transmission = isset($_GET['transmission']) ? htmlspecialchars($_GET['transmission']) : '';
$disponibilite = isset($_GET['disponibilite']) ? htmlspecialchars($_GET['disponibilite']) : '';

// Construction de la requête SQL de base
$sql = "SELECT * FROM vehicules WHERE 1=1";
$params = [];

// Ajout des conditions de filtrage
if (!empty($marque)) {
    $sql .= " AND marque = ?";
    $params[] = $marque;
}
if (!empty($modele)) {
    $sql .= " AND modele LIKE ?";
    $params[] = "%$modele%";
}
if ($anneeMin !== null) {
    $sql .= " AND annee >= ?";
    $params[] = $anneeMin;
}
if ($anneeMax !== null) {
    $sql .= " AND annee <= ?";
    $params[] = $anneeMax;
}
if ($prixMin !== null) {
    $sql .= " AND prix >= ?";
    $params[] = $prixMin;
}
if ($prixMax !== null) {
    $sql .= " AND prix <= ?";
    $params[] = $prixMax;
}
if (!empty($carburant)) {
    $sql .= " AND carburant = ?";
    $params[] = $carburant;
}
if (!empty($transmission)) {
    $sql .= " AND transmission = ?";
    $params[] = $transmission;
}
if ($disponibilite === 'achat') {
    $sql .= " AND stock > 0";
} elseif ($disponibilite === 'location') {
    $sql .= " AND disponible_location = 1 AND stock > 0";
}

// Tri
$tri = isset($_GET['tri']) ? htmlspecialchars($_GET['tri']) : 'prix_asc';
switch ($tri) {
    case 'prix_desc':
        $sql .= " ORDER BY prix DESC";
        break;
    case 'annee_desc':
        $sql .= " ORDER BY annee DESC";
        break;
    case 'annee_asc':
        $sql .= " ORDER BY annee ASC";
        break;
    default:
        $sql .= " ORDER BY prix ASC";
}

// Exécution de la requête
$stmt = $db->prepare($sql);
$stmt->execute($params);
$vehicules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des marques distinctes pour le filtre
$stmt = $db->query("SELECT DISTINCT marque FROM vehicules ORDER BY marque");
$marques = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Variables de la page
$pageTitle = "Catalogue des véhicules";
$pageDescription = "Découvrez notre sélection de véhicules disponibles à l'achat et à la location.";
$currentPage = 'catalogue';
$additionalCss = ['css/catalogue.css'];

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="catalogue-container">
    <!-- Section des filtres -->
    <aside class="filtres-sidebar">
        <div class="filtre-section">
            <h3>Filtres</h3>
            <form id="filtres-form" class="filtres-form" method="GET">
                <!-- Marque -->
                <div class="form-group">
                    <label for="marque">Marque</label>
                    <select name="marque" id="marque" class="form-control">
                        <option value="">Toutes les marques</option>
                        <?php foreach ($marques as $m): ?>
                            <option value="<?= $m ?>" <?= $marque === $m ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Modèle -->
                <div class="form-group">
                    <label for="modele">Modèle</label>
                    <input type="text" name="modele" id="modele" class="form-control" 
                           value="<?= $modele ?>" placeholder="Rechercher un modèle">
                </div>

                <!-- Prix -->
                <div class="form-group">
                    <label>Prix</label>
                    <div class="range-inputs">
                        <input type="number" name="prix_min" placeholder="Min" 
                               value="<?= $prixMin ?>" class="form-control">
                        <input type="number" name="prix_max" placeholder="Max" 
                               value="<?= $prixMax ?>" class="form-control">
                    </div>
                </div>

                <!-- Année -->
                <div class="form-group">
                    <label>Année</label>
                    <div class="range-inputs">
                        <input type="number" name="annee_min" placeholder="Min" 
                               value="<?= $anneeMin ?>" class="form-control">
                        <input type="number" name="annee_max" placeholder="Max" 
                               value="<?= $anneeMax ?>" class="form-control">
                    </div>
                </div>

                <!-- Carburant -->
                <div class="form-group">
                    <label for="carburant">Carburant</label>
                    <select name="carburant" id="carburant" class="form-control">
                        <option value="">Tous types</option>
                        <option value="essence" <?= $carburant === 'essence' ? 'selected' : '' ?>>Essence</option>
                        <option value="diesel" <?= $carburant === 'diesel' ? 'selected' : '' ?>>Diesel</option>
                        <option value="electrique" <?= $carburant === 'electrique' ? 'selected' : '' ?>>Électrique</option>
                        <option value="hybride" <?= $carburant === 'hybride' ? 'selected' : '' ?>>Hybride</option>
                    </select>
                </div>

                <!-- Transmission -->
                <div class="form-group">
                    <label for="transmission">Transmission</label>
                    <select name="transmission" id="transmission" class="form-control">
                        <option value="">Toutes</option>
                        <option value="manuelle" <?= $transmission === 'manuelle' ? 'selected' : '' ?>>Manuelle</option>
                        <option value="automatique" <?= $transmission === 'automatique' ? 'selected' : '' ?>>Automatique</option>
                    </select>
                </div>

                <!-- Disponibilité -->
                <div class="form-group">
                    <label for="disponibilite">Disponibilité</label>
                    <select name="disponibilite" id="disponibilite" class="form-control">
                        <option value="">Tous</option>
                        <option value="achat" <?= $disponibilite === 'achat' ? 'selected' : '' ?>>Disponible à l'achat</option>
                        <option value="location" <?= $disponibilite === 'location' ? 'selected' : '' ?>>Disponible à la location</option>
                    </select>
                </div>

                <!-- Tri -->
                <div class="form-group">
                    <label for="tri">Trier par</label>
                    <select name="tri" id="tri" class="form-control">
                        <option value="prix_asc" <?= $tri === 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                        <option value="prix_desc" <?= $tri === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                        <option value="annee_desc" <?= $tri === 'annee_desc' ? 'selected' : '' ?>>Plus récent</option>
                        <option value="annee_asc" <?= $tri === 'annee_asc' ? 'selected' : '' ?>>Plus ancien</option>
                    </select>
                </div>

                <div class="filtres-actions">
                    <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                    <button type="reset" class="btn btn-outline" id="reset-filtres">Réinitialiser</button>
                </div>
            </form>
        </div>
    </aside>

    <!-- Section principale du catalogue -->
    <main class="catalogue-main">
        <div class="catalogue-header">
            <h1><?= $pageTitle ?></h1>
            <p class="results-count"><?= count($vehicules) ?> véhicule(s) trouvé(s)</p>
        </div>

        <?php if (empty($vehicules)): ?>
            <div class="no-results">
                <p>Aucun véhicule ne correspond à vos critères de recherche.</p>
                <a href="<?= url('catalogue/') ?>" class="btn btn-primary">
                    <i class="fas fa-sync"></i>
                    Réinitialiser les filtres
                </a>
            </div>
        <?php else: ?>
            <div class="vehicules-grid">
                <?php foreach ($vehicules as $vehicule): ?>
                    <div class="vehicule-card">
                        <div class="vehicule-image">
                            <?php
                            $imagePath = 'images/vehicules/' . strtolower($vehicule['marque']) . '/' . strtolower($vehicule['modele']) . '.jpg';
                            ?>
                            <img src="<?= asset($imagePath) ?>" 
                                 alt="<?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?>"
                                 onerror="this.src='<?= asset('images/vehicules/default-car.jpg') ?>'">
                            <?php if ($vehicule['stock'] > 0): ?>
                                <span class="badge badge-success">Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Indisponible</span>
                            <?php endif; ?>
                        </div>
                        <div class="vehicule-info">
                            <h3><?= htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']) ?></h3>
                            <div class="vehicule-specs">
                                <span><i class="fas fa-calendar"></i> <?= $vehicule['annee'] ?></span>
                                <span><i class="fas fa-gas-pump"></i> <?= ucfirst($vehicule['carburant']) ?></span>
                                <span><i class="fas fa-cog"></i> <?= ucfirst($vehicule['transmission']) ?></span>
                            </div>
                            <div class="vehicule-prix">
                                <div class="prix-achat">
                                    <span class="label">Prix d'achat</span>
                                    <span class="montant"><?= number_format($vehicule['prix'], 0, ',', ' ') ?> €</span>
                                </div>
                                <?php if ($vehicule['disponible_location'] && $vehicule['tarif_location_journalier'] > 0): ?>
                                    <div class="prix-location">
                                        <span class="label">Location</span>
                                        <span class="montant"><?= number_format($vehicule['tarif_location_journalier'], 0, ',', ' ') ?> €/jour</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="vehicule-actions">
                                <a href="<?= url('vehicule/detail?id_vehicule=' . $vehicule['id_vehicule']) ?>" class="btn btn-primary">
                                    <i class="fas fa-info-circle"></i> Voir les détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 