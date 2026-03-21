<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin']);

// Consulta para listar usuários
$sql = "SELECT * FROM usuarios_circulares";
$result = $conn->query($sql);

if ($result) {
    $usuarios = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Erro na consulta: " . $conn->error);
}

// Contagem por tipo de usuário
$stats = [
    'total' => count($usuarios),
    'admin' => 0,
    'recepcao' => 0,
    'facilitador' => 0
];

foreach ($usuarios as $usuario) {
    $stats[$usuario['tipo']]++;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -30px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 20px 25px;
            border: none;
        }

        .table-container {
            overflow: hidden;
            border-radius: 0 0 12px 12px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--light-gray);
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #dee2e6;
            padding: 16px 20px;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        .table tbody td {
            padding: 14px 20px;
            vertical-align: middle;
            border-top: 1px solid #edf2f7;
        }

        .btn-new {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(26, 188, 156, 0.4);
            color: white;
        }

        .btn-back {
            position: absolute;
            left: 20px;
            top: 20px;
            z-index: 10;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-action i {
            font-size: 0.9rem;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            background-color: white;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #e9ecef;
            margin-bottom: 20px;
        }

        .status-ativo {
            background-color: #e8f5e9;
            color: #388e3c;
            border-left: 4px solid #4caf50;
        }

        .status-inativo {
            background-color: #ffebee;
            color: #d32f2f;
            border-left: 4px solid #f44336;
        }

        .action-cell {
            min-width: 150px;
        }

        .page-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .page-description {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .filter-badge {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 5px 15px;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .filter-badge.active {
            background-color: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .badge-tipo {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-admin {
            background-color: #d1c4e9;
            color: #4527a0;
        }

        .badge-recepcao_agenda {
            background-color: #bbdefb;
            color: #1565c0;
        }

        .badge-recepcao_entrada {
            background-color: #ffecb3;
            color: #f57f17;
        }

        .badge-psicologa {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }

            .btn-back {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="header text-center">
        <div class="container position-relative">
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>

            <h1 class="page-title"><i class="fas fa-users me-2"></i>Gestão de Usuários</h1>
            <p class="page-description">Cadastro e administração de usuários do sistema</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['total'] ?></div>
                    <div class="stats-label">Total de Usuários</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['admin'] ?></div>
                    <div class="stats-label">Administradores</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['recepcao'] ?></div>
                    <div class="stats-label">Recepcionistas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['facilitador'] ?></div>
                    <div class="stats-label">Facilitadores</div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h4><i class="fas fa-filter me-2"></i> Filtros</h4>
            <div class="d-flex flex-wrap">
                <div class="filter-badge active">
                    <i class="fas fa-users me-2"></i> Todos os Usuários
                </div>
                <div class="filter-badge">
                    <i class="fas fa-user-shield me-2"></i> Administradores
                </div>
                <div class="filter-badge">
                    <i class="fas fa-user-clock me-2"></i> Recepcionistas
                </div>
                <div class="filter-badge">
                    <i class="fas fa-user-md me-2"></i> Facilitadores
                </div>
                <div class="filter-badge active">
                    <i class="fas fa-user-check me-2"></i> Ativos
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2"></i>
                    <span>Lista de Usuários</span>
                </div>
                <a href="novo.php" class="btn btn-new">
                    <i class="fas fa-plus me-1"></i>Novo Usuário
                </a>
            </div>

            <div class="table-container">
                <?php if (count($usuarios) > 0): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-user me-2"></i> Nome</th>
                                <th><i class="fas fa-user-tag me-2"></i> Usuário</th>
                                <th><i class="fas fa-tag me-2"></i> Tipo</th>
                                <th><i class="fas fa-user-md me-2"></i> Facilitadores</th>
                                <th><i class="fas fa-toggle-on me-2"></i> Status</th>
                                <th class="action-cell"><i class="fas fa-cogs me-2"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario):
                                $tipoClass = 'badge-' . $usuario['tipo'];

                                // Mapear o tipo para exibição amigável
                                $tiposExibicao = [
                                    'admin' => 'Administrador',
                                    'recepcao' => 'Recepcionista',
                                    'facilitador' => 'Facilitador(a)'
                                ];
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                    <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                                    <td>
                                        <span class="badge-tipo <?= $tipoClass ?>">
                                            <?= $tiposExibicao[$usuario['tipo']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $usuario['tipo'] === 'facilitador' ?
                                            htmlspecialchars($usuario['facilitador_nome']) : '-' ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $usuario['ativo'] ? 'status-ativo' : 'status-inativo' ?>">
                                            <?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="editar.php?id=<?= $usuario['id'] ?>"
                                                class="btn btn-warning btn-action"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="excluir.php?id=<?= $usuario['id'] ?>"
                                                class="btn btn-danger btn-action"
                                                title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
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
                        <i class="fas fa-user-slash"></i>
                        <h3 class="text-muted mb-2">Nenhum usuário cadastrado</h3>
                        <p class="text-muted mb-4">Comece cadastrando seu primeiro usuário</p>
                        <a href="novo.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Cadastrar Usuário
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Usuários</p>
            <p>Controle de acesso e permissões</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-danger');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Tem certeza que deseja excluir este usuário permanentemente?')) {
                        e.preventDefault();
                    }
                });
            });

            // Filtros
            const filterBadges = document.querySelectorAll('.filter-badge');
            filterBadges.forEach(badge => {
                badge.addEventListener('click', function() {
                    this.classList.toggle('active');
                    // Implementação real de filtragem iria aqui
                });
            });
        });
    </script>
</body>

</html>