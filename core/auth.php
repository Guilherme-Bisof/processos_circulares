<?php
session_start();

// Se não tiver login, manda para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

function permitir($tiposPermitidos)
{
    if (!isset($_SESSION['usuario_tipo']) || !in_array($_SESSION['usuario_tipo'], (array)$tiposPermitidos)) {
        header("Location: ../core/acesso_negado.php");
        exit();
    }
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string
{
    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $message = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    return $message;
}

