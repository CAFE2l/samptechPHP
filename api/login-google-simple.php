<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();
require_once '../config.php';
require_once '../models/Usuario.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$user = $input['user'];
$email = $user['email'];
$nome = $user['displayName'];
$uid = $user['uid'];

try {
    $usuarioModel = new Usuario();
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo, ativo, data_cadastro) VALUES (?, ?, ?, 'cliente', 1, NOW())");
        $stmt->execute([$nome, $email, password_hash($uid, PASSWORD_DEFAULT)]);
        $usuario_id = $pdo->lastInsertId();
        
        $usuario = [
            'id' => $usuario_id,
            'nome' => $nome,
            'email' => $email,
            'tipo' => 'cliente'
        ];
    }
    
    // Set session
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_tipo'] = $usuario['tipo'];
    $_SESSION['usuario_telefone'] = $usuario['telefone'] ?? '';
    $_SESSION['usuario_endereco'] = $usuario['endereco'] ?? '';
    $_SESSION['usuario_logado'] = true;
    $_SESSION['login_time'] = time();
    
    echo json_encode(['success' => true, 'message' => 'Login realizado']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor']);
}
