<?php
session_start();
require_once 'db_config.php';

if(!isset($_SESSION['user_id'])){
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_admin = 0;

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$sql = "SELECT is_admin FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $is_admin = (int) $user['is_admin'];
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
    <title>Menu Filiais - Checklist Datacenter</title>
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
            position: relative;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 30px;
            position: relative;
        }

        .header-left {
            position: absolute;
            left: 20px;
        }

        .header-title-img {
            max-width: 300px;
            height: auto;
        }

        .logo {
            max-height: 70px;
        }

        .header-right-buttons {
            position: absolute;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .logout-btn, .admin-link, .history-link {
            text-decoration: none;
            color: #007BFF;
            font-weight: 600;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .logout-btn:hover, .admin-link:hover, .history-link:hover {
            background-color: #e2e6ea;
            color: #0056b3;
        }
        
        .menu-title {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 40px;
        }
        
        .menu-title h2 {
            font-size: 1.8em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .menu-grid {
            /* CORREÇÃO AQUI: Garante que as caixas tenham tamanho uniforme */
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }

        .menu-item {
            /* CORREÇÃO AQUI: Estilo das caixas igual ao do submenu */
            background-color: #f5f5f5;
            border: none;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            font-size: 1.1em;
            font-weight: 600;
            color: #003366;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            cursor: pointer;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <div class="header-left">
                <img src="https://plp.com.br/wp-content/themes/jupiter/images/logo-plp-novo.png" alt="Logo PLP" class="logo">
            </div>
            <img src="Logo/logo_name.png" alt="CHECKLIST T.I" class="header-title-img">
            <div class="header-right-buttons">
                <a href="user_history.php" class="history-link">Histórico</a>
                <?php if ($is_admin === 1): ?>
                    <a href="admin.php" class="admin-link">Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>
        </div>
        
        <div class="menu-title">
            <h2>Selecione a Filial:</h2>
        </div>

        <div class="menu-grid">
            <?php if ($is_admin === 1): ?>
                <a href="submenu.php?filial=GLOBAL" class="menu-item">GLOBAL</a>
            <?php endif; ?>

            <?php
            $filiais = [
                'PLP_BRASIL',
                'MAXXWELD',
                'JAP_TELECOM',
                'PLP_ARGENTINA',
                'PLP_COLOMBIA'
            ];
            
            foreach ($filiais as $filial_name):
                $link_href = "submenu.php?filial=$filial_name";
            ?>
                <a href="<?php echo htmlspecialchars($link_href); ?>" class="menu-item"><?php echo str_replace('_', ' ', $filial_name); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>