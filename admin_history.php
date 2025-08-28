<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1){
    header("location: checklist.php");
    exit;
}

require_once 'db_config.php';

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Lógica para excluir o histórico de checklist
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_submission'])) {
    $submission_id = $_POST['submission_id'];
    
    // Inicia a transação para garantir que as duas exclusões sejam atômicas
    $conn->begin_transaction();

    try {
        // 1. Excluir os itens do checklist (porque eles referenciam a submissão)
        $sql_delete_itens = "DELETE FROM itens WHERE submission_id = ?";
        $stmt_itens = $conn->prepare($sql_delete_itens);
        $stmt_itens->bind_param("i", $submission_id);
        $stmt_itens->execute();
        $stmt_itens->close();
        
        // 2. Excluir a submissão principal
        $sql_delete_submission = "DELETE FROM submissions WHERE id = ?";
        $stmt_submission = $conn->prepare($sql_delete_submission);
        $stmt_submission->bind_param("i", $submission_id);
        $stmt_submission->execute();
        $stmt_submission->close();
        
        $conn->commit();
        $message = "Histórico de checklist excluído com sucesso!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Erro ao excluir o histórico: " . $e->getMessage();
    }
    
    $conn->close();
    header("Location: admin_history.php?message=" . urlencode($message) . "&error=" . urlencode($error));
    exit;
}

// Lógica de busca por data, filial, tipo de checklist e paginação
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$filial_search = $_GET['filial'] ?? '';
$checklist_type_search = $_GET['checklist_type'] ?? '';
$limit = $_GET['limit'] ?? 15;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$filiais = [
    'PLP_BRASIL',
    'MAXXWELD',
    'JAP_TELECOM',
    'PLP_ARGENTINA',
    'PLP_COLOMBIA',
    'GLOBAL'
];

$checklist_types = [
    'infra',
    'backup',
    'helpdesk',
    'monitoramento',
    'checklist',
    'tape_inventory'
];

$where_clauses = [];
$params = [];
$types = "";

if (!empty($start_date)) {
    $where_clauses[] = "s.data_preenchimento >= ?";
    $params[] = $start_date . " 00:00:00";
    $types .= "s";
}

if (!empty($end_date)) {
    $where_clauses[] = "s.data_preenchimento <= ?";
    $params[] = $end_date . " 23:59:59";
    $types .= "s";
}

if (!empty($filial_search) && $filial_search !== 'all') {
    $where_clauses[] = "s.filial = ?";
    $params[] = $filial_search;
    $types .= "s";
}

if (!empty($checklist_type_search) && $checklist_type_search !== 'all') {
    $where_clauses[] = "s.checklist_type = ?";
    $params[] = $checklist_type_search;
    $types .= "s";
}

$sql_count = "SELECT COUNT(*) AS total_records FROM submissions s";
if (count($where_clauses) > 0) {
    $sql_count .= " WHERE " . implode(" AND ", $where_clauses);
}

$stmt_count = $conn->prepare($sql_count);
if (count($params) > 0) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_records = $result_count->fetch_assoc()['total_records'];
$total_pages = ceil($total_records / $limit);
$stmt_count->close();

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$sql = "SELECT s.id, s.filial, s.data_preenchimento, s.checklist_type, u.username
        FROM submissions s
        JOIN users u ON s.user_id = u.id";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY s.data_preenchimento DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}

