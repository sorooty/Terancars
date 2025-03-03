<?php
/**
 * Page catalogue
 * Affiche la liste des véhicules disponibles avec filtres
 */

// Définition du titre de la page
$pageTitle = "Catalogue";

// Inclusion des fichiers nécessaires
include '../config/config.php'; // Connexion à la DB
include '../includes/header.php'; // En-tête du site

// Initialisation des variables de filtrage
$where_clauses = [];
$params = [];
$types = "";

// Vérifier si la connexion à la base de données est établie
if (!isset($conn) || $conn->connect_error) {
    echo '<div class="alert alert-danger">Erreur de connexion à la base de données : ' . (isset($conn) ? $conn->connect_error : 'Connexion non établie') . '</div>';
    $conn_error = true;
} else {
    $conn_error = false;
}

// Vérifier si la table vehicules existe
$table_exists = false;
if (!$conn_error) {
    if (function_exists('tableExists')) {
        $table_exists = tableExists($conn, 'vehicules');
    } else {
        // Méthode alternative pour vérifier si la table existe
        $check_table = $conn->query("SHOW TABLES LIKE 'vehicules'");
        $table_exists = ($check_table && $check_table->num_rows > 0);
    }
}

// Traitement des filtres si soumis
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filter'])) {
    // Filtre par marque
    if (!empty($_GET['marque'])) {
        $where_clauses[] = "marque = ?";
        $params[] = $_GET['marque'];
        $types .= "s";
    }
    
    // Filtre par carburant
    if (!empty($_GET['carburant'])) {
        $where_clauses[] = "carburant = ?";
        $params[] = $_GET['carburant'];
        $types .= "s";
    }
    
    // Filtre par transmission
    if (!empty($_GET['transmission'])) {
        $where_clauses[] = "transmission = ?";
        $params[] = $_GET['transmission'];
        $types .= "s";
    }
    
    // Filtre par année
    if (!empty($_GET['annee_min'])) {
        $where_clauses[] = "annee >= ?";
        $params[] = $_GET['annee_min'];
        $types .= "i";
    }
    
    // Filtre par prix
    if (!empty($_GET['prix_max']) && $_GET['prix_max'] > 0) {
        // Vérifier si la colonne prix_achat existe
        $prix_column = 'prix_achat';
        if (function_exists('columnExists')) {
            if (!columnExists($conn, 'vehicules', 'prix_achat')) {
                // Essayer avec la colonne 'prix' si prix_achat n'existe pas
                if (columnExists($conn, 'vehicules', 'prix')) {
                    $prix_column = 'prix';
                }
            }
        } else {
            // Méthode alternative pour vérifier si la colonne existe
            $check_column = $conn->query("SHOW COLUMNS FROM vehicules LIKE 'prix_achat'");
            if (!$check_column || $check_column->num_rows === 0) {
                $check_prix = $conn->query("SHOW COLUMNS FROM vehicules LIKE 'prix'");
                if ($check_prix && $check_prix->num_rows > 0) {
                    $prix_column = 'prix';
                }
            }
        }
        
        $where_clauses[] = "$prix_column <= ?";
        $params[] = $_GET['prix_max'];
        $types .= "d";
    }
    
    // Filtre par disponibilité pour location
    if (isset($_GET['disponible_location']) && $_GET['disponible_location'] == 1) {
        $where_clauses[] = "disponible_location = 1";
    }
}

// Construction de la requête SQL
$sql = "SELECT * FROM vehicules";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY id_vehicule DESC";

// Préparation et exécution de la requête
if ($table_exists) {
    $stmt = $conn->prepare($sql);
    
    // Vérifier si la préparation a réussi
    if ($stmt === false) {
        // Afficher un message d'erreur
        echo '<div class="alert alert-danger">Erreur de préparation de la requête : ' . $conn->error . '</div>';
    } else {
        // La préparation a réussi, continuer avec le binding des paramètres
        if (!empty($params)) {
            // Utilisation de call_user_func_array pour éviter les problèmes avec bind_param
            $bind_names[] = $types;
            for ($i = 0; $i < count($params); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $params[$i];
                $bind_names[] = &$$bind_name;
            }
            call_user_func_array(array($stmt, 'bind_param'), $bind_names);
        }
        $stmt->execute();
        $result = $stmt->get_result();
    }
} else {
    // La table n'existe pas, initialiser $result à null
    $result = null;
    echo '<div class="alert alert-warning">La table des véhicules n\'existe pas ou n\'est pas accessible.</div>';
}

