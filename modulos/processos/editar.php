<?php

require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'recepcao']);

$erro = '';
$sucesso = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Buscar processo
$stmt = $conn->prepare("SELECT * FROM processos_circulares_total WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$processo = $result->fetch_assoc();

if (!$processo) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_processo = $_POST['numero_processo'] ?? '';
    $infratores = $_POST['infratores'] ?? '';
    $vitimas = $_POST['vitimas'] ?? '';
    $data_bo = $_POST['data_bo'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';
    $situacao = $_POST['situacao'] ?? 'Solicitação';
    $facilitador = $_POST['facilitador'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';
    $status = $_POST['status'] ?? 'Em andamento';
    $data_circulo = $_POST['data_circulo'] ?? '';

    // Validação básica
    if (empty($numero_processo) || empty($infratores) || empty($vitimas) || empty($data_bo) || empty($data_entrada) || empty($facilitador)) {
        $erro = 'Preencha todos os campos obrigatórios';
    } else {
        // Atualizar no banco
        $stmt = $conn->prepare("UPDATE processos_circulares_total SET 
            numero_processo = ?, 
            infratores = ?, 
            vitimas = ?, 
            data_bo = ?, 
            data_entrada = ?, 
            situacao = ?, 
            facilitador = ?, 
            observacoes = ?,
            status = ?,
            data_circulo = ?
            WHERE id = ?");
        $stmt->bind_param(
            "ssssssssssi",
            $numero_processo,
            $infratores,
            $vitimas,
            $data_bo,
            $data_entrada,
            $situacao,
            $facilitador,
            $observacoes,
            $status,
            $data_circulo,
            $id
        );

        if ($stmt->execute()) {
            $sucesso = 'Processo atualizado com sucesso!';
            // Atualizar dados locais para exibição
            $processo['numero_processo'] = $numero_processo;
            $processo['infratores'] = $infratores;
            $processo['vitimas'] = $vitimas;
            $processo['data_bo'] = $data_bo;
            $processo['data_entrada'] = $data_entrada;
            $processo['situacao'] = $situacao;
            $processo['facilitador'] = $facilitador;
            $processo['observacoes'] = $observacoes;
            $processo['status'] = $status;
            $processo['data_circulo'] = $data_circulo;
        } else {
            $erro = 'Erro ao atualizar processo: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Processo Circular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --gray: #95a5a6;
            --light-gray: #f8f9fa;
        }

        body {
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 30px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 15px 20px;
            border: none;
        }

        .form-container {
            padding: 25px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(26, 188, 156, 0.4);
        }

        .btn-back {
            background: var(--light-gray);
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="container">
            <a href="index.php" class="btn btn-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit me-2"></i>
                <span>Editar Processo Circular: <?= htmlspecialchars($processo['numero_processo']) ?></span>
            </div>

            <div class="form-container">
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <?php if ($sucesso): ?>
                    <div class="alert alert-success"><?= $sucesso ?></div>
                <?php endif; ?>

                <form method="POST" id="formProcesso">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_circulo" class="form-label">Data do Círculo</label>
                                <input type="datetime-local" class="form-control" id="data_circulo" name="data_circulo"
                                    value="<?= htmlspecialchars(isset($processo['data_circulo']) ? str_replace(' ', 'T', $processo['data_circulo']) : '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_processo" class="form-label">Número do Processo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_processo" name="numero_processo"
                                    value="<?= htmlspecialchars($processo['numero_processo']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="facilitador" class="form-label">Facilitador <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="facilitador" name="facilitador"
                                    value="<?= htmlspecialchars($processo['facilitador']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="infratores" class="form-label">Infratores <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="infratores" name="infratores" rows="3" required><?= htmlspecialchars($processo['infratores']) ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vitimas" class="form-label">Vítimas <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="vitimas" name="vitimas" rows="3" required><?= htmlspecialchars($processo['vitimas']) ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_bo" class="form-label">Data do BO <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_bo" name="data_bo"
                                    value="<?= htmlspecialchars($processo['data_bo']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_entrada" class="form-label">Data de Entrada <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_entrada" name="data_entrada"
                                    value="<?= htmlspecialchars($processo['data_entrada']) ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="situacao" class="form-label">Situação</label>
                                <select class="form-select" id="situacao" name="situacao">
                                    <option value="Solicitação" <?= $processo['situacao'] === 'Solicitação' ? 'selected' : '' ?>>Solicitação</option>
                                    <option value="Pré-círculo" <?= $processo['situacao'] === 'Pré-círculo' ? 'selected' : '' ?>>Pré-círculo</option>
                                    <option value="Círculo realizado" <?= $processo['situacao'] === 'Círculo realizado' ? 'selected' : '' ?>>Círculo realizado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Em andamento" <?= $processo['status'] === 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                                    <option value="Concluído" <?= $processo['status'] === 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                                    <option value="Cancelado" <?= $processo['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?= htmlspecialchars($processo['observacoes']) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save me-1"></i> Atualizar Processo
                            </button>
                        </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Processos Circulares</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>