<?php
/**
 * Page de test de connexion
 * Permet de vérifier que la connexion à la base de données fonctionne correctement
 * et que les fonctionnalités de base sont opérationnelles
 */

// Définition du titre de la page
$pageTitle = "Test de connexion";

// Inclusion des fichiers nécessaires
include '../config/config.php';
include '../includes/header.php';

// Initialisation des variables
$tests = [];
$allTestsPassed = true;

// Test 1: Vérifier la connexion à la base de données
$tests[] = [
    'name' => 'Connexion à la base de données',
    'passed' => !$conn->connect_error,
    'message' => $conn->connect_error ? "Erreur: " . $conn->connect_error : "Connexion établie avec succès"
];

if ($conn->connect_error) {
    $allTestsPassed = false;
}

// Test 2: Vérifier l'existence des tables principales
$requiredTables = ['utilisateurs', 'vehicules', 'commandes', 'locations'];
$tablesTest = [
    'name' => 'Existence des tables principales',
    'passed' => true,
    'details' => []
];

foreach ($requiredTables as $table) {
    $exists = tableExists($conn, $table);
    $tablesTest['details'][] = [
        'name' => $table,
        'exists' => $exists
    ];
    
    if (!$exists) {
        $tablesTest['passed'] = false;
        $allTestsPassed = false;
    }
}

$tablesTest['message'] = $tablesTest['passed'] ? 
    "Toutes les tables requises existent" : 
    "Certaines tables requises n'existent pas";

$tests[] = $tablesTest;

// Test 3: Vérifier les fonctions essentielles
$functionsTest = [
    'name' => 'Fonctions essentielles',
    'passed' => true,
    'details' => []
];

// Test de la fonction isLoggedIn
$functionsTest['details'][] = [
    'name' => 'isLoggedIn()',
    'works' => function_exists('isLoggedIn'),
    'result' => function_exists('isLoggedIn') ? (isLoggedIn() ? 'Utilisateur connecté' : 'Utilisateur non connecté') : 'Fonction non définie'
];

// Test de la fonction setAlert et getAlert
if (function_exists('setAlert') && function_exists('getAlert')) {
    setAlert("Ceci est un test d'alerte", "info");
    $alert = getAlert();
    $alertWorks = $alert && $alert['message'] == "Ceci est un test d'alerte" && $alert['type'] == "info";
    
    $functionsTest['details'][] = [
        'name' => 'setAlert() et getAlert()',
        'works' => $alertWorks,
        'result' => $alertWorks ? 'Fonctionnent correctement' : 'Ne fonctionnent pas correctement'
    ];
    
    if (!$alertWorks) {
        $functionsTest['passed'] = false;
        $allTestsPassed = false;
    }
} else {
    $functionsTest['details'][] = [
        'name' => 'setAlert() et getAlert()',
        'works' => false,
        'result' => 'Une ou plusieurs fonctions non définies'
    ];
    
    $functionsTest['passed'] = false;
    $allTestsPassed = false;
}

// Test de la fonction tableExists
$functionsTest['details'][] = [
    'name' => 'tableExists()',
    'works' => function_exists('tableExists'),
    'result' => function_exists('tableExists') ? 'Fonctionne correctement' : 'Fonction non définie'
];

if (!function_exists('tableExists')) {
    $functionsTest['passed'] = false;
    $allTestsPassed = false;
}

$functionsTest['message'] = $functionsTest['passed'] ? 
    "Toutes les fonctions essentielles fonctionnent" : 
    "Certaines fonctions essentielles ne fonctionnent pas";

$tests[] = $functionsTest;

// Test 4: Vérifier l'accès aux données
$dataTest = [
    'name' => 'Accès aux données',
    'passed' => true,
    'details' => []
];

// Test de récupération des véhicules
$query = "SELECT COUNT(*) as count FROM vehicules";
$result = $conn->query($query);

if ($result) {
    $count = $result->fetch_assoc()['count'];
    $dataTest['details'][] = [
        'name' => 'Récupération des véhicules',
        'works' => true,
        'result' => "Nombre de véhicules: $count"
    ];
} else {
    $dataTest['details'][] = [
        'name' => 'Récupération des véhicules',
        'works' => false,
        'result' => "Erreur: " . $conn->error
    ];
    
    $dataTest['passed'] = false;
    $allTestsPassed = false;
}

// Test de récupération des utilisateurs
if (tableExists($conn, 'utilisateurs')) {
    $query = "SELECT COUNT(*) as count FROM utilisateurs";
    $result = $conn->query($query);
    
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        $dataTest['details'][] = [
            'name' => 'Récupération des utilisateurs',
            'works' => true,
            'result' => "Nombre d'utilisateurs: $count"
        ];
    } else {
        $dataTest['details'][] = [
            'name' => 'Récupération des utilisateurs',
            'works' => false,
            'result' => "Erreur: " . $conn->error
        ];
        
        $dataTest['passed'] = false;
        $allTestsPassed = false;
    }
}

$dataTest['message'] = $dataTest['passed'] ? 
    "Accès aux données réussi" : 
    "Problèmes d'accès aux données";

