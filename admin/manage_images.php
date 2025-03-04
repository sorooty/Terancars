<?php
/**
 * Interface d'administration des images
 * Permet de gérer les images des véhicules
 */

// Définition du titre de la page
$pageTitle = "Gestion des images";

// Inclusion des fichiers nécessaires
require_once '../config/config.php';
require_once '../includes/header.php';
require_once '../includes/image_functions.php';

// Vérifier si l'utilisateur est admin
if (!isAdmin()) {
    header('Location: /TeranCar/public/index.php');
    exit;
}

// Traitement de l'upload d'image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $vehicleId = intval($_POST['vehicle_id']);
    
    if (isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $successCount = 0;
        
        // Traiter chaque image
        for ($i = 0; $i < count($files['name']); $i++) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            if ($file['error'] === 0) {
                if (saveVehicleImage($vehicleId, $file)) {
                    $successCount++;
                }
            }
        }
        
        if ($successCount > 0) {
            setAlert("$successCount image(s) ajoutée(s) avec succès.", 'success');
        } else {
            setAlert("Erreur lors de l'upload des images.", 'danger');
        }
    }
}

// Traitement de la suppression d'image
if (isset($_GET['delete'])) {
    $vehicleId = intval($_GET['vehicle_id']);
    $imageName = $_GET['delete'];
    
    if (deleteVehicleImage($vehicleId, $imageName)) {
        setAlert("Image supprimée avec succès.", 'success');
    } else {
        setAlert("Erreur lors de la suppression de l'image.", 'danger');
    }
}

// Récupérer tous les véhicules
$query = "SELECT id_vehicule, marque, modele FROM vehicules ORDER BY marque, modele";
$vehicles = $conn->query($query);
?>

<div class="admin-container">
    <h1>Gestion des Images</h1>
    
    <?php if (isset($_GET['vehicle_id'])) { 
        $selectedVehicleId = intval($_GET['vehicle_id']);
        $vehicleQuery = "SELECT marque, modele FROM vehicules WHERE id_vehicule = ?";
        $stmt = $conn->prepare($vehicleQuery);
        $stmt->bind_param("i", $selectedVehicleId);
        $stmt->execute();
        $vehicleResult = $stmt->get_result();
        $vehicle = $vehicleResult->fetch_assoc();
    ?>
        <h2>Images pour <?php echo $vehicle['marque'] . ' ' . $vehicle['modele']; ?></h2>
        
        <!-- Formulaire d'upload -->
        <div class="upload-form">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="vehicle_id" value="<?php echo $selectedVehicleId; ?>">
                <div class="form-group">
                    <label for="images">Ajouter des images</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" required>
                </div>
                <button type="submit" name="upload" class="btn btn-primary">Uploader</button>
            </form>
        </div>
        
        <!-- Galerie d'images -->
        <div class="image-gallery">
            <?php
            $images = getVehicleImages($selectedVehicleId);
            foreach ($images as $image) {
                if (strpos($image, 'no-image.jpg') === false) {
                    $imageName = basename($image);
                    ?>
                    <div class="image-item">
                        <img src="<?php echo $image; ?>" alt="Image véhicule">
                        <div class="image-actions">
                            <a href="?vehicle_id=<?php echo $selectedVehicleId; ?>&delete=<?php echo $imageName; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette image ?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        
        <a href="manage_images.php" class="btn btn-secondary mt-3">Retour à la liste</a>
        
    <?php } else { ?>
        <!-- Liste des véhicules -->
        <div class="vehicle-list">
            <?php while ($vehicle = $vehicles->fetch_assoc()) { 
                $imageCount = count(array_filter(getVehicleImages($vehicle['id_vehicule']), function($img) {
                    return strpos($img, 'no-image.jpg') === false;
                }));
            ?>
                <div class="vehicle-item">
                    <div class="vehicle-info">
                        <h3><?php echo $vehicle['marque'] . ' ' . $vehicle['modele']; ?></h3>
                        <span class="image-count"><?php echo $imageCount; ?> image(s)</span>
                    </div>
                    <a href="?vehicle_id=<?php echo $vehicle['id_vehicule']; ?>" class="btn btn-primary">
                        Gérer les images
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<style>
.admin-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.upload-form {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.image-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.image-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-actions {
    position: absolute;
    bottom: 0;
    right: 0;
    padding: 0.5rem;
    background: rgba(0,0,0,0.5);
}

.vehicle-list {
    display: grid;
    gap: 1rem;
}

.vehicle-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.vehicle-info {
    flex: 1;
}

.vehicle-info h3 {
    margin: 0;
    font-size: 1.1rem;
}

.image-count {
    color: #6c757d;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .image-gallery {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .vehicle-item {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?> 