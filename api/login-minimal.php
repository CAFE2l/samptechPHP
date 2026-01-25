<?php
// Minimal Firebase login - no external dependencies
session_start();

// Force JSON header
header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Capture all output
ob_start();

try {
    // Get input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['token'])) {
        throw new Exception('Token não fornecido');
    }
    
    // Mock successful login for testing
    $_SESSION['usuario_id'] = 1;
    $_SESSION['usuario_nome'] = 'Usuário Google Test';
    $_SESSION['usuario_email'] = 'test@gmail.com';
    $_SESSION['usuario_tipo'] = 'cliente';
    $_SESSION['usuario_logado'] = true;
    $_SESSION['login_time'] = time();
    
    // Clear any output buffer
    ob_clean();
    
    // Return success
    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso',
        'user' => [
            'id' => $_SESSION['usuario_id'],
            'nome' => $_SESSION['usuario_nome'],
            'email' => $_SESSION['usuario_email']
        ]
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