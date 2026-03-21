<?php
require_once __DIR__ . '/../../core/conexao.php';
require_once __DIR__ . '/../../core/auth.php';
permitir(['admin']);

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $facilitador_nome = $_POST['facilitador_nome'] ?? '';
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    // Validação básica
    if (empty($nome) || empty($usuario) || empty($senha) || empty($tipo)) {
        $erro = 'Preencha todos os campos obrigatórios';
    } elseif ($tipo === 'facilitador' && empty($facilitador_nome)) {
        $erro = 'Para tipo facilitador, o nome do(a) facilitador(a) é obrigatório';
    } else {
        // Criptografar senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir no banco
        $stmt = $conn->prepare("INSERT INTO usuarios_circulares (nome, usuario, senha, tipo, facilitador_nome, ativo) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $nome, $usuario, $senha_hash, $tipo, $facilitador_nome, $ativo);

        if ($stmt->execute()) {
            $sucesso = 'Usuário cadastrado com sucesso!';
            // Limpar formulário após sucesso
            $_POST = [];
        } else {
            $erro = 'Erro ao cadastrar usuário: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos mantidos do arquivo principal */
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

        .facilitador-field {
            display: none;
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
                <i class="fas fa-user-plus me-2"></i>
                <span>Cadastrar Novo Usuário</span>
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
                            value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usuario" class="form-label">Usuário <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="usuario" name="usuario"
                                value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="senha" class="form-label">Senha <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione o tipo</option>
                                <option value="admin" <?= ($_POST['tipo'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="recepcao" <?= ($_POST['tipo'] ?? '') === 'recepcao' ? 'selected' : '' ?>>Recepcionista</option>
                                <option value="facilitador" <?= ($_POST['tipo'] ?? '') === 'facilitador' ? 'selected' : '' ?>>Facilitador(a)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo"
                                    <?= isset($_POST['ativo']) ? 'checked' : 'checked' ?>>
                                <label class="form-check-label" for="ativo">Usuário ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 facilitador-field" id="facilitadorContainer">
                        <label for="facilitador_nome" class="form-label">Nome do(a) facilitador(a) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="facilitador_nome" name="facilitador_nome"
                            value="<?= htmlspecialchars($_POST['facilitador_nome'] ?? '') ?>">
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-save me-1"></i> Salvar Usuário
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
            const facilitadorContainer = document.getElementById('facilitadorContainer');

            // Mostrar/ocultar campo de nome da psicóloga
            function togglefacilitadorField() {
                if (tipoSelect.value === 'facilitador') {
                    facilitadorContainer.style.display = 'block';
                    document.getElementById('facilitador_nome').required = true;
                } else {
                    facilitadorContainer.style.display = 'none';
                    document.getElementById('facilitador_nome').required = false;
                }
            }

            // Inicializar estado
            togglefacilitadorField();

            // Adicionar listener para mudanças
            tipoSelect.addEventListener('change', togglefacilitadorField);
        });
    </script>
</body>

</html>