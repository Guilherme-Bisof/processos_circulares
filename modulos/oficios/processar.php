<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'recepcao_agenda']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    $dados = [
        'numero_oficio' => $_POST['numero_oficio'],
        'nome_completo' => $_POST['nome_completo'],
        'tipo_oficio' => $_POST['tipo_oficio'],
        'status_oficio' => $_POST['status_oficio'],
        'data_criacao' => $_POST['data_criacao']
    ];

    if ($id) {
        // Atualizar ofício existente
        $sql = "UPDATE oficios SET 
                numero_oficio = ?,
                nome_completo = ?,
                tipo_oficio = ?,
                status_oficio = ?,
                data_criacao = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $dados['numero_oficio'],
            $dados['nome_completo'],
            $dados['tipo_oficio'],
            $dados['status_oficio'],
            $dados['data_criacao'],
            $id
        );
    } else {
        // Criar novo ofício
        $sql = "INSERT INTO oficios (
            numero_oficio,
            nome_completo,
            tipo_oficio,
            status_oficio,
            data_criacao
        ) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssss",
            $dados['numero_oficio'],
            $dados['nome_completo'],
            $dados['tipo_oficio'],
            $dados['status_oficio'],
            $dados['data_criacao']
        );
    }

    if ($stmt->execute()) {
        $_SESSION['msg_sucesso'] = $id ? 'Ofício atualizado com sucesso!' : 'Ofício criado com sucesso!';
    } else {
        $_SESSION['msg_erro'] = 'Erro ao salvar ofício: ' . $stmt->error;
    }

    header('Location: listar.php');
    exit;
}

// Redirecionar se acesso direto
header('Location: listar.php');
exit;
