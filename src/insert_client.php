<?php
include 'dbconfig.php';

// Tableau de clients à insérer
$clients = [
    ['Jean Dupont', 'jean.dupont@example.com', '770000001', 'Dakar, Sénégal'],
    ['Alice Diop', 'alice.diop@example.com', '770000002', 'Thiès, Sénégal'],
    ['Mamadou Faye', 'mamadou.faye@example.com', '770000003', 'Saint-Louis, Sénégal'],
    ['Aissatou Ndiaye', 'aissatou.ndiaye@example.com', '770000004', 'Ziguinchor, Sénégal'],
    ['Omar Sow', 'omar.sow@example.com', '770000005', 'Kaolack, Sénégal'],
    ['Fatou Bâ', 'fatou.ba@example.com', '770000006', 'Kolda, Sénégal'],
    ['Boubacar Camara', 'boubacar.camara@example.com', '770000007', 'Louga, Sénégal'],
    ['Khadija Gueye', 'khadija.gueye@example.com', '770000008', 'Tambacounda, Sénégal'],
    ['Serigne Fall', 'serigne.fall@example.com', '770000009', 'Podor, Sénégal'],
    ['Moussa Kane', 'moussa.kane@example.com', '770000010', 'Matam, Sénégal'],
    ['Astou Mbaye', 'astou.mbaye@example.com', '770000011', 'Fatick, Sénégal'],
    ['Abdoulaye Gaye', 'abdoulaye.gaye@example.com', '770000012', 'Kaffrine, Sénégal'],
    ['Ndeye Diouf', 'ndeye.diouf@example.com', '770000013', 'Bakel, Sénégal'],
    ['Ibrahima Thiam', 'ibrahima.thiam@example.com', '770000014', 'Kédougou, Sénégal'],
    ['Sokhna Seck', 'sokhna.seck@example.com', '770000015', 'Diourbel, Sénégal'],
    ['Cheikh Sarr', 'cheikh.sarr@example.com', '770000016', 'Mbour, Sénégal'],
    ['Awa Cissé', 'awa.cisse@example.com', '770000017', 'Pikine, Sénégal'],
    ['Babacar Ndiaye', 'babacar.ndiaye@example.com', '770000018', 'Rufisque, Sénégal'],
    ['Salif Diallo', 'salif.diallo@example.com', '770000019', 'Tivaouane, Sénégal'],
    ['Mame Fatou Ka', 'mame.fatou.ka@example.com', '770000020', 'Touba, Sénégal']
];

// Insertion des 20 clients
foreach ($clients as $client) {
    $sql = "INSERT INTO clients (nom, email, telephone, adresse) 
            VALUES ('$client[0]', '$client[1]', '$client[2]', '$client[3]')";

    if ($conn->query($sql) !== TRUE) {
        echo "Erreur lors de l'ajout de $client[0] : " . $conn->error . "<br>";
    }
}

echo "20 clients ajoutés avec succès !";

$conn->close();
?>

