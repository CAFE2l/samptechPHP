<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$produto_id = $data['produto_id'] ?? 0;
$nome = $data['nome'] ?? '';
$preco = $data['preco'] ?? 0;
$quantidade = $data['quantidade'] ?? 1;

if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$found = false;
foreach($_SESSION['cart'] as &$item) {
    if($item['produto_id'] == $produto_id) {
        $item['quantidade'] += $quantidade;
        $found = true;
        break;
    }
}

if(!$found) {
    $_SESSION['cart'][] = [
        'produto_id' => $produto_id,
        'nome' => $nome,
        'preco' => $preco,
        'quantidade' => $quantidade
    ];
}

echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);
