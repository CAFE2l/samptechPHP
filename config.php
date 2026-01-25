<?php
// config.php - Configurações globais

// Detectar ambiente automaticamente
$is_local = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
             strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

if ($is_local) {
    // Ambiente local (XAMPP)
    define('BASE_URL', 'http://localhost/samptech');
    define('BASE_PATH', '/samptech');
} else {
    // Ambiente de produção (Railway)
    define('BASE_URL', 'https://samptech-production.up.railway.app');
    define('BASE_PATH', '');
}

// Função helper para gerar URLs corretas
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

// Função para assets (CSS, JS, imagens)
function asset($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/assets/' . $path;
}
?>