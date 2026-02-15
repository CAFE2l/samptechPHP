<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if(!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faça login para comprar']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$produto_id = $data['produto_id'] ?? 0;
$quantidade = $data['quantidade'] ?? 1;

try {
    // Get product
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = 1");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$produto) {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
        exit();
    }
    
    if($produto['estoque'] < $quantidade) {
        echo json_encode(['success' => false, 'message' => 'Estoque insuficiente']);
        exit();
    }
    
    $total = $produto['preco'] * $quantidade;
    
    // Create order
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, status, data_pedido) VALUES (?, ?, 'processando', NOW())");
    $stmt->execute([$_SESSION['usuario_id'], $total]);
    $pedido_id = $pdo->lastInsertId();
    
    // Add item to order
    $stmt = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
    $stmt->execute([$pedido_id, $produto_id, $quantidade, $produto['preco']]);
    
    // Update stock
    $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
    $stmt->execute([$quantidade, $produto_id]);
    
    echo json_encode(['success' => true, 'pedido_id' => $pedido_id]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao processar compra']);
}