// Récupération des valeurs distinctes pour les filtres
$marques = [];
$carburants = [];
$transmissions = [];

if ($table_exists) {
    // Récupération des marques
    $query_marques = "SELECT DISTINCT marque FROM vehicules ORDER BY marque";
    $result_marques = $conn->query($query_marques);
    if ($result_marques) {
        while ($row = $result_marques->fetch_assoc()) {
            $marques[] = $row['marque'];
        }
    }
    
    // Récupération des carburants
    $query_carburants = "SELECT DISTINCT carburant FROM vehicules ORDER BY carburant";
    $result_carburants = $conn->query($query_carburants);
    if ($result_carburants) {
        while ($row = $result_carburants->fetch_assoc()) {
            $carburants[] = $row['carburant'];
        }
    }
    
    // Récupération des transmissions
    $query_transmissions = "SELECT DISTINCT transmission FROM vehicules ORDER BY transmission";
    $result_transmissions = $conn->query($query_transmissions);
    if ($result_transmissions) {
        while ($row = $result_transmissions->fetch_assoc()) {
            $transmissions[] = $row['transmission'];
        }
    }
}
?>

<!-- En-tête de la page catalogue -->
<div class="catalogue-header">
    <div class="container">
        <h1>Catalogue de véhicules</h1>
        <p>Découvrez notre sélection de véhicules disponibles à l'achat ou à la location</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Colonne des filtres (à gauche) -->
        <div class="col-md-3">
            <div class="filtres">
                <h2>Filtres</h2>
                <form action="" method="GET">
                    <input type="hidden" name="filter" value="1">
                    
                    <div class="filtre-groupe">
                        <label for="marque">Marque</label>
                        <select name="marque" id="marque">
                            <option value="">Toutes les marques</option>
                            <?php foreach ($marques as $marque): ?>
                                <option value="<?php echo htmlspecialchars($marque); ?>" <?php echo (isset($_GET['marque']) && $_GET['marque'] == $marque) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($marque); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filtre-groupe">
                        <label for="carburant">Carburant</label>
                        <select name="carburant" id="carburant">
                            <option value="">Tous les carburants</option>
                            <?php foreach ($carburants as $carburant): ?>
                                <option value="<?php echo htmlspecialchars($carburant); ?>" <?php echo (isset($_GET['carburant']) && $_GET['carburant'] == $carburant) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($carburant); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filtre-groupe">
                        <label for="transmission">Transmission</label>
                        <select name="transmission" id="transmission">
                            <option value="">Toutes les transmissions</option>
                            <?php foreach ($transmissions as $transmission): ?>
                                <option value="<?php echo htmlspecialchars($transmission); ?>" <?php echo (isset($_GET['transmission']) && $_GET['transmission'] == $transmission) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($transmission); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filtre-groupe">
                        <label for="annee_min">Année minimum</label>
                        <select name="annee_min" id="annee_min">
                            <option value="">Toutes les années</option>
                            <?php for ($i = date("Y"); $i >= 2000; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo (isset($_GET['annee_min']) && $_GET['annee_min'] == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filtre-groupe">
                        <label for="prix_max">Prix maximum</label>
                        <input type="number" name="prix_max" id="prix_max" min="0" step="1000" value="<?php echo isset($_GET['prix_max']) ? htmlspecialchars($_GET['prix_max']) : ''; ?>" placeholder="Prix maximum">
                    </div>
                    
                    <div class="filtre-checkbox">
                        <input type="checkbox" name="disponible_location" id="disponible_location" value="1" <?php echo (isset($_GET['disponible_location']) && $_GET['disponible_location'] == 1) ? 'checked' : ''; ?>>
                        <label for="disponible_location">Disponible à la location</label>
                    </div>
                    
                    <div class="filtre-actions">
                        <button type="submit" class="btn-filtre">Appliquer les filtres</button>
                        <a href="catalogue.php" class="btn-reset">Réinitialiser</a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Colonne des véhicules (à droite) -->
        <div class="col-md-9">
            <?php 
            // Vérifier si $result est défini
            if (!isset($result) && isset($stmt) && $stmt !== false) {
                $stmt->execute();
                $result = $stmt->get_result();
            }
            
            if (isset($result) && $result && $result->num_rows > 0): 
            ?>
                <div class="vehicules-container">
                    <?php while ($vehicule = $result->fetch_assoc()): ?>
                        <div class="vehicule-card">
                            <div class="vehicule-image">
                                <?php
                                // Récupérer l'image principale du véhicule
                                $id_vehicule = $vehicule['id_vehicule'];
                                $image_path = '../assets/images/default-car.jpg'; // Image par défaut
                                
                                // Vérifier si la table images_vehicules existe
                                $images_table_exists = false;
                                if (!$conn_error) {
                                    $check_images_table = $conn->query("SHOW TABLES LIKE 'images_vehicules'");
                                    $images_table_exists = ($check_images_table && $check_images_table->num_rows > 0);
                                }
                                
                                if ($images_table_exists) {
                                    $image_query = "SELECT chemin_image FROM images_vehicules WHERE id_vehicule = ? LIMIT 1";
                                    $image_stmt = $conn->prepare($image_query);
                                    
                                    if ($image_stmt) {
                                        $image_stmt->bind_param("i", $id_vehicule);
                                        $image_stmt->execute();
                                        $image_result = $image_stmt->get_result();
                                        
                                        if ($image_result && $image_result->num_rows > 0) {
                                            $image = $image_result->fetch_assoc();
                                            $image_path = $image['chemin_image'];
                                        }
                                        $image_stmt->close();
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']); ?>">
                            </div>
                            <div class="vehicule-info">
                                <h3><?php echo htmlspecialchars($vehicule['marque'] . ' ' . $vehicule['modele']); ?></h3>
                                <p class="vehicule-annee"><?php echo isset($vehicule['annee']) ? htmlspecialchars($vehicule['annee']) : 'Année non spécifiée'; ?></p>
                                <div class="vehicule-specs">
                                    <?php if (isset($vehicule['carburant'])): ?>
                                    <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($vehicule['carburant']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($vehicule['transmission'])): ?>
                                    <span><i class="fas fa-cogs"></i> <?php echo htmlspecialchars($vehicule['transmission']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($vehicule['kilometrage'])): ?>
                                    <span><i class="fas fa-road"></i> <?php echo number_format($vehicule['kilometrage'], 0, ',', ' '); ?> km</span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($vehicule['stock'])): ?>
                                    <span><i class="fas fa-warehouse"></i> Stock: <?php echo htmlspecialchars($vehicule['stock']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="vehicule-price">
                                    <?php 
                                    // Vérifier si le véhicule est disponible à la location
                                    $location_disponible = isset($vehicule['disponible_location']) && $vehicule['disponible_location'] == 1;
                                    
                                    // Vérifier si le véhicule est disponible à l'achat
                                    $achat_disponible = isset($vehicule['disponible_achat']) && $vehicule['disponible_achat'] == 1;
                                    
                                    if ($achat_disponible): 
                                        // Vérifier si la colonne prix_achat existe, sinon utiliser prix
                                        $prix_achat = isset($vehicule['prix_achat']) ? $vehicule['prix_achat'] : (isset($vehicule['prix']) ? $vehicule['prix'] : 0);
                                    ?>
                                        <p class="price-achat"><?php echo number_format($prix_achat, 0, ',', ' '); ?> €</p>
                                    <?php endif; ?>
                                    
                                    <?php if ($location_disponible): 
                                        // Vérifier si la colonne prix_location existe, sinon utiliser un prix par défaut
                                        $prix_location = isset($vehicule['prix_location']) ? $vehicule['prix_location'] : (isset($vehicule['prix_jour']) ? $vehicule['prix_jour'] : 0);
                                    ?>
                                        <p class="price-location">Location: <?php echo number_format($prix_location, 0, ',', ' '); ?> €/jour</p>
                                    <?php endif; ?>
                                </div>
                                <div class="vehicle-actions">
                                    <a href="details.php?id=<?php echo $vehicule['id_vehicule']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye"></i> Voir détails
                                    </a>
                                    <a href="panier.php?action=ajouter&id=<?php echo $vehicule['id_vehicule']; ?>&type=achat" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart"></i> Acheter
                                    </a>
                                    <?php if (isset($vehicule['disponible_location']) && $vehicule['disponible_location'] == 1) : ?>
                                    <a href="panier.php?action=ajouter&id=<?php echo $vehicule['id_vehicule']; ?>&type=location" class="btn btn-secondary">
                                        <i class="fas fa-key"></i> Louer
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h3>Aucun véhicule trouvé</h3>
                    <p>Aucun véhicule ne correspond à vos critères de recherche. Veuillez modifier vos filtres.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>