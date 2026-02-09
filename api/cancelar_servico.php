<?php
// api/cancelar_servico.php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

// Obter dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID do serviço não informado']);
    exit();
}

// Incluir banco de dados
require_once '../config.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Verificar se o agendamento pertence ao usuário
    $query = "SELECT id, usuario_id FROM agendamentos WHERE id = :id AND usuario_id = :usuario_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Serviço não encontrado ou não pertence a você']);
        exit();
    }
    
    // Atualizar status para cancelado
    $updateQuery = "UPDATE agendamentos SET status = 'cancelado' WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':id', $id);
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Serviço cancelado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar serviço']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
