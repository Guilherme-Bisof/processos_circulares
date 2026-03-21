<?php
session_start();


$usuario = $_SESSION['usuario'] ?? null;
$tipo = $_SESSION['tipo'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }
        .card i {
            font-size: 4rem;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <div class="card bg-dark text-white p-5">
            <i class="fas fa-lock mb-4"></i>
            <h2>Acesso Negado</h2>
            <p class="mt-3">
                Você não tem permissão para acessar esta página.<br>
                <?php if ($usuario): ?>
                    Usuário logado: <strong><?php echo htmlspecialchars($usuario); ?></strong> (<?php echo htmlspecialchars($tipo); ?>)
                <?php else: ?>
                    <span class="text-warning">Nenhum usuário logado.</span>
                <?php endif; ?>
            </p>
            <a href="login.php" class="btn btn-primary mt-3">
                <i class="fas fa-sign-in-alt"></i> Ir para Login
            </a>
        </div>
    </div>
</body>
</html>
