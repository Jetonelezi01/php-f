<?php
// Informacionet për lidhjen me bazën e të dhënave
$host = 'localhost'; // Ose adresa IP e serverit të bazës së të dhënave
$username = 'root'; // Emri i përdoruesit të bazës së të dhënave
$password = ''; // Fjalëkalimi i përdoruesit të bazës së të dhënave
$dbname = 'frizer'; // Emri i bazës së të dhënave

// Lidhja me MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Kontrollimi i gabimeve në lidhjen me bazën e të dhënave
if ($conn->connect_error) {
    die("Lidhja me bazën e të dhënave ka dështuar: " . $conn->connect_error);
}
?>
