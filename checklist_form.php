<?php
session_start();
ini_set('memory_limit', '256M');
date_default_timezone_set('America/Sao_Paulo');
if(!isset($_SESSION['user_id'])){
    header("location: login.php");
    exit;
}

require_once 'db_config.php';

$filial = isset($_GET['filial']) ? htmlspecialchars($_GET['filial']) : 'Nao Identificado';
$filial_display = str_replace('_', ' ', $filial);
$checklist_type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'infra';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$items = [];
// A busca por itens só é necessária para os checklists existentes
if ($checklist_type !== 'tape_inventory') {
    $sql = "SELECT item_id, item_name 
            FROM checklist_item_names 
            WHERE checklist_type = ? 
            AND (filial IS NULL OR filial = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $checklist_type, $filial);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    $stmt->close();
}
$conn->close();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="icon" type="image/png" href="Logo/logo-plp.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist de <?php echo ucfirst(str_replace('_', ' ', $checklist_type)); ?> - <?php echo $filial_display; ?></title>
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
        h2, p { font-family: 'Poppins', Arial, sans-serif; font-weight: 600; color: #333; }
        .checklist-item { display: flex; flex-direction: column; margin-bottom: 20px; border-radius: 8px; border: 1px solid #ddd; padding: 15px; }
        .item-row { display: flex; align-items: center; margin-bottom: 10px; flex-wrap: wrap; }
        .item-text { flex-grow: 1; font-size: 16px; font-weight: 600; color: #003366; margin-right: 20px; min-width: 200px; }
        .item-status { display: flex; gap: 10px; margin-right: 20px; align-items: center; }
        .status-btn { padding: 8px 12px; font-weight: 600; border: 2px solid #ccc; border-radius: 5px; cursor: pointer; transition: all 0.2s; background-color: #f5f5f5; color: #333; }
        .status-btn.active { border-width: 2px; background-color: #fff; box-shadow: 0 0 5px rgba(0,0,0,0.2); transform: scale(1.05); }
        .status-btn.ok.active { border-color: #4CAF50; color: #4CAF50; }
        .status-btn.na.active { border-color: #808080; color: #808080; }
        .status-btn.nok.active { border-color: #F44336; color: #F44336; }
        .status-btn.running.active { border-color: #007BFF; color: #007BFF; }
        .status-btn.erro.active { border-color: #F44336; color: #F44336; }
        .status-btn.warning.active { border-color: #ffc107; color: #ffc107; }
        .item-observation { flex: 2; }
        .item-observation textarea { width: 100%; border: 1px solid #ccc; border-radius: 4px; padding: 8px; resize: vertical; font-family: inherit; }
        .save-btn { display: block; width: 100%; padding: 15px; background-color: #007BFF; color: #fff; border: none; border-radius: 8px; font-size: 18px; cursor: pointer; margin-top: 20px; font-weight: 600; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.3s ease, background-color 0.3s ease; }
        .save-btn:hover { background-color: #0056b3; transform: translateY(-2px); }
        .footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 15px; border-top: 1px solid #eee; }
        .back-btn { text-decoration: none; color: #007BFF; font-weight: 600; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa; transition: all 0.3s ease; }
        .back-btn:hover { background-color: #e2e6ea; color: #0056b3; }
        
        .checklist-item label { font-weight: 600; margin-bottom: 5px; color: #003366; }
        .checklist-item input[type="date"], .checklist-item input[type="text"], .checklist-item textarea, .checklist-item select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .checklist-item .item-status input[type="radio"] { width: auto; }
        
        /* INÍCIO DA MODIFICAÇÃO: Ajustes de Estilo para Alinhamento */
        .item-tipo-backup, .item-backup-cloud {
            display: flex;
            flex-direction: column;
            margin-left: 20px;
        }

        .item-tipo-backup > label, .item-backup-cloud > label {
            margin: 0 0 4px 0;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .item-tipo-backup select {
            width: 150px;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-family: inherit;
        }

        .radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
            height: 38px; /* Mesma altura do select box */
        }
        .radio-group label {
            font-weight: normal;
            font-size: 14px;
            color: #333;
            margin: 0;
            display: flex;
            align-items: center;
        }
        .radio-group input[type="radio"] {
            margin-right: 5px;
        }
        /* FIM DA MODIFICAÇÃO */
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <div class="header-left">
                <img src="https://plp.com.br/wp-content/themes/jupiter/images/logo-plp-novo.png" alt="Logo PLP" class="logo">
            </div>
            <img src="Logo/logo_name.png" alt="CHECKLIST T.I" class="header-title-img">
        </div>
        
        <h2>Checklist de <?php echo ucfirst(str_replace('_', ' ', $checklist_type)); ?> - <?php echo $filial_display; ?></h2>
        <p>Olá, <?php echo $_SESSION['username']; ?>!</p>
        
        <form action="generate_pdf.php" method="POST">
            <input type="hidden" name="filial" value="<?php echo htmlspecialchars($filial); ?>">
            <input type="hidden" name="checklist_type" value="<?php echo htmlspecialchars($checklist_type); ?>">

            <?php if ($checklist_type === 'tape_inventory'): ?>
                <div class="checklist-item">
                    <label for="job_name">Backup:</label>
                    <select id="job_name" name="tape_data[job_name]" required>
                        <option value="">Selecione...</option>
                        <option value="Backup 2º Semana">Backup 2º Semana</option>
                        <option value="Backup 3º Semana">Backup 3º Semana</option>
                        <option value="Backup 4º Semana">Backup 4º Semana</option>
                        <option value="Backup 5º Semana">Backup 5º Semana</option>
                        <option value="Backup Mensal">Backup Mensal</option>
                    </select>
                </div>
                <div class="checklist-item">
                    <label for="tape_inserted">Fita Inserida Nº:</label>
                    <input type="text" id="tape_inserted" name="tape_data[tape_inserted]" placeholder="Ex: fita01" required>
                    <label for="inserted_location">Local:</label>
                    <select id="inserted_location" name="tape_data[inserted_location]" required>
                        <option value="">Selecione...</option>
                        <option value="Cofre">Cofre</option>
                        <option value="Armário">Armário</option>
                        <option value="Tape">Tape</option>
                    </select>
                    <label for="last_used">Última vez utilizada (Data):</label>
                    <input type="text" id="last_used" name="tape_data[last_used]" placeholder="Preenchido automaticamente" readonly>
                </div>
                <div class="checklist-item">
                    <label for="tape_removed">Fita Retirada Nº:</label>
                    <input type="text" id="tape_removed" name="tape_data[tape_removed]" placeholder="Ex: fita02" required>
                    <label for="removed_location">Local:</label>
                     <select id="removed_location" name="tape_data[removed_location]" required>
                        <option value="">Selecione...</option>
                        <option value="Cofre">Cofre</option>
                        <option value="Armário">Armário</option>
                        <option value="Tape">Tape</option>
                    </select>
                </div>
                <div class="checklist-item">
                    <label>Fita Regravada?</label>
                    <div>
                        <input type="radio" id="overwritten_yes" name="tape_data[overwritten]" value="Sim" required>
                        <label for="overwritten_yes">Sim</label>
                        <input type="radio" id="overwritten_no" name="tape_data[overwritten]" value="Não" required>
                        <label for="overwritten_no">Não</label>
                    </div>
                </div>
                
                <?php else:
                $i = 0;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        echo '<div class="checklist-item">';
                        echo '    <div class="item-row">';
                        echo '        <div class="item-text">' . htmlspecialchars($item['item_name']) . '</div>';
                        echo '        <div class="item-status">';
                        
                        switch ($checklist_type) {
                            case 'infra':
                            case 'helpdesk':
                            case 'checklist':
                            case 'monitoramento':
                                echo '        <button type="button" class="status-btn ok" data-status="ok" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">OK</button>';
                                echo '        <button type="button" class="status-btn na" data-status="na" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">N/A</button>';
                                echo '        <button type="button" class="status-btn nok" data-status="nok" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">NOK</button>';
                                break;
                            case 'backup':
                                echo '        <button type="button" class="status-btn ok" data-status="ok" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">OK</button>';
                                echo '        <button type="button" class="status-btn running" data-status="running" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">Running</button>';
                                echo '        <button type="button" class="status-btn na" data-status="na" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">N/A</button>';
                                echo '        <button type="button" class="status-btn erro" data-status="erro" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">Erro</button>';
                                echo '        <button type="button" class="status-btn warning" data-status="warning" onclick="selectStatus(this, ' . htmlspecialchars($item['item_id']) . ')">Warning</button>';
                                
                                echo '        <div class="item-tipo-backup">';
                                echo '            <label for="tipo_backup_' . htmlspecialchars($item['item_id']) . '">Tipo de Backup:</label>';
                                echo '            <select name="items[' . $i . '][tipo_backup]" id="tipo_backup_' . htmlspecialchars($item['item_id']) . '">';
                                echo '                <option value="">Selecione...</option>';
                                echo '                <option value="Full Backup">Full Backup</option>';
                                echo '                <option value="Incremental Backup">Incremental Backup</option>';
                                echo '            </select>';
                                echo '        </div>';

                                // INÍCIO DA MODIFICAÇÃO: Renomeia e reestrutura a seção
                                echo '        <div class="item-backup-cloud">';
                                echo '            <label>Cloud Replication:</label>';
                                echo '            <div class="radio-group">';
                                echo '                <label><input type="radio" name="items[' . $i . '][backup_cloud]" value="Sim">Sim</label>';
                                echo '                <label><input type="radio" name="items[' . $i . '][backup_cloud]" value="Não">Não</label>';
                                echo '            </div>';
                                echo '        </div>';
                                // FIM DA MODIFICAÇÃO

                                break;
                        }
                        
                        echo '            <input type="hidden" name="items[' . $i . '][id]" value="' . htmlspecialchars($item['item_id']) . '">';
                        echo '            <input type="hidden" name="items[' . $i . '][status]" id="status_' . htmlspecialchars($item['item_id']) . '">';
                        echo '        </div>';
                        echo '    </div>';
                        echo '    <div class="item-observation">';
                        echo '        <textarea name="items[' . $i . '][observation]" placeholder="Observação..."></textarea>';
                        echo '    </div>';
                        echo '</div>';
                        $i++;
                    }
                } else {
                    echo '<p>Nenhum item encontrado para este tipo de checklist.</p>';
                }
            endif;
            ?>
            
            <div class="footer">
                <a href="submenu.php?filial=<?php echo htmlspecialchars($filial); ?>" class="back-btn">Voltar</a>
                <a href="logout.php" class="logout-btn">Sair</a>
            </div>

            <button type="submit" class="save-btn">Salvar Checklist</button>
        </form>
    </div>

    <script>
        function selectStatus(button, itemId) {
            const status = button.dataset.status;
            const input = document.getElementById('status_' + itemId);
            
            const itemElement = button.closest('.checklist-item');
            const allButtons = itemElement.querySelectorAll('.status-btn');
            
            allButtons.forEach(btn => btn.classList.remove('active'));
            
            button.classList.add('active');
            input.value = status;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const success = new URLSearchParams(window.location.search).get('success');
            if (success) {
                alert('Checklist salvo com sucesso!');
                window.location.href = 'checklist_form.php?filial=<?php echo htmlspecialchars($filial); ?>&type=<?php echo htmlspecialchars($checklist_type); ?>';
            }

            const tapeInsertedInput = document.getElementById('tape_inserted');
            if (tapeInsertedInput) {
                tapeInsertedInput.addEventListener('blur', function() {
                    const tapeNumber = this.value;
                    const lastUsedInput = document.getElementById('last_used');

                    if (tapeNumber.length > 0) {
                        lastUsedInput.value = 'Buscando...';

                        fetch(`get_tape_history.php?tape_number=${encodeURIComponent(tapeNumber)}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Erro de rede ou no servidor');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.error) {
                                    console.error('Erro ao buscar histórico:', data.error);
                                    lastUsedInput.value = 'Histórico não encontrado.';
                                } else {
                                    lastUsedInput.value = data.last_used || 'Nenhum histórico encontrado.';
                                }
                            })
                            .catch(error => {
                                console.error('Erro na requisição:', error);
                                lastUsedInput.value = 'Erro ao buscar dados.';
                            });
                    } else {
                        lastUsedInput.value = '';
                    }
                });
            }
        });
    </script>
</body>
</html>