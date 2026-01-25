<?php
// api/process_cart.php - Process cart items and separate products/services
require_once '../config/session.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    if (empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
        exit;
    }

    $produtos = [];
    $servicos = [];
    $total_produtos = 0;
    $total_servicos = 0;

    // Separate cart items into products and services
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['type'])) {
            if ($item['type'] === 'produto') {
                $produtos[] = [
                    'id' => $item['id'] ?? 0,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'] ?? 1,
                    'description' => $item['description'] ?? '',
                    'image' => $item['image'] ?? ''
                ];
                $total_produtos += $item['price'] * ($item['quantity'] ?? 1);
            } else if ($item['type'] === 'servico') {
                $servicos[] = [
                    'id' => $item['id'] ?? 0,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'description' => $item['description'] ?? '',
                    'image' => $item['image'] ?? ''
                ];
                $total_servicos += $item['price'];
            }
        }
    }

    // Create orders in database (simplified)
    $pedido_id = null;
    $agendamento_ids = [];

    // Process products as orders
    if (!empty($produtos)) {
        $pedido_data = [
            'usuario_id' => $_SESSION['usuario_id'],
            'items' => $produtos,
            'total' => $total_produtos,
            'status' => 'processando',
            'data_pedido' => date('Y-m-d H:i:s'),
            'payment_method' => 'pending'
        ];
        
        // In a real system, you would insert into orders table
        $pedido_id = 'ORD-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    // Process services as appointments
    if (!empty($servicos)) {
        foreach ($servicos as $servico) {
            $agendamento_data = [
                'usuario_id' => $_SESSION['usuario_id'],
                'servico_id' => $servico['id'],
                'servico_nome' => $servico['name'],
                'valor_orcamento' => $servico['price'],
                'status' => 'pendente',
                'data_agendamento' => date('Y-m-d H:i:s'),
                'descricao_problema' => $servico['description']
            ];
            
            // In a real system, you would insert into agendamentos table
            $agendamento_ids[] = 'AG-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    // Clear cart
    $_SESSION['cart'] = [];

    echo json_encode([
        'success' => true,
        'message' => 'Carrinho processado com sucesso',
        'pedido_id' => $pedido_id,
        'agendamento_ids' => $agendamento_ids,
        'produtos_count' => count($produtos),
        'servicos_count' => count($servicos),
        'redirect_produtos' => !empty($produtos) ? 'meusPedidos.php' : null,
        'redirect_servicos' => !empty($servicos) ? 'meusServicos.php' : null
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>  