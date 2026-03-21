<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'recepcao_agenda']);

if (!isset($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = $_GET['id'];

// Verificar se o ofício existe
$sql = "SELECT id FROM oficios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['msg_erro'] = 'Ofício não encontrado!';
    header('Location: listar.php');
    exit;
}

// Excluir o ofício
$sql = "DELETE FROM oficios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['msg_sucesso'] = 'Ofício excluído com sucesso!';
} else {
    $_SESSION['msg_erro'] = 'Erro ao excluir ofício: ' . $stmt->error;
}

header('Location: listar.php');
exit;
