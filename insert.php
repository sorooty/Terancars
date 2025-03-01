<?php
include 'dbconfig.php';

$sql = "INSERT INTO utilisateurs (nom, email, password) VALUES ('Alice', 'alice@example.com', 'motdepasse')";
if ($conn->query($sql) === TRUE) {
    echo "Nouvel utilisateur ajouté avec succès !";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
