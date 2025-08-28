<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
require_once 'db_config.php';

// Apenas o usuário 'admin' pode acessar esta página
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1){
    header("location: checklist.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Garante que os campos não estão vazios
    if (empty($username) || empty($password)) {
        $error = "Usuário e senha são obrigatórios.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password_hash, is_admin) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $default_is_admin = 0;
        $stmt->bind_param("ssi", $username, $password_hash, $default_is_admin);

        if ($stmt->execute()) {
            $message = "Usuário criado com sucesso!";
        } else {
            $error = "Erro ao criar o usuário: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Usuário</title>
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
            max-width: 500px; 
            margin: 0 auto; 
            background-color: #fff; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            text-align: center; 
        }
        h1 { 
            color: #003366; 
            margin-bottom: 20px;
            font-size: 2em;
            font-weight: 600;
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 15px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-family: inherit;
        }
        button { 
            width: 100%; 
            padding: 15px; 
            background-color: #007BFF; 
            color: #fff; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: 600;
            cursor: pointer; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .success { 
            color: green; 
            margin-bottom: 10px; 
        }
        .error { 
            color: red; 
            margin-bottom: 10px; 
        }
        .back-btn { 
            display: block; 
            margin-top: 30px; 
            text-decoration: none; 
            color: #007BFF; 
            font-weight: 600;
            padding: 10px 15px; 
            border: 1px solid #ddd; 
            border-radius: 8px;
            background-color: #f8f9fa;
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
        <h1>Criar Novo Usuário</h1>
        <?php if (!empty($message)) { echo "<p class='success'>$message</p>"; } ?>
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Nome de usuário" required>
            <input type="password" name="password" placeholder="Senha" required>
            <button type="submit">Criar Usuário</button>
        </form>
        <a href="admin.php" class="back-btn">Voltar para o Menu Admin</a>
    </div>
</body>
</html>