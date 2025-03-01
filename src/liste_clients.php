<?php
include 'dbconfig.php';

$result = $conn->query("SELECT * FROM clients LIMIT 20");

echo "<h2>Liste des Clients (Max 20)</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Adresse</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["id_client"] . "</td>";
    echo "<td>" . $row["nom"] . "</td>";
    echo "<td>" . $row["email"] . "</td>";
    echo "<td>" . $row["telephone"] . "</td>";
    echo "<td>" . $row["adresse"] . "</td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();
?>
