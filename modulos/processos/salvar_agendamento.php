<?php



header('Content-Type: application/json');


error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../core/conexao.php';
    require_once __DIR__ . '/../../core/auth.php';

    // Verificar se é uma requisição POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $processo_id = $_POST['processo_id'] ?? null;
    $data_circulo = $_POST['data_circulo'] ?? null;

    if (!$processo_id || !$data_circulo) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Validar se o processo existe
    $sql_check = "SELECT id FROM processos_circulares_total WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    if (!$stmt_check) {
        throw new Exception('Erro na preparação da consulta: ' . $conn->error);
    }
    $stmt_check->bind_param("i", $processo_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Processo não encontrado']);
        $stmt_check->close();
        exit;
    }

    $stmt_check->close();


    $sql_describe = "SHOW COLUMNS FROM processos_circulares_total LIKE 'situacao'";
    $result_describe = $conn->query($sql_describe);
    $column_info = $result_describe->fetch_assoc();

    // Determinar valor da situação baseado na estrutura da coluna
    $situacao_value = 'Agendado';

    // Se for ENUM, verificar se 'Agendado' está disponível
    if (strpos($column_info['Type'], 'enum') !== false) {
        // Extrair valores do ENUM
        preg_match_all("/'([^']+)'/", $column_info['Type'], $matches);
        $enum_values = $matches[1];

        // Verificar se 'Agendado' existe, senão usar o primeiro valor
        if (!in_array('Agendado', $enum_values) && !empty($enum_values)) {
            $situacao_value = $enum_values[0];
        }
    }

    // Verificar se deve incluir a situação na atualização
    $update_situacao = true;

    // Se VARCHAR, verificar tamanho
    if (strpos($column_info['Type'], 'varchar') !== false) {
        preg_match('/varchar\((\d+)\)/', $column_info['Type'], $matches);
        $max_length = isset($matches[1]) ? intval($matches[1]) : 255;

        if (strlen($situacao_value) > $max_length) {
            // Se não couber, não atualizar a situação
            $update_situacao = false;
        }
    }

    // Montar query baseado na necessidade
    if ($update_situacao) {
        $sql = "UPDATE processos_circulares_total SET data_circulo = ?, situacao = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception('Erro na preparação da query: ' . $conn->error);
        }

        $stmt->bind_param("ssi", $data_circulo, $situacao_value, $processo_id);
    } else {
        $sql = "UPDATE processos_circulares_total SET data_circulo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception('Erro na preparação da query: ' . $conn->error);
        }

        $stmt->bind_param("si", $data_circulo, $processo_id);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Agendamento salvo com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhum registro foi atualizado']);
        }
    } else {
        throw new Exception('Erro na execução da query: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {

    error_log("Erro em salvar_agendamento.php: " . $e->getMessage());


    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
