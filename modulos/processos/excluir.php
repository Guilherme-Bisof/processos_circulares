<?php
// processos_circulares/excluir.php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'recepcao']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Excluir processo
$stmt = $conn->prepare("DELETE FROM processos_circulares_total WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setFlash('success', 'Processo excluído com sucesso!');
} else {
    setFlash('error', 'Erro ao excluir processo: ' . $stmt->error);
}

$stmt->close();
header('Location: index.php');
exit;
