<?php
include 'config.php';

$sql = "DELETE FROM clients WHERE id_client=1";

if ($conn->query($sql) === TRUE) {
    echo "Client supprimé !";
} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
