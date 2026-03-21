<?php
include './core/auth.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel - Justiça Restaurativa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/painel.css">
</head>

<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="logo-container">
                <div class="logo">
                    <img src="./assets/img/logo.png" alt="Logo Justiça Restaurativa">
                </div>
                <div>
                    <h1 class="header-title">Painel Administrativo</h1>
                    <p class="header-subtitle">Sistema de Processos Circulares</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome -->
    <div class="container">
        <div class="welcome-card">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr(htmlspecialchars($_SESSION['usuario_nome']), 0, 1)); ?>
                </div>
                <div class="user-details">
                    <h2 class="user-greeting">Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h2>
                    <span class="user-role"><?php echo htmlspecialchars($_SESSION['usuario_tipo']); ?></span>
                </div>
            </div>
        </div>

        <!-- Dashboard -->
        <div class="dashboard-wrapper">
            <div class="dashboard-grid">
                <a href="modulos/agenda/index.php" class="dashboard-card">
                    <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="card-content">
                        <h3 class="card-title">Agenda</h3>
                        <p class="card-desc">Agendamentos e acompanhamento</p>
                    </div>
                </a>

                <!-- <a href="modulos/arquivos/listar.php" class="dashboard-card">
                    <div class="card-icon" style="background: linear-gradient(135deg, #718096, #a0aec0);">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Arquivamentos</h3>
                        <p class="card-desc">Documentos e histórico</p>
                    </div>
                </a> !-->

                <a href="modulos/oficios/listar.php" class="dashboard-card">
                    <div class="card-icon" style="background: linear-gradient(135deg, #dd6b20, #ed8936);">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Ofícios</h3>
                        <p class="card-desc">Gerenciamento de ofícios</p>
                    </div>
                </a>

                <a href="modulos/processos/index.php" class="dashboard-card">
                    <div class="card-icon" style="background: linear-gradient(135deg, #00b5d8, #0bc5ea);">
                        <i class="fas fa-circle-nodes"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">Processos Circulares</h3>
                        <p class="card-desc">Gestão dos círculos restaurativos</p>
                    </div>
                </a>

                <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                    <a href="modulos/usuarios/listar.php" class="dashboard-card">
                        <div class="card-icon" style="background: linear-gradient(135deg, #9f7aea, #b794f4);">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">Gerenciar Usuários</h3>
                            <p class="card-desc">Controle de acesso do sistema</p>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="logout-container">
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="container">
            <p>Sistema de Justiça Restaurativa &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</body>

</html>