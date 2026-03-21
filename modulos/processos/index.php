<?php
// processos_circulares/index.php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'recepcao', 'psicologa']);

$flashSuccess = getFlash('success');
$flashError = getFlash('error');

// Obter filtros da URL
$filtro_status = $_GET['filtro'] ?? 'todos';
$filtro_situacao = $_GET['situacao'] ?? 'todos';

// Construir a consulta SQL com base nos filtros
$sql = "SELECT * FROM processos_circulares_total WHERE 1=1";

// Aplicar filtros
if ($filtro_status !== 'todos') {
    $sql .= " AND status = '" . $conn->real_escape_string($filtro_status) . "'";
}

if ($filtro_situacao !== 'todos') {
    $sql .= " AND situacao = '" . $conn->real_escape_string($filtro_situacao) . "'";
}

$sql .= " ORDER BY data_entrada DESC";

$result = $conn->query($sql);

if ($result) {
    $processos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Erro na consulta: " . $conn->error);
}

// Consulta para estatísticas (todos os registros, sem filtros)
$sql_stats = "SELECT * FROM processos_circulares_total";
$result_stats = $conn->query($sql_stats);
$todos_processos = $result_stats->fetch_all(MYSQLI_ASSOC);

// Contagem por status
$stats = [
    'total' => count($todos_processos),
    'solicitacao' => 0,
    'pre_circulo' => 0,
    'circulo_realizado' => 0,
    'andamento' => 0,
    'concluido' => 0,
    'cancelado' => 0
];

