<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'facilitador']); // troquei os perfis para o contexto de círculos

$action = $_GET['action'] ?? '';

if ($action === 'create') {
    // Receber dados do formulário
    $data = json_decode(file_get_contents('php://input'), true);

    // Validação básica dos dados
    if (empty($data['nome_completo']) || empty($data['data_agendamento']) || empty($data['hora_agendamento'])) {
        echo json_encode(['success' => false, 'error' => 'Dados obrigatórios não informados']);
        exit;
    }

    // Inserir no banco de dados
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
        echo json_encode(['success' => false, 'error' => 'Erro na preparação da query: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "sssssss",
        $data['nome_completo'],
        $data['participantes'],
        $data['data_agendamento'],
        $data['hora_agendamento'],
        $data['prioridade'],
        $data['observacoes'],
        $data['status']
    );

    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        // Retornar o novo evento criado
        echo json_encode([
            'id' => $id,
            'title' => $data['nome_completo'] . " - " . $data['hora_agendamento'],
            'start' => $data['data_agendamento'] . 'T' . $data['hora_agendamento'],
            'extendedProps' => [
                'id' => $id,
                'titulo' => $data['nome_completo'], // Corrigido: era uma chave vazia
                'participantes' => $data['participantes'],
                'data_agendamento' => $data['data_agendamento'],
                'hora_agendamento' => $data['hora_agendamento'],
                'prioridade' => $data['prioridade'],
                'observacoes' => $data['observacoes'],
                'status' => $data['status'],
                'type' => 'agenda' // Adicionado para identificar o tipo do evento
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

if ($action === 'delete') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id'])) {
        echo json_encode(['success' => false, 'error' => 'ID não informado']);
        exit;
    }

    $id = intval($data['id']);

    $sql = "DELETE FROM processos_circulares_agenda WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Erro na preparação da query: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

if ($action === 'list') {
    // Buscar eventos da agenda
    $sql_agenda = "SELECT * FROM processos_circulares_agenda ORDER BY data_agendamento, hora_agendamento";
    $result_agenda = $conn->query($sql_agenda);

    $eventos = [];

    if ($result_agenda) {
        while ($row = $result_agenda->fetch_assoc()) {
            $eventos[] = [
                'id' => 'agenda_' . $row['id'], // Prefixo para diferenciar
                'title' => $row['nome_completo'] . " - " . $row['hora_agendamento'],
                'start' => $row['data_agendamento'] . 'T' . $row['hora_agendamento'],
                'extendedProps' => [
                    'id' => $row['id'],
                    'titulo' => $row['nome_completo'], // Corrigido: era uma chave vazia
                    'participantes' => $row['participantes'],
                    'data_agendamento' => $row['data_agendamento'],
                    'hora_agendamento' => $row['hora_agendamento'],
                    'prioridade' => $row['prioridade'],
                    'observacoes' => $row['observacoes'],
                    'status' => $row['status'],
                    'type' => 'agenda' // Importante para o JavaScript identificar o tipo
                ]
            ];
        }
    }

    // Se você também quiser incluir processos circulares da outra tabela, descomente:
    /*
    $sql_processos = "SELECT * FROM processos_circulares_total WHERE data_circulo IS NOT NULL ORDER BY data_circulo";
    $result_processos = $conn->query($sql_processos);
    
    if ($result_processos) {
        while ($row = $result_processos->fetch_assoc()) {
            $eventos[] = [
                'id' => 'processo_' . $row['id'],
                'title' => 'Processo: ' . $row['numero_processo'],
                'start' => $row['data_circulo'],
                'extendedProps' => [
                    'id' => $row['id'],
                    'numero_processo' => $row['numero_processo'],
                    'data_circulo' => $row['data_circulo'],
                    'Facilitador' => $row['Facilitador'],
                    'situacao' => $row['situacao'],
                    'type' => 'processo_circular'
                ]
            ];
        }
    }
    */

    header('Content-Type: application/json');
    echo json_encode($eventos);
    exit;
}

// Se chegou até aqui sem action válida
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Ação não reconhecida']);
