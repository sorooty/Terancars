<?php
/**
 * Page de test d'API
 * Permet de tester la communication entre le front-end et le back-end
 * via des requêtes AJAX
 */

// Inclusion du fichier de configuration
include '../config/config.php';

// Vérifier si c'est une requête AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Si c'est une requête API
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $response = ['success' => false, 'message' => 'Action non reconnue'];
    
    // Test de connexion à la base de données
    if ($action === 'test_db_connection') {
        $response = [
            'success' => !$conn->connect_error,
            'message' => $conn->connect_error ? 
                "Erreur de connexion: " . $conn->connect_error : 
                "Connexion à la base de données réussie"
        ];
    }
    
    // Test de récupération des véhicules
    elseif ($action === 'get_vehicles') {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        
        if (tableExists($conn, 'vehicules')) {
            $query = "SELECT * FROM vehicules LIMIT ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $limit);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $vehicles = [];
                
                while ($row = $result->fetch_assoc()) {
                    $vehicles[] = $row;
                }
                
                $response = [
                    'success' => true,
                    'message' => count($vehicles) . " véhicules récupérés",
                    'data' => $vehicles
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => "Erreur lors de l'exécution de la requête: " . $conn->error
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => "La table 'vehicules' n'existe pas dans la base de données"
            ];
        }
    }
    
    // Test de session
    elseif ($action === 'test_session') {
        $_SESSION['test_value'] = $_GET['value'] ?? 'test_value';
        
        $response = [
            'success' => true,
            'message' => "Valeur de session définie",
            'session_id' => session_id(),
            'session_value' => $_SESSION['test_value']
        ];
    }
    
    // Vérifier la valeur de session
    elseif ($action === 'check_session') {
        $response = [
            'success' => isset($_SESSION['test_value']),
            'message' => isset($_SESSION['test_value']) ? 
                "Valeur de session trouvée: " . $_SESSION['test_value'] : 
                "Aucune valeur de session trouvée",
            'session_id' => session_id()
        ];
    }
    
    // Si c'est une requête AJAX, renvoyer du JSON
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Si ce n'est pas une requête API, afficher la page de test
$pageTitle = "Test d'API";
include '../includes/header.php';
?>

<div class="api-test-container">
    <div class="api-test-header">
        <h1>Test d'API et communication front-end/back-end</h1>
        <p>Cette page permet de tester la communication entre le front-end et le back-end via des requêtes AJAX.</p>
    </div>
    
    <div class="api-test-cards">
        <div class="api-test-card">
            <div class="api-test-card-header">
                <h3>Test de connexion à la base de données</h3>
            </div>
            <div class="api-test-card-body">
                <p>Vérifie que la connexion à la base de données fonctionne correctement.</p>
                <div class="api-test-result" id="db-connection-result">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                </div>
                <button class="btn btn-primary" id="test-db-connection">Tester la connexion</button>
            </div>
        </div>
        
        <div class="api-test-card">
            <div class="api-test-card-header">
                <h3>Test de récupération des données</h3>
            </div>
            <div class="api-test-card-body">
                <p>Vérifie que la récupération des données depuis la base fonctionne correctement.</p>
                <div class="form-group">
                    <label for="vehicles-limit">Nombre de véhicules à récupérer:</label>
                    <input type="number" id="vehicles-limit" class="form-control" value="3" min="1" max="10">
                </div>
                <div class="api-test-result" id="get-vehicles-result">
                    <div class="spinner-border text-primary d-none" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                </div>
                <button class="btn btn-primary" id="test-get-vehicles">Récupérer les véhicules</button>
            </div>
        </div>
        
        <div class="api-test-card">
            <div class="api-test-card-header">
                <h3>Test de session</h3>
            </div>
            <div class="api-test-card-body">
                <p>Vérifie que la gestion des sessions fonctionne correctement.</p>
                <div class="form-group">
                    <label for="session-value">Valeur à stocker en session:</label>
                    <input type="text" id="session-value" class="form-control" value="test_value">
                </div>
                <div class="api-test-result" id="session-result">
                    <div class="spinner-border text-primary d-none" role="status">
                        <span class="sr-only">Chargement...</span>
                    </div>
                </div>
                <button class="btn btn-primary" id="test-session">Définir la valeur de session</button>
                <button class="btn btn-secondary" id="check-session">Vérifier la valeur de session</button>
            </div>
        </div>
    </div>
    
    <div class="api-test-actions">
        <a href="test-connection.php" class="btn btn-primary">Voir les tests de connexion</a>
        <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
