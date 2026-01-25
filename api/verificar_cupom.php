<?php
session_start();
require_once '../config/database.php';
require_once '../models/Usuario.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['foto_perfil'])) {
    echo json_encode(['success' => false, 'message' => 'Arquivo não enviado']);
    exit;
}

$file = $_FILES['foto_perfil'];
$user_id = $_SESSION['usuario_id'];

// Validações
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido. Use PNG, JPEG ou JPG']);
    exit;
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Máximo 5MB']);
    exit;
}

// Criar diretório se não existir
$upload_dir = '../uploads/profile_photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Gerar nome único
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Upload do arquivo
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // Atualizar banco de dados
    $usuarioModel = new Usuario();
    $db_path = 'uploads/profile_photos/' . $filename;
    
    if ($usuarioModel->atualizarFotoPerfil($user_id, $db_path)) {
        // Atualizar sessão
        $_SESSION['usuario_foto'] = $db_path;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Foto atualizada com sucesso',
            'photo_url' => '../' . $db_path
        ]);
    } else {
        unlink($filepath); // Remove arquivo se falhou no banco
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar no banco de dados']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload do arquivo']);
}
?>