$base_url = "admin_history.php?start_date=$start_date&end_date=$end_date&filial=$filial_search&checklist_type=$checklist_type_search&limit=$limit";

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Checklists (Admin)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', Arial, sans-serif; 
            background-color: #f0f2f5; 
            padding: 20px; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background-color: #fff; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .header { 
            text-align: center; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 20px; 
            margin-bottom: 20px; 
        }
        h1 { 
            color: #003366; 
            font-weight: 600; 
            font-size: 2em;
        }
        .search-form-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 20px;
        }
        .search-form-row {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 10px;
        }
        .search-form-row > div {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .search-form label { 
            font-weight: 600; 
            color: #555;
            font-size: 0.9em;
        }
        .search-form input[type="date"], .search-form select { 
            padding: 8px; 
            border: 1px solid #ccc; 
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
        }
        .btn {
            color: #fff;
            background-color: #4CAF50;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background-color: #43a047;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .pagination-controls {
            display: flex;
            gap: 5px;
        }
        .pagination-controls a, .pagination-controls span {
            padding: 8px 12px;
            text-decoration: none;
            color: #007BFF;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .pagination-controls a:hover {
            background-color: #f0f2f5;
        }
        .pagination-controls span.current {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }
        .back-btn, .download-btn, .delete-btn {
            display: inline-block;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            box-sizing: border-box;
            width: 100%;
            text-align: center;
        }
        .download-btn {
            color: #fff;
            background-color: #4CAF50;
            border: none;
        }
        .download-btn:hover {
            background-color: #43a047;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #007BFF;
            color: white;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody tr:hover {
            background-color: #e9e9e9;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #555;
            font-weight: 400;
        }
        .action-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .action-cell a, .action-cell form {
            flex-grow: 1;
        }
        .message, .error {
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 400;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* CORREÇÃO AQUI: Estilo para o botão Voltar */
        .header-top-buttons {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .back-btn-small {
            text-decoration: none;
            color: #007BFF;
            font-weight: 600;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .back-btn-small:hover {
            background-color: #e2e6ea;
            color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header-top-buttons">
            <a href="admin.php" class="back-btn-small">Voltar para o Menu Admin</a>
        </div>
        
        <div class="header">
            <h1>Histórico de Checklists</h1>
        </div>
        
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        
        <div class="search-form-container">
            <form action="" method="GET" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px;">
                <div>
                    <label for="start_date">Data de Início:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div>
                    <label for="end_date">Data de Fim:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <div>
                    <label for="filial">Filial:</label>
                    <select name="filial" id="filial">
                        <option value="all">Todas</option>
                        <?php foreach ($filiais as $filial_option): ?>
                            <option value="<?php echo htmlspecialchars($filial_option); ?>" <?php echo ($filial_search === $filial_option) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(str_replace('_', ' ', $filial_option)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="checklist_type">Tipo de Checklist:</label>
                    <select name="checklist_type" id="checklist_type">
                        <option value="all">Todos</option>
                        <?php foreach ($checklist_types as $type_option): ?>
                            <option value="<?php echo htmlspecialchars($type_option); ?>" <?php echo ($checklist_type_search === $type_option) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $type_option))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Buscar</button>
            </form>
            <form action="" method="GET" style="display: flex; align-items: center; gap: 10px;">
                <div>
                    <label for="limit">Linhas / página</label>
                    <select name="limit" onchange="this.form.submit()">
                        <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="15" <?php echo ($limit == 15) ? 'selected' : ''; ?>>15</option>
                        <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                    </select>
                </div>
                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                <input type="hidden" name="filial" value="<?php echo htmlspecialchars($filial_search); ?>">
                <input type="hidden" name="checklist_type" value="<?php echo htmlspecialchars($checklist_type_search); ?>">
                <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
            </form>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Filial</th>
                        <th>Tipo de Checklist</th>
                        <th>Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($row['data_preenchimento'])); ?></td>
                        <td><?php echo htmlspecialchars(str_replace('_', ' ', $row['filial'])); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $row['checklist_type']))); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="action-cell">
                            <a href="generate_pdf.php?submission_id=<?php echo $row['id']; ?>" class="download-btn">Baixar PDF</a>
                            <form action="" method="post" onsubmit="return confirm('Tem certeza que deseja excluir este histórico?');" style="display:inline-block;">
                                <input type="hidden" name="delete_submission" value="1">
                                <input type="hidden" name="submission_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="delete-btn">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="pagination-container">
                <div>Página <?php echo $page; ?> de <?php echo $total_pages; ?></div>
                <div class="pagination-controls">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo htmlspecialchars($base_url . '&page=' . ($page - 1)); ?>">Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($base_url . '&page=' . $i); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?php echo htmlspecialchars($base_url . '&page=' . ($page + 1)); ?>">Próximo</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="no-data">Nenhum checklist encontrado<?php echo (!empty($start_date) || !empty($end_date) || !empty($filial_search) || !empty($checklist_type_search)) ? " para o período/filial/tipo selecionado(s)." : "."; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
$conn->close();
?>