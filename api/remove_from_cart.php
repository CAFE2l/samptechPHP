<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$produto_id = $data['produto_id'] ?? 0;

if(isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item['produto_id'] != $produto_id);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

echo json_encode(['success' => true]);
