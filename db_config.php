<?php
$servername = "db";
$username = "root";
$password = "Plp@2020";
$dbname = "checklist_db";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
