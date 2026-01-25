<?php
// config/session.php

// Configurar sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de segurança para sessão
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    session_start();
}

// Verificar se usuário está autenticado
function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

// Atualizar dados do usuário na sessão
function atualizarSessaoUsuario($userId) {
    if (estaLogado()) {
        require_once __DIR__ . '/database.php';
        require_once __DIR__ . '/../models/Usuario.php';
        $usuarioModel = new Usuario();
        $userData = $usuarioModel->buscarPorId($userId);
        if ($userData) {
            $_SESSION['usuario_foto'] = $userData['foto_perfil'] ?? '';
        }
    }
}

// Verificar tipo de usuário
function eAdmin() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
}

function eTecnico() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'tecnico';
}

function eCliente() {
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'cliente';
}

// Redirecionar se não estiver logado
function requerLogin($tipoRequerido = null) {
    if (!estaLogado()) {
        $_SESSION['mensagem_erro'] = "Você precisa estar logado para acessar esta página.";
        header('Location: ../pages/login.php');
        exit();
    }
    
    if ($tipoRequerido && $_SESSION['tipo'] !== $tipoRequerido) {
        $_SESSION['mensagem_erro'] = "Você não tem permissão para acessar esta área.";
        header('Location: ../index.php');
        exit();
    }
}

// Destruir sessão (logout)
function logout() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}
?>