$tests[] = $dataTest;

// Test 5: Vérifier les sessions
$sessionTest = [
    'name' => 'Gestion des sessions',
    'passed' => true,
    'message' => "Les sessions fonctionnent correctement"
];

if (!isset($_SESSION)) {
    $sessionTest['passed'] = false;
    $sessionTest['message'] = "Les sessions ne sont pas initialisées";
    $allTestsPassed = false;
}

$tests[] = $sessionTest;

// Affichage des résultats
?>

<div class="test-connection-container">
    <div class="test-header">
        <h1>Test de connexion et fonctionnalités</h1>
        <p>Cette page vérifie que la connexion à la base de données et les fonctionnalités de base fonctionnent correctement.</p>
    </div>
    
    <div class="test-summary">
        <div class="test-status <?php echo $allTestsPassed ? 'success' : 'error'; ?>">
            <?php if ($allTestsPassed): ?>
                <i class="fas fa-check-circle"></i> Tous les tests ont réussi
            <?php else: ?>
                <i class="fas fa-exclamation-triangle"></i> Certains tests ont échoué
            <?php endif; ?>
        </div>
    </div>
    
    <div class="test-results">
        <?php foreach ($tests as $test): ?>
            <div class="test-card">
                <div class="test-card-header <?php echo $test['passed'] ? 'success' : 'error'; ?>">
                    <h3><?php echo $test['name']; ?></h3>
                    <span class="test-status-badge">
                        <?php if ($test['passed']): ?>
                            <i class="fas fa-check"></i> Réussi
                        <?php else: ?>
                            <i class="fas fa-times"></i> Échoué
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="test-card-body">
                    <p><?php echo $test['message']; ?></p>
                    
                    <?php if (isset($test['details']) && !empty($test['details'])): ?>
                        <div class="test-details">
                            <h4>Détails</h4>
                            <ul>
                                <?php foreach ($test['details'] as $detail): ?>
                                    <li class="<?php echo isset($detail['works']) && $detail['works'] ? 'success' : 'error'; ?>">
                                        <strong><?php echo $detail['name']; ?>:</strong> 
                                        <?php echo isset($detail['result']) ? $detail['result'] : (isset($detail['exists']) ? ($detail['exists'] ? 'Existe' : 'N\'existe pas') : ''); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="test-actions">
        <a href="test-connection.php" class="btn btn-primary">Relancer les tests</a>
        <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
    
    <div class="debug-info">
        <h3>Informations de débogage</h3>
        <div class="debug-card">
            <h4>Configuration de la base de données</h4>
            <ul>
                <li><strong>Hôte:</strong> <?php echo $host; ?></li>
                <li><strong>Base de données:</strong> <?php echo $dbname; ?></li>
                <li><strong>Port:</strong> <?php echo $port; ?></li>
                <li><strong>Utilisateur:</strong> <?php echo $user; ?></li>
            </ul>
        </div>
        
        <div class="debug-card">
            <h4>Informations PHP</h4>
            <ul>
                <li><strong>Version PHP:</strong> <?php echo phpversion(); ?></li>
                <li><strong>Extensions chargées:</strong> <?php echo implode(', ', get_loaded_extensions()); ?></li>
                <li><strong>Session active:</strong> <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Oui' : 'Non'; ?></li>
            </ul>
        </div>
    </div>
</div>

<style>
.test-connection-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.test-header {
    text-align: center;
    margin-bottom: 2rem;
}

.test-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.test-header p {
    color: #666;
}

.test-summary {
    text-align: center;
    margin-bottom: 2rem;
}

.test-status {
    display: inline-block;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1.2rem;
    font-weight: 600;
}

.test-status.success {
    background-color: #d4edda;
    color: #155724;
}

.test-status.error {
    background-color: #f8d7da;
    color: #721c24;
}

.test-status i {
    margin-right: 0.5rem;
}

.test-results {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.test-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.test-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    color: white;
}

.test-card-header.success {
    background-color: #28a745;
}

.test-card-header.error {
    background-color: #dc3545;
}

.test-card-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.test-status-badge {
    background-color: rgba(255, 255, 255, 0.2);
    padding: 0.3rem 0.6rem;
    border-radius: 4px;
    font-size: 0.9rem;
}

.test-card-body {
    padding: 1.5rem;
    background-color: white;
}

.test-details {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.test-details h4 {
    margin-top: 0;
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.test-details ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.test-details li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f5f5f5;
}

.test-details li:last-child {
    border-bottom: none;
}

.test-details li.success {
    color: #28a745;
}

.test-details li.error {
    color: #dc3545;
}

.test-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.debug-info {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
}

.debug-info h3 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #666;
}

.debug-card {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.debug-card h4 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: #495057;
}

.debug-card ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.debug-card li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.debug-card li:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .test-connection-container {
        padding: 1rem;
    }
    
    .test-status {
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
    }
    
    .test-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .test-status-badge {
        margin-top: 0.5rem;
    }
    
    .test-actions {
        flex-direction: column;
    }
    
    .test-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?> 