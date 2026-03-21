<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin']);

$erro = '';
$sucesso = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = $_GET['id'];

// Buscar usuário
$stmt = $conn->prepare("SELECT * FROM usuarios_circulares WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header('Location: listar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $usuario_val = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $psicologa_nome = $_POST['psicologa_nome'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    // Validação básica
    if (empty($nome) || empty($usuario_val) || empty($tipo)) {
        $erro = 'Preencha todos os campos obrigatórios';
    } elseif ($tipo === 'psicologa' && empty($psicologa_nome)) {
        $erro = 'Para tipo Psicóloga, o nome da psicóloga é obrigatório';
    } else {
        // Montar query de atualização
        $params = [$nome, $usuario_val, $tipo, $psicologa_nome, $ativo, $id];
        $types = "sssssi";

        // Se senha foi preenchida, atualizar
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $query = "UPDATE usuarios SET nome = ?, usuario = ?, senha = ?, tipo = ?, psicologa_nome = ?, ativo = ? WHERE id = ?";
            $params = [$nome, $usuario_val, $senha_hash, $tipo, $psicologa_nome, $ativo, $id];
            $types = "ssssssi";
        } else {
            $query = "UPDATE usuarios SET nome = ?, usuario = ?, tipo = ?, psicologa_nome = ?, ativo = ? WHERE id = ?";
        }

        // Executar atualização
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $sucesso = 'Usuário atualizado com sucesso!';
            // Atualizar dados locais
            $usuario['nome'] = $nome;
            $usuario['usuario'] = $usuario_val;
            $usuario['tipo'] = $tipo;
            $usuario['psicologa_nome'] = $psicologa_nome;
            $usuario['ativo'] = $ativo;
        } else {
            $erro = 'Erro ao atualizar usuário: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos mantidos do novo.php */
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

        .psicologa-field {
            display: <?= $usuario['tipo'] === 'psicologa' ? 'block' : 'none' ?>;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="container">
            <a href="listar.php" class="btn btn-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit me-2"></i>
                <span>Editar Usuário: <?= htmlspecialchars($usuario['nome']) ?></span>
            </div>

            <div class="form-container">
                <?php if ($erro): ?>
                    <div class="alert alert-danger"><?= $erro ?></div>
                <?php endif; ?>

                <?php if ($sucesso): ?>
                    <div class="alert alert-success"><?= $sucesso ?></div>
                <?php endif; ?>

                <form method="POST" id="formUsuario">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome"
                            value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usuario" class="form-label">Usuário <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usuario" name="usuario"
                                value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="senha" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha"
                                placeholder="Deixe em branco para manter a atual">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="admin" <?= $usuario['tipo'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="recepcao" <?= $usuario['tipo'] === 'recepcao' ? 'selected' : '' ?>>Recepcionista</option>
                                <option value="psicologa" <?= $usuario['tipo'] === 'psicologa' ? 'selected' : '' ?>>Psicóloga</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo"
                                    <?= $usuario['ativo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="ativo">Usuário ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 psicologa-field" id="psicologaContainer">
                        <label for="psicologa_nome" class="form-label">Nome da Psicóloga <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="psicologa_nome" name="psicologa_nome"
                            value="<?= htmlspecialchars($usuario['psicologa_nome']) ?>">
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-save me-1"></i> Atualizar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Usuários</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const psicologaContainer = document.getElementById('psicologaContainer');

            // Mostrar/ocultar campo de nome da psicóloga
            function togglePsicologaField() {
                if (tipoSelect.value === 'psicologa') {
                    psicologaContainer.style.display = 'block';
                    document.getElementById('psicologa_nome').required = true;
                } else {
                    psicologaContainer.style.display = 'none';
                    document.getElementById('psicologa_nome').required = false;
                }
            }

            // Inicializar estado
            togglePsicologaField();

            // Adicionar listener para mudanças
            tipoSelect.addEventListener('change', togglePsicologaField);
        });
    </script>
</body>

</html>