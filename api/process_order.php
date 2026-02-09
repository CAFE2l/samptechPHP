<?php
session_start();
require_once '../config.php';
require_once '../models/Pedido.php';
require_once '../models/Servico.php';

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

if (!$input || !isset($input['items']) || empty($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'Itens não fornecidos']);
    exit;
}

try {
    $usuario_id = $_SESSION['usuario_id'];
    $items = $input['items'];
    $total = 0;
    
    // Calculate total and separate products from services
    $produtos = [];
    $servicos = [];
    
    foreach ($items as $item) {
        $total += $item['price'] * ($item['quantity'] ?? 1);
        
        if (($item['type'] ?? 'produto') === 'servico') {
            $servicos[] = $item;
        } else {
            $produtos[] = $item;
        }
    }
    
    $pedidoModel = new Pedido();
    $servicoModel = new Servico();
    
    $results = [];
    
    // Create product order if there are products
    if (!empty($produtos)) {
        $pedidoData = [
            'usuario_id' => $usuario_id,
            'items' => json_encode($produtos),
            'total' => array_sum(array_map(fn($p) => $p['price'] * ($p['quantity'] ?? 1), $produtos)),
            'status' => 'pendente',
            'metodo_pagamento' => $input['payment_method'] ?? 'A definir',
            'endereco_entrega' => $input['address'] ?? 'Endereço do usuário'
        ];
        
        $pedidoResult = $pedidoModel->criar($pedidoData);
        if ($pedidoResult['success']) {
            $results['pedido'] = $pedidoResult['id'];
        }
    }
    
    // Create service orders (one for each service)
    foreach ($servicos as $servico) {
        $servicoData = [
            'usuario_id' => $usuario_id,
            'tipo_servico' => $servico['name'],
            'descricao' => $servico['description'] ?? 'Serviço solicitado via carrinho',
            'preco' => $servico['price'],
            'status' => 'agendado'
        ];
        
        $servicoResult = $servicoModel->criar($servicoData);
        if ($servicoResult['success']) {
            $results['servicos'][] = $servicoResult['id'];
        }
    }
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    echo json_encode([
        'success' => true, 
        'message' => 'Pedido processado com sucesso',
        'results' => $results
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao processar pedido: ' . $e->getMessage()]);
}
?>
