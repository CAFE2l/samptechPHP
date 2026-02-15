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

$data = json_decode(file_get_contents('php://input'), true);
$pedido_id = $data['pedido_id'] ?? 0;

try {
    // Get order details
    $stmt = $pdo->prepare("SELECT p.*, u.nome, u.telefone FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ? AND p.usuario_id = ?");
    $stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$pedido) {
        echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
        exit();
    }
    
    if(!in_array($pedido['status'], ['processando', 'pendente'])) {
        echo json_encode(['success' => false, 'message' => 'Pedido não pode ser cancelado']);
        exit();
    }
    
    // Get order items
    $stmt = $pdo->prepare("SELECT ip.*, p.nome as produto_nome FROM itens_pedido ip JOIN produtos p ON ip.produto_id = p.id WHERE ip.pedido_id = ?");
    $stmt->execute([$pedido_id]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Restore stock
    foreach($itens as $item) {
        $stmt = $pdo->prepare("UPDATE produtos SET estoque = estoque + ? WHERE id = ?");
        $stmt->execute([$item['quantidade'], $item['produto_id']]);
    }
    
    // Build items list for WhatsApp
    $itens_texto = '';
    foreach($itens as $item) {
        $itens_texto .= "%0A- " . $item['produto_nome'] . " (" . $item['quantidade'] . "x R$ " . number_format($item['preco_unitario'], 2, ',', '.') . ")";
    }
    
    // Delete order
    $stmt = $pdo->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->execute([$pedido_id]);
    
    // Send WhatsApp notification to admin
    $admin_telefone = '5564992800407';
    $mensagem = "🚨 PEDIDO CANCELADO%0A%0APedido: #" . $pedido_id . "%0ACliente: " . $pedido['nome'] . "%0AValor Total: R$ " . number_format($pedido['total'], 2, ',', '.') . "%0A%0AProdutos:" . $itens_texto . "%0A%0AEstoque restaurado automaticamente.";
    $whatsapp_url = "https://wa.me/" . $admin_telefone . "?text=" . $mensagem;
    
    echo json_encode(['success' => true, 'whatsapp_url' => $whatsapp_url]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao cancelar pedido']);
}
