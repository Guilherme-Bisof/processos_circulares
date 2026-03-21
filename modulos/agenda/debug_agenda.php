<?php
// Arquivo de debug para testar a criação de agendamentos
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';

// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

echo "Debug de criação de agendamento\n";
echo "Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'não definido') . "\n";

$action = $_GET['action'] ?? 'não definido';
echo "Action: " . $action . "\n";

if ($action === 'test_create') {
    // Dados de teste
    $dados_teste = [
        'nome_completo' => 'Teste João Silva',
        'participantes' => 'Katia',
        'data_agendamento' => '2025-09-15',
        'hora_agendamento' => '14:30',
        'prioridade' => 'media',
        'observacoes' => 'Teste de criação',
        'status' => 'Agendado'
    ];

    echo "\nDados de teste:\n";
    print_r($dados_teste);

    // Testar inserção no banco
    $sql = "INSERT INTO processos_circulares_agenda (
        nome_completo,
        participantes,
        data_agendamento,
        hora_agendamento,
        prioridade,
        observacoes,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "\nERRO na preparação: " . $conn->error . "\n";
        exit;
    }

    $stmt->bind_param(
        "sssssss",
        $dados_teste['nome_completo'],
        $dados_teste['participantes'],
        $dados_teste['data_agendamento'],
        $dados_teste['hora_agendamento'],
        $dados_teste['prioridade'],
        $dados_teste['observacoes'],
        $dados_teste['status']
    );

    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        echo "\nSUCESSO! ID inserido: " . $id . "\n";

        $response = [
            'success' => true,
            'id' => $id,
            'title' => $dados_teste['nome_completo'] . " - " . $dados_teste['hora_agendamento'],
            'start' => $dados_teste['data_agendamento'] . 'T' . $dados_teste['hora_agendamento'],
            'extendedProps' => [
                'id' => $id,
                'titulo' => $dados_teste['nome_completo'],
                'participantes' => $dados_teste['participantes'],
                'data_agendamento' => $dados_teste['data_agendamento'],
                'hora_agendamento' => $dados_teste['hora_agendamento'],
                'prioridade' => $dados_teste['prioridade'],
                'observacoes' => $dados_teste['observacoes'],
                'status' => $dados_teste['status'],
                'type' => 'agenda'
            ]
        ];

        echo "\nResposta JSON:\n";
        echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
        echo "\nERRO na execução: " . $stmt->error . "\n";
    }
}

if ($action === 'test_table') {
    echo "\nTestando estrutura da tabela:\n";

    $sql = "DESCRIBE processos_circulares_agenda";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
    } else {
        echo "ERRO: " . $conn->error . "\n";
    }
}

if ($action === 'test_list') {
    echo "\nListando registros existentes:\n";

    $sql = "SELECT * FROM processos_circulares_agenda LIMIT 5";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
    } else {
        echo "ERRO: " . $conn->error . "\n";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    echo "\nRecebendo POST para create:\n";

    $input = file_get_contents('php://input');
    echo "Input bruto: " . $input . "\n";

    $data = json_decode($input, true);
    echo "Dados decodificados:\n";
    print_r($data);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "ERRO JSON: " . json_last_error_msg() . "\n";
    }
}
