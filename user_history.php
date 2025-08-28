<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("location: login.php");
    exit;
}

require_once 'db_config.php';

$user_id = $_SESSION['user_id'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Lógica de busca por data e paginação
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$limit = $_GET['limit'] ?? 15; // Número de linhas por página, padrão 15
$page = $_GET['page'] ?? 1;   // Página atual, padrão 1
$offset = ($page - 1) * $limit; // Cálculo do offset

$where_clauses = ["s.user_id = ?"];
$params = [$user_id];
$types = "i";

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

// Consulta para obter o total de registros (necessário para a paginação)
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

// Consulta para buscar os registros da página atual
$sql = "SELECT s.id, s.filial, s.data_preenchimento, s.checklist_type, u.username
        FROM submissions s
        JOIN users u ON s.user_id = u.id";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY s.data_preenchimento DESC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}

// Constrói a URL para manter os filtros na paginação
$base_url = "user_history.php?start_date=$start_date&end_date=$end_date&limit=$limit";

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Histórico de Checklists</title>
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
        .search-form { 
            display: flex; 
            flex-wrap: wrap;
            gap: 10px; 
            align-items: flex-end; 
            margin-bottom: 20px; 
        }
        .search-form label { 
            font-weight: 600; 
            color: #555;
        }
        .search-form input[type="date"] { 
            padding: 8px; 
            border: 1px solid #ccc; 
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
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
        .back-btn, .download-btn, .btn {
            display: inline-block;
            text-decoration: none;
            color: #007BFF;
            font-weight: 600;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        .download-btn, .btn {
            color: #fff;
            background-color: #4CAF50;
            border: none;
        }
        .download-btn:hover, .btn:hover {
            background-color: #43a047;
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
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Meu Histórico de Checklists</h1>
            <a href="checklist.php" class="back-btn">Voltar</a>
        </div>
        
        <div class="search-form-container">
            <form action="" method="GET" class="search-form">
                <div>
                    <label for="start_date">Data de Início:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div>
                    <label for="end_date">Data de Fim:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <button type="submit" class="btn">Buscar</button>
            </form>
            <div style="flex-grow: 1;"></div>
            <form action="" method="GET" class="search-form" style="align-items: center;">
                <label for="limit">Linhas / página</label>
                <select name="limit" onchange="this.form.submit()">
                    <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="15" <?php echo ($limit == 15) ? 'selected' : ''; ?>>15</option>
                    <option value="25" <?php echo ($limit == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>50</option>
                </select>
                <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            </form>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Filial</th>
                        <th>Tipo de Checklist</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($row['data_preenchimento'])); ?></td>
                        <td><?php echo htmlspecialchars(str_replace('_', ' ', $row['filial'])); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['checklist_type'])); ?></td>
                        <td>
                            <a href="generate_pdf.php?submission_id=<?php echo $row['id']; ?>" class="download-btn">Baixar PDF</a>
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
            <p class="no-data">Nenhum checklist encontrado<?php echo (!empty($start_date) || !empty($end_date)) ? " para o período selecionado." : " no seu histórico."; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
$conn->close();
?>