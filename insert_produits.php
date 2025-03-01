<?php
include 'dbconfig.php';

$sql = "INSERT INTO produits (nom_produit, description, prix, stock, categorie, disponible_location, tarif_location_journalier) 
        VALUES ('Toyota Corolla', 'Voiture en excellent état', 10000.00, 5, 'Voitures', 1, 200.00)";

if ($conn->query($sql) === TRUE) {
    echo "Produit ajouté avec succès !";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
