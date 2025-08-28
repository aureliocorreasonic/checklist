<?php
session_start();
ini_set('memory_limit', '256M'); // Aumenta o limite de memória do PHP
date_default_timezone_set('America/Sao_Paulo');
if(!isset($_SESSION['user_id'])){
    header("location: login.php");
    exit;
}

require_once 'db_config.php';
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurações para o Dompdf
$options = new Options();
$options->set('defaultFont', 'Poppins');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$submission_id = null;
$filial = null;
$checklist_type = null;
$items = [];
$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'] ?? 0;
$data_preenchimento = date("Y-m-d H:i:s");

// Conecta ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Lógica de processamento e geração do PDF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // === Lógica de inserção no banco de dados ===
    $filial = isset($_POST['filial']) ? $_POST['filial'] : 'Nao Identificado';
    $checklist_type = isset($_POST['checklist_type']) ? $_POST['checklist_type'] : '';
    $tape_number = ($checklist_type === 'tape_inventory') ? ($_POST['tape_data']['tape_number'] ?? NULL) : NULL;
    
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO submissions (filial, user_id, data_preenchimento, checklist_type, tape_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $filial, $user_id, $data_preenchimento, $checklist_type, $tape_number);
        $stmt->execute();
        $submission_id = $stmt->insert_id;
        $stmt->close();
        
        if ($checklist_type === 'tape_inventory') {
            $tape_data = $_POST['tape_data'];
            
            $item_map = [
                'tape_inserted' => 108, 'inserted_location' => 110,
                'tape_removed' => 109, 'removed_location' => 111,
                'job_name' => 102, 'overwritten' => 104,
                'last_used' => 105, 'last_overwritten' => 106
            ];
            
            foreach ($tape_data as $key => $value) {
                if (isset($item_map[$key])) {
                    $item_id = $item_map[$key];
                    $status = $value;
                    $observacao = null;

                    $stmt_item = $conn->prepare("INSERT INTO itens (submission_id, item_id, status, observacao) VALUES (?, ?, ?, ?)");
                    $stmt_item->bind_param("isss", $submission_id, $item_id, $status, $observacao);
                    $stmt_item->execute();
                    $stmt_item->close();
                }
            }
        } else {
            foreach ($_POST['items'] as $item) {
                $item_id = $item['id'];
                $status = $item['status'];
                $observacao = isset($item['observation']) ? $item['observation'] : '';
                
                if (isset($item['tipo_backup']) && !empty($item['tipo_backup'])) {
                    $observacao .= (empty($observacao) ? '' : "\n") . "Tipo de Backup: " . $item['tipo_backup'];
                }

                if (isset($item['backup_cloud']) && !empty($item['backup_cloud'])) {
                    $observacao .= (empty($observacao) ? '' : "\n") . "Cloud Replication: " . $item['backup_cloud'];
                }

                if (empty(trim($observacao))) {
                    $observacao = NULL;
                }

                $stmt = $conn->prepare("INSERT INTO itens (submission_id, item_id, status, observacao) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $submission_id, $item_id, $status, $observacao);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        die("Erro ao salvar o checklist: " . $e->getMessage());
    }

    $sql = "SELECT id, filial, data_preenchimento, checklist_type, user_id FROM submissions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $submission_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $submission_data = $result->fetch_assoc();
        $filial = $submission_data['filial'];
        $checklist_type = $submission_data['checklist_type'];
        $data_preenchimento = $submission_data['data_preenchimento'];
        $submission_user_id = $submission_data['user_id'];
    }
    $stmt->close();

    $sql_items = "SELECT item_id, status, observacao FROM itens WHERE submission_id = ?";
    $stmt_items = $conn->prepare($sql_items);
    $stmt_items->bind_param("i", $submission_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();

    if ($result_items->num_rows > 0) {
        while ($row = $result_items->fetch_assoc()) {
            $items[] = $row;
        }
    }
    $stmt_items->close();

} elseif (isset($_GET['submission_id'])) {
    $submission_id = $_GET['submission_id'];

    if ($is_admin === 1) {
        $sql = "SELECT filial, data_preenchimento, checklist_type, user_id FROM submissions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $submission_id);
    } else {
        $sql = "SELECT filial, data_preenchimento, checklist_type, user_id FROM submissions WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $submission_id, $user_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $submission_data = $result->fetch_assoc();
        $filial = $submission_data['filial'];
        $checklist_type = $submission_data['checklist_type'];
        $data_preenchimento = $submission_data['data_preenchimento'];
        $submission_user_id = $submission_data['user_id'];

        $sql_items = "SELECT item_id, status, observacao FROM itens WHERE submission_id = ?";
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $submission_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();

        if ($result_items->num_rows > 0) {
            while ($row = $result_items->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt_items->close();
    }
    $stmt->close();
} else {
    header("location: checklist.php");
    exit;
}

$sql_username = "SELECT username FROM users WHERE id = ?";
$stmt_username = $conn->prepare($sql_username);
$stmt_username->bind_param("i", $submission_user_id);
$stmt_username->execute();
$result_username = $stmt_username->get_result();
$submission_username = "Usuário Desconhecido";
if ($result_username->num_rows > 0) {
    $submission_username = $result_username->fetch_assoc()['username'];
}
$stmt_username->close();

$item_names = [];
if ($checklist_type) {
    if ($checklist_type === 'tape_inventory') {
        $item_names = [
            108 => 'Fita Inserida Nº',
            109 => 'Fita Retirada Nº',
            110 => 'Fita Inserida Local',
            111 => 'Fita Retirada Local',
            102 => 'Tipo de Backup',
            104 => 'Fita Regravada?',
            105 => 'Última vez utilizada (Data)',
            106 => 'Última vez regravada'
        ];
    } else {
        $sql_names = "SELECT item_id, item_name FROM checklist_item_names WHERE checklist_type = ?";
        $stmt_names = $conn->prepare($sql_names);
        $stmt_names->bind_param("s", $checklist_type);
        $stmt_names->execute();
        $result_names = $stmt_names->get_result();
        if ($result_names->num_rows > 0) {
            while($row = $result_names->fetch_assoc()) {
                $item_names[$row['item_id']] = $row['item_name'];
            }
        }
        $stmt_names->close();
    }
}

$conn->close();

$titulo_mapa = [
    'infra' => 'Checklist de Infraestrutura',
    'backup' => 'Checklist de Backup',
    'helpdesk' => 'Checklist de Helpdesk',
    'monitoramento' => 'Checklist de Monitoramento',
    'checklist' => 'Checklist',
    'tape_inventory' => 'Tape Inventory'
];
$titulo_documento = $titulo_mapa[strtolower($checklist_type)] ?? 'Checklist';

$filial_display = str_replace('_', ' ', $filial);

$logo_path = '/var/www/html/checklist/Logo/logo-plp.png';
$logo_base64 = '';
if (file_exists($logo_path) && is_readable($logo_path)) {
    $logo_data = file_get_contents($logo_path);
    $logo_base64 = 'data:image/png;base64,' . base64_encode($logo_data);
}

$date_obj = new DateTime($data_preenchimento);
$formatted_date = $date_obj->format('d/m/Y H:i:s');

$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 40px; }
        body { font-family: "Poppins", Arial, sans-serif; font-size: 12px; }
        .main-header-table { width: 100%; border-collapse: collapse; }
        .main-header-table td { vertical-align: top; padding: 0; }
        .logo-cell { width: 150px; text-align: left; }
        .title-cell { text-align: center; }
        .blank-cell { width: 150px; }
        /* INÍCIO DA MODIFICAÇÃO: Redução do tamanho do logo */
        .logo { width: 84px; }
        /* FIM DA MODIFICAÇÃO */
        .titulo-principal { color: #003366; font-size: 24px; margin: 0; font-weight: 600; }
        .subtitulo { font-size: 16px; margin: 0; font-weight: normal; color: #555; }
        .info-table { width: 100%; font-size: 12px; color: #666; margin-top: 15px; }
        .info-table td { padding-right: 20px; text-align: left; }
        .info-table td:nth-child(2) { text-align: center; }
        .info-table td:nth-child(3) { text-align: right; }
        .hr-line { height: 1px; background-color: #ddd; border: none; margin: 10px 0 20px 0; }
        .hr-line-sub { height: 1px; background-color: #ddd; border: none; margin: 0 0 20px 0; }
        .checklist-item { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; background-color: #f9f9f9; box-shadow: 0 2px 5px rgba(0,0,0,0.05); page-break-inside: avoid; }
        .item-titulo { font-weight: 600; font-size: 14px; color: #003366; margin: 0 0 8px 0; }
        .item-status { font-weight: 600; padding: 4px 10px; border-radius: 20px; font-size: 11px; text-transform: uppercase; color: #fff; }
        .item-obs { font-size: 12px; color: #555; margin-top: 10px; border-top: 1px dashed #ddd; padding-top: 8px; }
        .status-ok { background-color: #4CAF50; }
        .status-na { background-color: #808080; }
        .status-nok { background-color: #F44336; }
        .status-running { background-color: #007BFF; }
        .status-erro { background-color: #F44336; }
        .status-warning { background-color: #ffc107; }
    </style>
</head>
<body>
    <table class="main-header-table">
        <tr>
            <td class="logo-cell">
                ' . ($logo_base64 ? '<img class="logo" src="' . $logo_base64 . '" alt="Logo PLP">' : '') . '
            </td>
            <td class="title-cell">
                <h1 class="titulo-principal">CHECKLIST</h1>
                <h2 class="subtitulo">' . htmlspecialchars($titulo_documento) . '</h2>
            </td>
            <td class="blank-cell"></td>
        </tr>
    </table>
    <hr class="hr-line">
    <table class="info-table">
        <tr>
            <td><strong>Filial:</strong> ' . htmlspecialchars(str_replace('_', ' ', $filial)) . '</td>
            <td><strong>Analista:</strong> ' . htmlspecialchars($submission_username) . '</td>
            <td><strong>Data:</strong> ' . $formatted_date . '</td>
        </tr>
    </table>
    <hr class="hr-line-sub">
    <div class="checklist-container">
';

foreach ($items as $item) {
    $item_id = $item['item_id'];
    $item_name = isset($item_names[$item_id]) ? $item_names[$item_id] : "Item {$item_id}";
    
    if ($checklist_type !== 'tape_inventory') {
        $status_class = '';
        $status_text = htmlspecialchars($item['status']);
        switch($item['status']) {
            case 'ok': $status_class = 'status-ok'; $status_text = 'OK'; break;
            case 'na': $status_class = 'status-na'; $status_text = 'N/A'; break;
            case 'nok': $status_class = 'status-nok'; $status_text = 'NOK'; break;
            case 'running': $status_class = 'status-running'; $status_text = 'RUNNING'; break;
            case 'erro': $status_class = 'status-erro'; $status_text = 'ERRO'; break;
            case 'warning': $status_class = 'status-warning'; $status_text = 'WARNING'; break;
            default: $status_class = 'status-na'; $status_text = 'N/A'; break;
        }
        
        $html .= '<div class="checklist-item">';
        $html .= '<h4 class="item-titulo">' . htmlspecialchars($item_name) . '</h4>';
        $html .= '<div>Status: <span class="item-status ' . $status_class . '">' . htmlspecialchars(strtoupper($status_text)) . '</span></div>';
        
        $observacao_original = isset($item['observacao']) ? $item['observacao'] : '';
        $observacao_display = $observacao_original;

        $tipo_backup_display = '';
        $pattern_tipo = '/Tipo de Backup: (.*?)(?:\n|$)/';
        if (preg_match($pattern_tipo, $observacao_display, $matches)) {
            $tipo_backup_display = trim($matches[1]);
            $observacao_display = str_replace($matches[0], '', $observacao_display);
        }

        $cloud_replication_display = '';
        $pattern_cloud = '/Cloud Replication: (.*?)(?:\n|$)/';
        if (preg_match($pattern_cloud, $observacao_display, $matches)) {
            $cloud_replication_display = trim($matches[1]);
            $observacao_display = str_replace($matches[0], '', $observacao_display);
        }

        if (!empty($tipo_backup_display)) {
            $html .= '<p><strong>Tipo de Backup:</strong> ' . htmlspecialchars($tipo_backup_display) . '</p>';
        }

        if (!empty($cloud_replication_display)) {
            $html .= '<p><strong>Cloud Replication:</strong> ' . htmlspecialchars($cloud_replication_display) . '</p>';
        }

        $observacao_display = trim($observacao_display);
        if (empty($observacao_display)) {
            $observacao_display = "Nenhuma observação.";
        } else {
            $observacao_display = nl2br(htmlspecialchars($observacao_display));
        }

        $html .= '<p class="item-obs"><strong>Observação:</strong> ' . $observacao_display . '</p>';
        $html .= '</div>';

    } else {
        // Lógica de renderização para Tape Inventory permanece a mesma
        $html .= '<div class="checklist-item">';
        $html .= '<h4 class="item-titulo">' . htmlspecialchars($item_name) . '</h4>';

        $display_value = htmlspecialchars($item['status']);
        
        if (($item_id === 105 || $item_id === 106) && !empty($item['status'])) {
            $timestamp = strtotime($item['status']);
            if ($timestamp !== false) {
                $display_value = date('d/m/Y', $timestamp);
            }
        }
        
        $html .= '<p>' . $display_value . '</p>';
        $html .= '</div>';
    }
}

$html .= '
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdf_output = $dompdf->output();

$base_save_path = '/mnt/repositorio_checklists/'; 
$sanitized_filial = preg_replace('/[^a-zA-Z0-9\s]/', '', $filial);
$filial_save_path = $base_save_path . str_replace(' ', '_', $sanitized_filial) . '/';

if (!file_exists($filial_save_path)) {
    mkdir($filial_save_path, 0777, true);
}

$filename = "checklist_{$filial}_" . date("Ymd_His") . ".pdf";

if (is_writable($filial_save_path)) {
    file_put_contents($filial_save_path . $filename, $pdf_output);
} else {
    error_log("Erro: O diretório de destino ($filial_save_path) não é gravável.");
}

$dompdf->stream($filename, array("Attachment" => true));
?>