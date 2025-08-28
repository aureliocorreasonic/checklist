<?php
session_start();
require_once 'db_config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1){
    header("location: checklist.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
    
    // Lógica para dar permissão de admin
    if (isset($_POST['set_admin_id'])) {
        $user_id = $_POST['set_admin_id'];
        $is_admin = (isset($_POST['is_admin']) && $_POST['is_admin'] == 'on') ? 1 : 0;
        $sql = "UPDATE users SET is_admin = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $is_admin, $user_id);
        if ($stmt->execute()) {
            $message = "Permissão de administrador atualizada.";
        } else {
            $error = "Erro ao atualizar permissão.";
        }
        $stmt->close();
    
    // Lógica para resetar senha
    } elseif (isset($_POST['reset_password_id'])) {
        $user_id = $_POST['reset_password_id'];
        $new_password = $_POST['new_password'];
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $password_hash, $user_id);
        if ($stmt->execute()) {
            $message = "Senha resetada com sucesso!";
        } else {
            $error = "Erro ao resetar a senha.";
        }
        $stmt->close();

    // Lógica para excluir usuário
    } elseif (isset($_POST['delete_user_id'])) {
        $user_id = $_POST['delete_user_id'];
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "Usuário excluído com sucesso!";
        } else {
            $error = "Erro ao excluir usuário.";
        }
        $stmt->close();
    }

    $conn->close();
    header("Location: manage_users.php");
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$users = [];
$sql = "SELECT id, username, is_admin FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
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
        h1 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #eee;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #007BFF;
            color: white;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tbody tr:hover {
            background-color: #e9e9e9;
        }
        .btn, .back-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn {
            color: #fff;
            background-color: #007BFF;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .reset-btn {
            background-color: #ff9800;
        }
        .reset-btn:hover {
            background-color: #e68a00;
        }
        .delete-btn {
            background-color: #f44336;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .checkbox {
            vertical-align: middle;
            transform: scale(1.2);
            margin-right: 5px;
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
        }
        .back-btn:hover {
            background-color: #e2e6ea;
            color: #0056b3;
        }
        input[type="password"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-family: inherit;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Usuários</h1>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <table>
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Admin</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td>
                        <form action="" method="post" style="margin:0;">
                            <input type="hidden" name="set_admin_id" value="<?php echo $user['id']; ?>">
                            <input type="checkbox" name="is_admin" class="checkbox" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>
                            <button type="submit" class="btn">Salvar</button>
                        </form>
                    </td>
                    <td>
                        <form action="" method="post" style="display:inline-block;">
                            <input type="hidden" name="reset_password_id" value="<?php echo $user['id']; ?>">
                            <input type="password" name="new_password" placeholder="Nova senha" required>
                            <button type="submit" class="btn reset-btn">Resetar Senha</button>
                        </form>
                        <form action="" method="post" style="display:inline-block;">
                            <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn delete-btn" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="admin.php" class="back-btn">Voltar para o Menu Admin</a>
    </div>
</body>
</html>