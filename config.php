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

// Configuração do Banco de Dados
try {
    $host = 'localhost';
    $dbname = 'samptech';
    $username = 'root';
    $password = 'mysql';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>