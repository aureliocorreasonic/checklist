<?php
session_start();
// Verifica se o usuário está logado e se a permissão de admin é 1
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1){
    header("location: checklist.php");
    exit;
}

$filial = isset($_GET['filial']) ? htmlspecialchars($_GET['filial']) : '';
$filial_display = str_replace('_', ' ', $filial);

if (empty($filial)) {
    // Se não houver filial na URL, redireciona de volta
    header("location: checklist.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $filial_display; ?></title>
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
        .admin-menu a, .back-btn {
            display: block;
            margin-bottom: 15px;
            padding: 20px;
            background-color: #f5f5f5;
            color: #003366;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
        }
        .admin-menu a:hover, .back-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            background-color: #e0e0e0;
        }
        .back-btn {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Itens de <?php echo $filial_display; ?></h1>
        <div class="admin-menu">
            <?php if ($filial === 'GLOBAL'): ?>
                <a href="manage_items.php?type=infra&filial=<?php echo $filial; ?>">Gerenciar Itens (Infra - Global)</a>
                <a href="manage_items.php?type=backup&filial=<?php echo $filial; ?>">Gerenciar Itens (Backup - Global)</a>
                <a href="manage_items.php?type=helpdesk&filial=<?php echo $filial; ?>">Gerenciar Itens (Helpdesk - Global)</a>
                <a href="manage_items.php?type=monitoramento&filial=<?php echo $filial; ?>">Gerenciar Itens (Monitoramento - Global)</a>
                <a href="manage_items.php?type=checklist&filial=<?php echo $filial; ?>">Gerenciar Itens (Geral - Global)</a>
            <?php else: ?>
                <a href="manage_items.php?type=infra&filial=<?php echo $filial; ?>">Gerenciar Itens (Infra)</a>
                <a href="manage_items.php?type=backup&filial=<?php echo $filial; ?>">Gerenciar Itens (Backup)</a>
                <a href="manage_items.php?type=helpdesk&filial=<?php echo $filial; ?>">Gerenciar Itens (Helpdesk)</a>
                <a href="manage_items.php?type=monitoramento&filial=<?php echo $filial; ?>">Gerenciar Itens (Monitoramento)</a>
                <a href="manage_items.php?type=checklist&filial=<?php echo $filial; ?>">Gerenciar Itens (Geral)</a>
            <?php endif; ?>
        </div>
        <a href="admin.php" class="back-btn">Voltar para o Menu Admin</a>
    </div>
</body>
</html>