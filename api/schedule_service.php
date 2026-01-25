<?php
// api/schedule_service.php - Schedule service and save to database
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../models/Pedido.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['service_name']) || !isset($input['price'])) {
    echo json_encode(['success' => false, 'message' => 'Dados do serviço incompletos']);
    exit;
}

try {
    $pedidoModel = new Pedido();
    
    // Create service order data
    $orderData = [
        'usuario_id' => $_SESSION['usuario_id'],
        'total' => $input['price'],
        'status' => 'processando',
        'metodo_pagamento' => 'A definir',
        'endereco_entrega' => $input['address'] ?? 'Serviço domiciliar',
        'items' => [
            [
                'name' => $input['service_name'],
                'quantity' => 1,
                'price' => $input['price'],
                'type' => 'servico'
            ]
        ]
    ];
    
    $result = $pedidoModel->criar($orderData);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Serviço agendado com sucesso!',
            'pedido_id' => $result['pedido_id']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => $result['message']]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}
?>