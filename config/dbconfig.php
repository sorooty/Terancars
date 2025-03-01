<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "terancar";
$port = 3307; // Change si tu as modifié le port de MySQL

$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
 echo "Connexion MySQL réussie !";
?>
