<?php
header('Content-Type: application/json');
ob_start();

try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/Usuario.php';
    require_once __DIR__ . '/../config/session.php';
    
    $input = json_decode(file_get_contents('php://input'), true);
    $token = $input['token'] ?? null;
    
    if (!$token) {
        throw new Exception('Token não fornecido');
    }
    
    // For now, extract email from token (simplified)
    // In production, you'd verify the Firebase token properly
    $email = 'user@gmail.com'; // This should come from Firebase token verification
    $name = 'Usuário Google';
    
    $usuarioModel = new Usuario();
    
    // Check if user exists
    $usuario = $usuarioModel->checkEmail($email);
    
    if (!$usuario) {
        // Create new Google user
        $resultado = $usuarioModel->criar([
            'nome' => $name,
            'email' => $email,
            'senha' => password_hash(uniqid(), PASSWORD_DEFAULT), // Random password for Google users
            'telefone' => '',
            'cpf' => '',
            'endereco' => '',
            'bairro' => '',
            'cidade' => '',
            'estado' => '',
            'cep' => '',
            'tipo' => 'cliente'
        ]);
        
        if ($resultado['success']) {
            $usuario = $usuarioModel->buscarPorId($resultado['id']);
        } else {
            throw new Exception($resultado['message']);
        }
    }
    
    // Set session
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_tipo'] = $usuario['tipo'];
    $_SESSION['usuario_logado'] = true;
    $_SESSION['login_time'] = time();
    
    ob_clean();
    
    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso'
    ]);
    
} catch (Exception $e) {
    ob_clean();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

ob_end_flush();
exit;
?>