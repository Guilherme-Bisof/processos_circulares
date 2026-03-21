<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'facilitador']); 

if (!isset($_GET['id'])) {
    header('Location: listar.php');
    exit;
}


$sqlFacilitadores = "SELECT id, CASE WHEN facilitador_nome is NOT NULL and facilitador_nome != '' THEN facilitador_nome ELSE nome END AS nome_exibicao FROM usuarios_circulares WHERE tipo = 'facilitador' AND ativo = 1";
$resultFacilitadores = $conn->query($sqlFacilitadores);
$facilitadores = $resultFacilitadores->fetch_all(MYSQLI_ASSOC);

$id = $_GET['id'];
$sql = "SELECT * FROM processos_circulares_arquivamentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$arquivamento = $result->fetch_assoc();

if (!$arquivamento) {
    header('Location: listar.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Arquivamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px 0;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="header text-center mb-4">
        <div class="container">
            <h1><i class="fas fa-edit me-2"></i>Editar Arquivamento</h1>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Editar Dados do Arquivamento</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $arquivamento['id'] ?>">

                            <div class="mb-3">
                                <label for="nome_completo" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo"
                                    value="<?= htmlspecialchars($arquivamento['nome_completo']) ?>" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                        value="<?= $arquivamento['data_nascimento'] ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="facilitador" class="form-label">Facilitador(a) Responsável</label>
                                    <select class="form-select" id="facilitador" name="facilitador" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($facilitadores as $fac): ?>
                                            <option value="<?= htmlspecialchars($fac['nome_exibicao']) ?>" 
                                                <?= $fac['nome_exibicao'] === $arquivamento['facilitador'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($fac['nome_exibicao']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="orgao_origem" class="form-label">Órgão de Origem</label>
                                    <select class="form-select" id="orgao_origem" name="orgao_origem" required>
                                        <option value="">Selecione...</option>
                                        <?php
                                        $orgaos = ['DDM', 'Conselho Tutelar'];
                                        foreach ($orgaos as $orgao) {
                                            $selected = $orgao === $arquivamento['orgao_origem'] ? 'selected' : '';
                                            echo "<option value='$orgao' $selected>$orgao</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="envio_relatorio" class="form-label">Data de Envio do Relatório</label>
                                    <input type="date" class="form-control" id="envio_relatorio" name="envio_relatorio"
                                        value="<?= $arquivamento['envio_relatorio'] ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="arquivo_relatorio" class="form-label">Arquivo do Relatório (PDF)</label>
                                <input type="file" class="form-control" id="arquivo_relatorio" name="arquivo_relatorio" accept=".pdf">

                                <?php if ($arquivamento['arquivo_relatorio']): ?>
                                    <div class="mt-2">
                                        <i class="fas fa-file-pdf text-danger me-1"></i>
                                        <a href="../uploads/<?= $arquivamento['arquivo_relatorio'] ?>" target="_blank">
                                            Visualizar arquivo atual
                                        </a>
                                    </div>
                                    <input type="hidden" name="arquivo_atual" value="<?= $arquivamento['arquivo_relatorio'] ?>">
                                <?php endif; ?>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="listar.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Voltar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Atualizar Arquivamento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>