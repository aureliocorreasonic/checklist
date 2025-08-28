<?php
session_start();
require_once 'db_config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1){
    header("location: checklist.php");
    exit;
}

$checklist_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'infra';
$type_display = ucfirst($checklist_type);
$filial = isset($_GET['filial']) ? htmlspecialchars($_GET['filial']) : '';
$filial_display = str_replace('_', ' ', $filial);

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    $redirect_url = "manage_items.php?type=$checklist_type&filial=$filial";

    if (isset($_POST['add_item'])) {
        $item_name = $_POST['item_name'];
        if (!empty($item_name)) {
            $sql = "INSERT INTO checklist_item_names (item_name, checklist_type, filial) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $redirect_url .= "&error=" . urlencode("Erro ao preparar a consulta de inserção: " . $conn->error);
            } else {
                $stmt->bind_param("sss", $item_name, $checklist_type, $filial);
                if ($stmt->execute()) {
                    $redirect_url .= "&message=" . urlencode("Item '$item_name' adicionado com sucesso!");
                } else {
                    $redirect_url .= "&error=" . urlencode("Erro ao adicionar item: " . $stmt->error);
                }
                $stmt->close();
            }
        } else {
            $redirect_url .= "&error=" . urlencode("O nome do item não pode ser vazio.");
        }
    } elseif (isset($_POST['edit_item'])) {
        $item_id = $_POST['edit_item_id'];
        $new_item_name = $_POST['new_item_name'];
        if (!empty($new_item_name)) {
            $sql = "UPDATE checklist_item_names SET item_name = ? WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $redirect_url .= "&error=" . urlencode("Erro ao preparar a consulta de atualização: " . $conn->error);
            } else {
                $stmt->bind_param("si", $new_item_name, $item_id);
                if ($stmt->execute()) {
                    $redirect_url .= "&message=" . urlencode("Item atualizado com sucesso!");
                } else {
                    $redirect_url .= "&error=" . urlencode("Erro ao atualizar item: " . $stmt->error);
                }
                $stmt->close();
            }
        } else {
            $redirect_url .= "&error=" . urlencode("O nome do item não pode ser vazio.");
        }
    } elseif (isset($_POST['delete_item'])) {
        $item_id = $_POST['delete_item_id'];
        $sql = "DELETE FROM checklist_item_names WHERE item_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $redirect_url .= "&error=" . urlencode("Erro ao preparar a consulta de exclusão: " . $conn->error);
        } else {
            $stmt->bind_param("i", $item_id);
            if ($stmt->execute()) {
                $redirect_url .= "&message=" . urlencode("Item excluído com sucesso!");
            } else {
                $redirect_url .= "&error=" . urlencode("Erro ao excluir item: " . $stmt->error);
            }
            $stmt->close();
        }
    }

    $conn->close();
    header("Location: $redirect_url");
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$items = [];
// A query de seleção agora filtra por filial E por tipo
$sql = "SELECT item_id, item_name FROM checklist_item_names WHERE checklist_type = ? AND filial = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Erro ao preparar a consulta de seleção: " . $conn->error);
}
$stmt->bind_param("ss", $checklist_type, $filial);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Itens - <?php echo $type_display; ?> - <?php echo $filial_display; ?></title>
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
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1, h3 {
            color: #003366;
            font-weight: 600;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        h3 {
            font-size: 1.2em;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .form-section {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .item-list {
            margin-top: 20px;
        }
        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .item-row form {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
        .btn, .add-btn, .edit-btn, .delete-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
        }
        .add-btn {
            background-color: #4CAF50;
        }
        .add-btn:hover {
            background-color: #45a049;
        }
        .edit-btn {
            background-color: #ff9800;
        }
        .edit-btn:hover {
            background-color: #e68a00;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .input-text {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 250px;
            margin-right: 10px;
            font-family: inherit;
        }
        .message {
            color: green;
            margin-bottom: 10px;
            font-weight: 400;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: 400;
        }
        .back-btn {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            color: #007BFF;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background-color: #e2e6ea;
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Itens - <?php echo $type_display; ?> - <?php echo $filial_display; ?></h1>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        
        <div class="form-section">
            <h3>Adicionar Novo Item</h3>
            <form action="" method="post" class="add-form">
                <input type="text" name="item_name" placeholder="Nome do novo item" class="input-text" required>
                <button type="submit" name="add_item" class="btn add-btn">Adicionar</button>
            </form>
        </div>
        
        <div class="item-list">
            <h3>Itens Existentes</h3>
            <?php if (empty($items)): ?>
                <p>Nenhum item encontrado para esta filial.</p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="item-row">
                        <form action="" method="post">
                            <input type="hidden" name="edit_item_id" value="<?php echo $item['item_id']; ?>">
                            <input type="text" name="new_item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" class="input-text">
                            <button type="submit" name="edit_item" class="btn edit-btn">Salvar</button>
                        </form>
                        <form action="" method="post">
                            <input type="hidden" name="delete_item_id" value="<?php echo $item['item_id']; ?>">
                            <button type="submit" name="delete_item" class="btn delete-btn" onclick="return confirm('Tem certeza que deseja excluir este item?');">Excluir</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <a href="admin.php" class="back-btn">Voltar para o Menu Admin</a>
    </div>
</body>
</html>