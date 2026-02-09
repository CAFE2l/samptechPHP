<?php
// api/cancelar_agendamento.php

require_once '../config.php';
require_once '../models/Agendamento.php';

session_start();

// Verificar se é POST e se usuário está logado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

// Obter dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID do agendamento não informado']);
    exit();
}

$agendamentoModel = new Agendamento();

// Verificar se o agendamento pertence ao usuário
$resultado = $agendamentoModel->buscarPorId($id);

if (!$resultado['success']) {
    echo json_encode($resultado);
    exit();
}

// Verificar se o usuário é dono do agendamento ou admin
$agendamento = $resultado['agendamento'];
if ($agendamento['usuario_id'] != $_SESSION['usuario_id'] && $_SESSION['usuario_tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Não autorizado a cancelar este agendamento']);
    exit();
}

// Cancelar agendamento
$resultadoCancelar = $agendamentoModel->cancelar($id);

echo json_encode($resultadoCancelar);
?>
