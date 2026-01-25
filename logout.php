<?php
// logout.php
session_start();

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Se desejar destruir a sessão completamente, apague também o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir a sessão
session_destroy();

// Apagar cookie de autenticação se existir
if (isset($_COOKIE['samptech_auth'])) {
    setcookie('samptech_auth', '', time() - 3600, '/');
}

// Redirecionar para login
header('Location: pages/login.php?logout=sucesso');
exit();
?>