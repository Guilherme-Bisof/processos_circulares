<?php



header('Content-Type: application/json');


error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../core/conexao.php';
    require_once __DIR__ . '/../../core/auth.php';


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $id = $_POST['id'] ?? null;
    $situacao = $_POST['situacao'] ?? null;

    if (!$id || !$situacao) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    // Validar situações permitidas
    $situacoes_validas = ['Agendado', 'Círculo realizado', 'Faltou', 'Cancelado'];
    if (!in_array($situacao, $situacoes_validas)) {
        echo json_encode(['success' => false, 'message' => 'Situação inválida']);
        exit;
    }

    // Validar se o processo existe
    $sql_check = "SELECT id FROM processos_circulares_total WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    if (!$stmt_check) {
        throw new Exception('Erro na preparação da consulta: ' . $conn->error);
    }
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Processo não encontrado']);
        $stmt_check->close();
        exit;
    }

    $stmt_check->close();

    // Atualizar a situação do processo
    $sql = "UPDATE processos_circulares_total SET situacao = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Erro na preparação da query: ' . $conn->error);
    }

    $stmt->bind_param("si", $situacao, $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Situação atualizada com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhum registro foi atualizado']);
        }
    } else {
        throw new Exception('Erro na execução da query: ' . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {

    error_log("Erro em atualizar_situacao.php: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
