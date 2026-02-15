<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit();
}

$cart = $_SESSION['cart'] ?? [];
if(empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$total = array_sum(array_map(fn($item) => $item['preco'] * $item['quantidade'], $cart));

try {
    $pdo->beginTransaction();
    
    // Create order
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, status, data_pedido) VALUES (?, ?, 'processando', NOW())");
    $stmt->execute([$_SESSION['usuario_id'], $total]);
    $pedido_id = $pdo->lastInsertId();
    
    // Add items
    foreach($cart as $item) {
        $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        $stmt->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $item['preco']]);
        
        // Update stock
        $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
        $stmt->execute([$item['quantidade'], $item['produto_id']]);
    }
    
    $pdo->commit();
    
    // Clear cart
    unset($_SESSION['cart']);
    
    echo json_encode(['success' => true, 'pedido_id' => $pedido_id]);
} catch(PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erro ao processar']);
}
