<?php
// api/cancelar_servico.php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['agendamento_id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID do serviço não informado']);
    exit();
}

require_once '../config.php';

try {
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = :id AND usuario_id = :usuario_id");
    $stmt->execute(['id' => $id, 'usuario_id' => $_SESSION['usuario_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Serviço cancelado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Serviço não encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao cancelar']);
}
?>
