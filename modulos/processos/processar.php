<?php
include '../../includes/auth.php';
include '../../includes/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_processo = $_POST['numero_processo'];
    $infratores = $_POST['infratores'];
    $vitimas = $_POST['vitimas'];
    $data_bo = $_POST['data_bo'];
    $data_entrada = $_POST['data_entrada'];
    $situacao = $_POST['situacao'];
    $facilitador = $_POST['facilitador'];
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];

    $sql = "INSERT INTO processos_circulares_total 
        (numero_processo, infratores, vitimas, data_bo, data_entrada, situacao, facilitador, status, observacoes)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Erro ao preparar consulta: ' . $conn->error);
    }
    $stmt->bind_param("sssssssss", $numero_processo, $infratores, $vitimas, $data_bo, $data_entrada, $situacao, $facilitador, $status, $observacoes);

    if ($stmt->execute()) {
        header("Location: index.php?sucesso=1");
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
