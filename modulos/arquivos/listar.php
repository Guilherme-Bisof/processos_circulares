<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin', 'facilitador']);

// Obter filtros da URL
$filtro_status = $_GET['status'] ?? 'todos';
$filtro_facilitador = $_GET['facilitador'] ?? 'todos';

// Construir a consulta SQL com base nos filtros
$sql = "SELECT * FROM processos_circulares_arquivamentos WHERE 1=1";

// Aplicar filtros
if ($filtro_status !== 'todos') {
    if ($filtro_status === 'com_relatorio') {
        $sql .= " AND arquivo_relatorio IS NOT NULL AND arquivo_relatorio != ''";
    } elseif ($filtro_status === 'pendentes') {
        $sql .= " AND (arquivo_relatorio IS NULL OR arquivo_relatorio = '')";
    }
}

if ($filtro_facilitador !== 'todos') {
    $sql .= " AND facilitador = '" . $conn->real_escape_string($filtro_facilitador) . "'";
}

$sql .= " ORDER BY envio_relatorio DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$arquivamentos = $result->fetch_all(MYSQLI_ASSOC);

// Consulta para estatísticas (todos os registros)
$sql_stats = "SELECT * FROM processos_circulares_arquivamentos";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->execute();
$result_stats = $stmt_stats->get_result();
$todos_arquivamentos = $result_stats->fetch_all(MYSQLI_ASSOC);

// Contagem para estatísticas
$stats = [
    'total' => count($todos_arquivamentos),
    'com_relatorio' => 0,
    'pendentes' => 0,
    'este_mes' => 0
];

$mes_atual = date('Y-m');
foreach ($todos_arquivamentos as $arq) {
    // Com relatório
    if (!empty($arq['arquivo_relatorio'])) {
        $stats['com_relatorio']++;
    } else {
        $stats['pendentes']++;
    }
    
    // Este mês
    if ($arq['envio_relatorio'] && date('Y-m', strtotime($arq['envio_relatorio'])) === $mes_atual) {
        $stats['este_mes']++;
    }
}

// Obter lista de facilitadores únicos
$sql_facilitadores = "SELECT DISTINCT facilitador FROM processos_circulares_arquivamentos WHERE facilitador IS NOT NULL AND facilitador != '' ORDER BY facilitador";
$result_facilitadores = $conn->query($sql_facilitadores);
$facilitadores = $result_facilitadores->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arquivamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/arquivamentos.css">
</head>

