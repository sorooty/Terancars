<?php
include 'dbconfig.php';

$sql = "UPDATE clients SET nom='Alice Diop' WHERE id_client=1";

if ($conn->query($sql) === TRUE) {
    echo "Client mis à jour !";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
