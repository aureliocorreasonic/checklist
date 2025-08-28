<?php
session_start();
// Verifica se o usuário está logado e se a permissão de admin é 1
if(!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1){
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
    <title>Menu Admin</title>
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
            max-width: 1000px; 
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
        h2 {
            font-size: 1.4em;
            font-weight: 400;
            color: #555;
            margin-bottom: 20px;
        }
        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 20px 0;
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
        <h1>Menu do Administrador</h1>
        <div class="admin-menu">
            <a href="create_user.php">Criar Novo Usuário</a>
            <a href="manage_users.php">Gerenciar Usuários</a>
            <a href="admin_history.php">Histórico de Checklists</a>
        </div>
        <hr style="margin: 20px 0;">
        <h2>Selecione a Filial para Gerenciar Itens:</h2>
        <div class="admin-menu">
            <a href="admin_filial_menu.php?filial=GLOBAL">GLOBAL</a>
            <?php
            $filiais = [
                'PLP_BRASIL',
                'MAXXWELD',
                'JAP_TELECOM',
                'PLP_ARGENTINA',
                'PLP_COLOMBIA'
            ];
            
            foreach ($filiais as $filial_name):
                $link_href = "admin_filial_menu.php?filial=$filial_name";
            ?>
                <a href="<?php echo htmlspecialchars($link_href); ?>"><?php echo str_replace('_', ' ', $filial_name); ?></a>
            <?php endforeach; ?>
        </div>
        <a href="checklist.php" class="back-btn">Voltar para o Menu Principal</a>
    </div>
</body>
</html>