<body>
    <div class="header text-center">
        <div class="container position-relative">
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>

            <h1 class="page-title"><i class="fas fa-archive me-2"></i>Arquivamentos</h1>
            <p class="page-description">Gerenciamento de documentos arquivados</p>
        </div>
    </div>

    <div class="container py-4">
        <!-- Cards de estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['total'] ?></div>
                    <div class="stats-label">Total de Arquivamentos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['com_relatorio'] ?></div>
                    <div class="stats-label">Relatórios Enviados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['pendentes'] ?></div>
                    <div class="stats-label">Pendentes</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['este_mes'] ?></div>
                    <div class="stats-label">Este Mês</div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="mb-4">
            <h4><i class="fas fa-filter me-2"></i> Filtros</h4>
            <div class="d-flex flex-wrap">
                <!-- Filtros por status -->
                <a href="?status=todos&facilitador=<?= $filtro_facilitador ?>"
                    class="filter-badge <?= $filtro_status === 'todos' ? 'active' : '' ?>">
                    <i class="fas fa-globe-americas me-2"></i> Todos
                </a>
                <a href="?status=com_relatorio&facilitador=<?= $filtro_facilitador ?>"
                    class="filter-badge <?= $filtro_status === 'com_relatorio' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle me-2"></i> Com Relatório
                </a>
                <a href="?status=pendentes&facilitador=<?= $filtro_facilitador ?>"
                    class="filter-badge <?= $filtro_status === 'pendentes' ? 'active' : '' ?>">
                    <i class="fas fa-clock me-2"></i> Pendentes
                </a>

                <!-- Separador visual -->
                <div class="w-100 my-2"></div>

                <!-- Filtros por facilitador -->
                <a href="?status=<?= $filtro_status ?>&facilitador=todos"
                    class="filter-badge <?= $filtro_facilitador === 'todos' ? 'active' : '' ?>">
                    <i class="fas fa-user-tie me-2"></i> Todos Facilitadores
                </a>
                <?php foreach ($facilitadores as $facilitador): ?>
                    <a href="?status=<?= $filtro_status ?>&facilitador=<?= urlencode($facilitador['facilitador']) ?>"
                        class="filter-badge <?= $filtro_facilitador === $facilitador['facilitador'] ? 'active' : '' ?>">
                        <i class="fas fa-user me-2"></i> <?= htmlspecialchars($facilitador['facilitador']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tabela principal -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2"></i>
                    <span>Lista de Arquivamentos</span>
                    <?php if ($filtro_status !== 'todos' || $filtro_facilitador !== 'todos'): ?>
                        <span class="badge bg-info ms-2">
                            Filtro:
                            <?php if ($filtro_status !== 'todos'): ?>
                                <?= $filtro_status === 'com_relatorio' ? 'Com Relatório' : ($filtro_status === 'pendentes' ? 'Pendentes' : $filtro_status) ?>
                            <?php endif; ?>
                            <?php if ($filtro_facilitador !== 'todos'): ?>
                                <?= $filtro_status !== 'todos' ? ' | ' : '' ?>Facilitador: <?= htmlspecialchars($filtro_facilitador) ?>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
                <a href="novo.php" class="btn btn-new">
                    <i class="fas fa-plus me-1"></i>Novo Arquivamento
                </a>
            </div>

            <div class="table-container">
                <?php if (count($arquivamentos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Nome</th>
                                    <th><i class="fas fa-calendar me-2"></i>Data Nascimento</th>
                                    <th><i class="fas fa-user-tie me-2"></i>Facilitador</th>
                                    <th><i class="fas fa-building me-2"></i>Órgão Origem</th>
                                    <th><i class="fas fa-paper-plane me-2"></i>Envio Relatório</th>
                                    <th><i class="fas fa-file me-2"></i>Arquivo</th>
                                    <th><i class="fas fa-cogs me-2"></i>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arquivamentos as $arq): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($arq['nome_completo']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($arq['data_nascimento'])) ?></td>
                                        <td><?= htmlspecialchars($arq['facilitador']) ?></td>
                                        <td><?= htmlspecialchars($arq['orgao_origem']) ?></td>
                                        <td>
                                            <?php if ($arq['envio_relatorio']): ?>
                                                <span class="status-badge status-ativo">
                                                    <?= date('d/m/Y', strtotime($arq['envio_relatorio'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-pendente">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($arq['arquivo_relatorio']): ?>
                                                <a href="../uploads/<?= $arq['arquivo_relatorio'] ?>" 
                                                   target="_blank" class="file-link">
                                                    <i class="fas fa-file-pdf me-1"></i> Ver
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-file-times me-1"></i> Nenhum
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="editar.php?id=<?= $arq['id'] ?>" 
                                                   class="btn btn-sm btn-warning action-btn" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="processar.php?action=delete&id=<?= $arq['id'] ?>"
                                                   class="btn btn-sm btn-danger action-btn"
                                                   title="Excluir"
                                                   onclick="return confirm('Tem certeza que deseja excluir este arquivamento permanentemente?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3 class="text-muted mb-2">Nenhum arquivamento encontrado</h3>
                        <p class="text-muted mb-4">
                            <?php if ($filtro_status !== 'todos' || $filtro_facilitador !== 'todos'): ?>
                                Não foram encontrados arquivamentos com os filtros aplicados.
                            <?php else: ?>
                                Você ainda não possui arquivamentos cadastrados.
                            <?php endif; ?>
                        </p>
                        <a href="novo.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>Criar Primeiro Arquivamento
                        </a>
                        <?php if ($filtro_status !== 'todos' || $filtro_facilitador !== 'todos'): ?>
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Rodapé -->
        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Arquivamentos</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Adicionar efeito de confirmação para exclusão
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-danger');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Tem certeza que deseja excluir este arquivamento permanentemente?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>