</div>

<script>
// Fonction pour afficher les résultats
function displayResult(elementId, response, isLoading = false) {
    const resultElement = document.getElementById(elementId);
    const spinner = resultElement.querySelector('.spinner-border');
    
    if (isLoading) {
        spinner.classList.remove('d-none');
        resultElement.innerHTML = '';
        resultElement.appendChild(spinner);
        return;
    }
    
    spinner.classList.add('d-none');
    
    let html = '';
    if (response.success) {
        html = `<div class="alert alert-success">${response.message}</div>`;
    } else {
        html = `<div class="alert alert-danger">${response.message}</div>`;
    }
    
    // Afficher les données si disponibles
    if (response.data) {
        html += '<div class="data-container">';
        html += '<h4>Données reçues:</h4>';
        html += '<pre>' + JSON.stringify(response.data, null, 2) + '</pre>';
        html += '</div>';
    }
    
    resultElement.innerHTML = html;
}

// Test de connexion à la base de données
document.getElementById('test-db-connection').addEventListener('click', function() {
    const resultElement = document.getElementById('db-connection-result');
    displayResult('db-connection-result', {}, true);
    
    fetch('api-test.php?action=test_db_connection')
        .then(response => response.json())
        .then(data => {
            displayResult('db-connection-result', data);
        })
        .catch(error => {
            displayResult('db-connection-result', {
                success: false,
                message: 'Erreur lors de la requête: ' + error.message
            });
        });
});

// Test de récupération des véhicules
document.getElementById('test-get-vehicles').addEventListener('click', function() {
    const limit = document.getElementById('vehicles-limit').value;
    displayResult('get-vehicles-result', {}, true);
    
    fetch(`api-test.php?action=get_vehicles&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            displayResult('get-vehicles-result', data);
        })
        .catch(error => {
            displayResult('get-vehicles-result', {
                success: false,
                message: 'Erreur lors de la requête: ' + error.message
            });
        });
});

// Test de session
document.getElementById('test-session').addEventListener('click', function() {
    const sessionValue = document.getElementById('session-value').value;
    displayResult('session-result', {}, true);
    
    fetch(`api-test.php?action=test_session&value=${encodeURIComponent(sessionValue)}`)
        .then(response => response.json())
        .then(data => {
            displayResult('session-result', data);
        })
        .catch(error => {
            displayResult('session-result', {
                success: false,
                message: 'Erreur lors de la requête: ' + error.message
            });
        });
});

// Vérifier la valeur de session
document.getElementById('check-session').addEventListener('click', function() {
    displayResult('session-result', {}, true);
    
    fetch('api-test.php?action=check_session')
        .then(response => response.json())
        .then(data => {
            displayResult('session-result', data);
        })
        .catch(error => {
            displayResult('session-result', {
                success: false,
                message: 'Erreur lors de la requête: ' + error.message
            });
        });
});
</script>

<style>
.api-test-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

.api-test-header {
    text-align: center;
    margin-bottom: 2rem;
}

.api-test-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.api-test-header p {
    color: #666;
}

.api-test-cards {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.api-test-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.api-test-card-header {
    background-color: #007bff;
    color: white;
    padding: 1rem;
}

.api-test-card-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.api-test-card-body {
    padding: 1.5rem;
    background-color: white;
}

.api-test-result {
    margin: 1.5rem 0;
    min-height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.api-test-result .alert {
    width: 100%;
    margin: 0;
}

.data-container {
    margin-top: 1rem;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.data-container h4 {
    margin-top: 0;
    font-size: 1rem;
    color: #495057;
}

.data-container pre {
    margin: 0;
    white-space: pre-wrap;
    font-size: 0.9rem;
}

.api-test-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

@media (min-width: 768px) {
    .api-test-cards {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
}

@media (max-width: 768px) {
    .api-test-container {
        padding: 1rem;
    }
    
    .api-test-actions {
        flex-direction: column;
    }
    
    .api-test-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?> 