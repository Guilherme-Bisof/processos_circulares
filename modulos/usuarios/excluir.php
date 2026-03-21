<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = (int) $_GET['id'];

// Verificar se o usuário existe
$sql = "SELECT id FROM usuarios_circulares WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Erro no prepare: ' . $conn->error);
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['msg_erro'] = 'Usuário não encontrado.';
    header('Location: listar.php');
    exit;
}

$stmt->close();

$sql = "DELETE FROM usuarios_circulares WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Erro no prepare: ' . $conn->error);
}
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    $_SESSION['msg_sucesso'] = 'Usuário excluído com sucesso!';
} else {
    $_SESSION['msg_erro'] = 'Erro ao excluir usuário: ' . $stmt->error;
}

$stmt->close();
header('Location: listar.php');
exit;
