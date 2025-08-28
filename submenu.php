<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("location: login.php");
    exit;
}

$filial = isset($_GET['filial']) ? htmlspecialchars($_GET['filial']) : '';
$filial_display = str_replace('_', ' ', $filial);

if (empty($filial)) {
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
    <title>Checklist de Filial - <?php echo $filial_display; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background-color: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background-color: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; }
        .header { display: flex; justify-content: center; align-items: center; padding: 20px; border-bottom: 1px solid #eee; margin-bottom: 30px; position: relative; }
        .header-left { position: absolute; left: 20px; }
        .header-title-img { max-width: 300px; height: auto; }
        .logo { max-height: 70px; }
        .logout-btn { text-decoration: none; color: #007BFF; font-weight: 600; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa; position: absolute; right: 20px; transition: all 0.3s ease; }
        .logout-btn:hover { background-color: #e2e6ea; color: #0056b3; }
        .menu-title { text-align: center; margin-top: 30px; margin-bottom: 40px; }
        .menu-title h2 { font-size: 1.8em; font-weight: 600; color: #333; margin-bottom: 10px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 20px; justify-content: center; }
        .menu-item { background-color: #f5f5f5; border: none; border-radius: 12px; padding: 30px; text-align: center; font-size: 1.1em; font-weight: 600; color: #003366; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease; cursor: pointer; }
        .menu-item:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); background-color: #e0e0e0; }
        .back-btn { display: block; width: fit-content; margin: 20px auto 0; text-decoration: none; color: #007BFF; font-weight: 600; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa; transition: all 0.3s ease; }
        .back-btn:hover { background-color: #e2e6ea; color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <img src="https://plp.com.br/wp-content/themes/jupiter/images/logo-plp-novo.png" alt="Logo PLP" class="logo">
            </div>
            <img src="Logo/logo_name.png" alt="CHECKLIST T.I" class="header-title-img">
            <a href="logout.php" class="logout-btn">Sair</a>
        </div>
        
        <div class="menu-title">
            <h2>Selecione o tipo de Checklist para a Filial: <?php echo $filial_display; ?></h2>
        </div>

        <div class="menu-grid">
            <a href="checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=infra" class="menu-item">Infraestrutura</a>
            <a href="checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=backup" class="menu-item">Backup</a>
            <a href="checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=helpdesk" class="menu-item">Helpdesk</a>
            <a href="checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=monitoramento" class="menu-item">Monitoramento</a>
            <a href="checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=checklist" class="menu-item">Geral</a>
            <a href="checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=tape_inventory" class="menu-item">Tape Inventory</a>
        </div>
        
        <a href="checklist.php" class="back-btn">Voltar para o Menu Principal</a>
    </div>
</body>
</html>