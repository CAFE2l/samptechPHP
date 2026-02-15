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

$produto_id = $_POST['produto_id'] ?? 0;
$avaliacao = $_POST['avaliacao'] ?? 0;
$comentario = $_POST['comentario'] ?? '';

if($avaliacao < 1 || $avaliacao > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating']);
    exit();
}

$midia = '';
if(isset($_FILES['midia']) && $_FILES['midia']['error'] == 0) {
    $ext = pathinfo($_FILES['midia']['name'], PATHINFO_EXTENSION);
    $midia = 'uploads/avaliacoes/' . uniqid() . '.' . $ext;
    if(!is_dir('../uploads/avaliacoes')) mkdir('../uploads/avaliacoes', 0777, true);
    move_uploaded_file($_FILES['midia']['tmp_name'], '../' . $midia);
}

try {
    $stmt = $pdo->prepare("INSERT INTO avaliacoes_produtos (produto_id, usuario_id, avaliacao, comentario, midia) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$produto_id, $_SESSION['usuario_id'], $avaliacao, $comentario, $midia]);
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
