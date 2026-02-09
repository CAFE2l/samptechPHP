<?php
session_start();
require_once '../config.php';
require_once '../models/Pedido.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do pedido não fornecido']);
    exit;
}

try {
    $pedidoModel = new Pedido();
    $pedido = $pedidoModel->buscarPorId($input['order_id']);
    
    if (!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit;
    }
    
    if ($pedido['usuario_id'] != $_SESSION['usuario_id']) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }
    
    if ($pedido['status'] !== 'pendente') {
        echo json_encode(['success' => false, 'message' => 'Pedido não pode ser cancelado']);
        exit;
    }
    
    if ($pedidoModel->atualizarStatus($input['order_id'], 'cancelado')) {
        echo json_encode(['success' => true, 'message' => 'Pedido cancelado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar pedido']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>
