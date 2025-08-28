<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuário não autenticado.']);
    exit;
}

$tape_number = $_GET['tape_number'] ?? '';

if (empty($tape_number)) {
    echo json_encode(['error' => 'Número da fita não fornecido.']);
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Falha na conexão: ' . $conn->connect_error]);
    exit;
}

// A consulta foi ajustada para buscar na tabela 'itens' e juntar com 'submissions'
$sql_last_used = "SELECT s.data_preenchimento 
                  FROM itens i
                  JOIN submissions s ON i.submission_id = s.id
                  WHERE i.status = ? AND i.item_id = 108
                  ORDER BY s.data_preenchimento DESC 
                  LIMIT 1";

$stmt_last_used = $conn->prepare($sql_last_used);
$stmt_last_used->bind_param("s", $tape_number);
$stmt_last_used->execute();
$result_last_used = $stmt_last_used->get_result();
$last_used_data = $result_last_used->fetch_assoc();
$stmt_last_used->close();
$conn->close();

// Formata a data para o padrão brasileiro
$last_used = null;
if ($last_used_data) {
    $last_used = date('d/m/Y', strtotime($last_used_data['data_preenchimento']));
}

echo json_encode([
    'last_used' => $last_used
]);
?>