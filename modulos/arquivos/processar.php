<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'facilitador']);

// Configurações para upload
$uploadDir = __DIR__ . '/../../uploads/';
$allowedTypes = ['application/pdf'];

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Processar ação
$action = $_GET['action'] ?? '';

if ($action === 'delete' && isset($_GET['id'])) {
    // Exclusão
    $id = $_GET['id'];

    // Buscar arquivo para remover
    $sql = "SELECT arquivo_relatorio FROM processos_circulares_arquivamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $arquivamento = $result->fetch_assoc();

    if ($arquivamento && $arquivamento['arquivo_relatorio']) {
        $filePath = $uploadDir . $arquivamento['arquivo_relatorio'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Excluir registro
    $sql = "DELETE FROM processos_circulares_arquivamentos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['msg'] = ['success', 'Arquivamento excluído com sucesso!'];
    } else {
        $_SESSION['msg'] = ['danger', 'Erro ao excluir arquivamento: ' . $stmt->error];
    }

    header('Location: listar.php');
    exit;
}

// Processar formulário (create/update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $dados = [
        'nome_completo' => $_POST['nome_completo'],
        'data_nascimento' => $_POST['data_nascimento'],
        'facilitador' => $_POST['facilitador'],
        'orgao_origem' => $_POST['orgao_origem'],
        'envio_relatorio' => $_POST['envio_relatorio'] ?: null,
        'arquivo_relatorio' => $_POST['arquivo_atual'] ?? null
    ];

    // Processar upload de arquivo
    if (isset($_FILES['arquivo_relatorio']) && $_FILES['arquivo_relatorio']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['arquivo_relatorio'];

        // Verificar tipo do arquivo
        if (in_array($file['type'], $allowedTypes)) {
            $fileName = uniqid('relatorio_') . '.pdf';
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Remover arquivo anterior se existir
                if ($id && isset($dados['arquivo_relatorio'])) {
                    $oldFilePath = $uploadDir . $dados['arquivo_relatorio'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $dados['arquivo_relatorio'] = $fileName;
            }
        }
    }

    // Salvar no banco de dados
    if ($id) {
        // Atualização
        $sql = "UPDATE processos_circulares_arquivamentos SET 
                nome_completo = ?,
                data_nascimento = ?,
                facilitador = ?,
                orgao_origem = ?,
                envio_relatorio = ?,
                arquivo_relatorio = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $dados['nome_completo'],
            $dados['data_nascimento'],
            $dados['facilitador'],
            $dados['orgao_origem'],
            $dados['envio_relatorio'],
            $dados['arquivo_relatorio'],
            $id
        );
    } else {
        // Inserção
        $sql = "INSERT INTO processos_circulares_arquivamentos (
            nome_completo,
            data_nascimento,
            facilitador,
            orgao_origem,
            envio_relatorio,
            arquivo_relatorio
        ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssss",
            $dados['nome_completo'],
            $dados['data_nascimento'],
            $dados['facilitador'],
            $dados['orgao_origem'],
            $dados['envio_relatorio'],
            $dados['arquivo_relatorio']
        );
    }

    if ($stmt->execute()) {
        $_SESSION['msg'] = ['success', $id ? 'Arquivamento atualizado com sucesso!' : 'Arquivamento criado com sucesso!'];
    } else {
        $_SESSION['msg'] = ['danger', 'Erro ao salvar: ' . $stmt->error];
    }

    header('Location: listar.php');
    exit;
}

// Redirecionar se acesso direto
header('Location: listar.php');
exit;