foreach ($todos_processos as $processo) {
    if ($processo['situacao'] === 'Solicitação') $stats['solicitacao']++;
    if ($processo['situacao'] === 'Pré-círculo') $stats['pre_circulo']++;
    if ($processo['situacao'] === 'Círculo realizado') $stats['circulo_realizado']++;
    if ($processo['status'] === 'Em andamento') $stats['andamento']++;
    if ($processo['status'] === 'Concluído') $stats['concluido']++;
    if ($processo['status'] === 'Cancelado') $stats['cancelado']++;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Processos Circulares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/processos.css">
</head>

<body>
    <div class="header text-center">
        <div class="container position-relative">
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>

            <h1 class="page-title"><i class="fas fa-sync-alt me-2"></i>Gestão de Processos Circulares</h1>
            <p class="page-description">Cadastro e consulta de processos circulares</p>
        </div>
    </div>

    <div class="container py-4">
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($flashSuccess); ?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($flashError); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['total'] ?></div>
                    <div class="stats-label">Total de Processos</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['andamento'] ?></div>
                    <div class="stats-label">Em Andamento</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['concluido'] ?></div>
                    <div class="stats-label">Concluídos</div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h4><i class="fas fa-filter me-2"></i> Filtros</h4>
            <div class="d-flex flex-wrap">
                <!-- Filtros de Status -->
                <a href="?filtro=todos&situacao=<?= $filtro_situacao ?>"
                    class="filter-badge <?= $filtro_status === 'todos' ? 'active' : '' ?>">
                    <i class="fas fa-globe-americas me-2"></i> Todos
                </a>
                <a href="?filtro=Em andamento&situacao=<?= $filtro_situacao ?>"
                    class="filter-badge <?= $filtro_status === 'Em andamento' ? 'active' : '' ?>">
                    <i class="fas fa-hourglass-half me-2"></i> Em andamento
                </a>
                <a href="?filtro=Concluído&situacao=<?= $filtro_situacao ?>"
                    class="filter-badge <?= $filtro_status === 'Concluído' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle me-2"></i> Concluído
                </a>
                <a href="?filtro=Cancelado&situacao=<?= $filtro_situacao ?>"
                    class="filter-badge <?= $filtro_status === 'Cancelado' ? 'active' : '' ?>">
                    <i class="fas fa-ban me-2"></i> Cancelado
                </a>

                <!-- Separador visual -->
                <div class="w-100 my-2"></div>

                <!-- Filtros de Situação -->
                <a href="?filtro=<?= $filtro_status ?>&situacao=todos"
                    class="filter-badge <?= $filtro_situacao === 'todos' ? 'active' : '' ?>">
                    <i class="fas fa-list me-2"></i> Todas situações
                </a>
                <a href="?filtro=<?= $filtro_status ?>&situacao=Solicitação"
                    class="filter-badge <?= $filtro_situacao === 'Solicitação' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt me-2"></i> Solicitação
                </a>
                <a href="?filtro=<?= $filtro_status ?>&situacao=Pré-círculo"
                    class="filter-badge <?= $filtro_situacao === 'Pré-círculo' ? 'active' : '' ?>">
                    <i class="fas fa-clock me-2"></i> Pré-círculo
                </a>
                <a href="?filtro=<?= $filtro_status ?>&situacao=Círculo realizado"
                    class="filter-badge <?= $filtro_situacao === 'Círculo realizado' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle me-2"></i> Círculo realizado
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2"></i>
                    <span>Lista de Processos Circulares</span>
                    <?php if ($filtro_status !== 'todos' || $filtro_situacao !== 'todos'): ?>
                        <span class="badge bg-info ms-2">
                            Filtro:
                            <?= $filtro_status !== 'todos' ? "Status: $filtro_status" : '' ?>
                            <?= $filtro_situacao !== 'todos' ? "Situação: $filtro_situacao" : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="../agenda/index.php" class="btn btn-agendar-cal me-2">
                        <i class="fas fa-calendar-alt me-1"></i>Calendário
                    </a>
                    <a href="novo.php" class="btn btn-new">
                        <i class="fas fa-plus me-1"></i>Novo Processo
                    </a>
                </div>
            </div>

            <div class="table-container">
                <?php if (count($processos) > 0): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i> Processo</th>
                                <th><i class="fas fa-user-friends me-2"></i> Infratores</th>
                                <th><i class="fas fa-user-injured me-2"></i> Vítimas</th>
                                <th><i class="fas fa-calendar-day me-2"></i> Data BO</th>
                                <th><i class="fas fa-calendar-plus me-2"></i> Data Entrada</th>
                                <th><i class="fas fa-calendar-check me-2"></i> Pré - Círculo 1</th>
                                <th><i class="fas fa-calendar-check me-2"></i> Pré - Círculo 2</th>
                                <th><i class="fas fa-calendar-check me-2"></i> Data Círculo</th>
                                <th><i class="fas fa-calendar-check me-2"></i> Pós Círculo</th>
                                <label for="situacao" class="form-label">Situação</label>
                                <th><i class="fas fa-tasks me-2"></i> Situação</th>
                                <th><i class="fas fa-user-tie me-2"></i> Facilitador</th>
                                <th><i class="fas fa-user-tie me-2"></i> Co-Facilitador</th>
                                <th><i class="fas fa-info-circle me-2"></i> Status</th>
                                <th class="action-cell"><i class="fas fa-cogs me-2"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($processos as $processo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($processo['numero_processo']) ?></td>
                                    <td><?= htmlspecialchars($processo['infratores']) ?></td>
                                    <td><?= htmlspecialchars($processo['vitimas']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($processo['data_bo'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($processo['data_entrada'])) ?></td>
                                    <td>
                                        <?php if (!empty($processo['data_circulo'])): ?>
                                            <?= date('d/m/Y H:i', strtotime($processo['data_circulo'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Não agendado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge 
                                        <?= 'status-' . strtolower(str_replace(' ', '-', $processo['situacao'])) ?>">
                                            <?= $processo['situacao'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($processo['facilitador']) ?></td>
                                    <td>
                                        <span class="status-badge 
                                        <?= 'status-' . strtolower(str_replace(' ', '-', $processo['status'])) ?>">
                                            <?= $processo['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="editar.php?id=<?= $processo['id'] ?>"
                                                class="btn btn-warning btn-action"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <!-- Botão de agendamento -->
                                            <a href="../agenda/index.php?processo_id=<?= $processo['id'] ?>"
                                                class="btn btn-agendar btn-action"
                                                title="Agendar Círculo">
                                                <i class="fas fa-calendar-plus"></i>
                                            </a>

                                            <a href="excluir.php?id=<?= $processo['id'] ?>"
                                                class="btn btn-danger btn-action"
                                                title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir este processo?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3 class="text-muted mb-2">Nenhum processo encontrado</h3>
                        <p class="text-muted mb-4">
                            <?php if ($filtro_status !== 'todos' || $filtro_situacao !== 'todos'): ?>
                                Não foram encontrados processos com os filtros aplicados.
                            <?php else: ?>
                                Você ainda não possui processos cadastrados
                            <?php endif; ?>
                        </p>
                        <a href="novo.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>Criar Primeiro Processo
                        </a>
                        <?php if ($filtro_status !== 'todos' || $filtro_situacao !== 'todos'): ?>
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Processos Circulares</p>
            <p>Escritividade para obtenção da personalidade de pessoas internas</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Adicionar efeito de confirmação para exclusão
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-danger');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Tem certeza que deseja excluir este processo permanentemente?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>