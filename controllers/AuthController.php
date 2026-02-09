<?php
// controllers/AuthController.php

namespace SampTech\Controllers;

session_start();

class AuthController {
    
    public function verificarAutenticacao() {
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
            $redirect = urlencode($_SERVER['REQUEST_URI']);
            header('Location: ../pages/login.php?redirect=' . $redirect);
            exit();
        }
        return true;
    }
    
    public function verificarAdmin() {
        if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
            header('Location: ../pages/login.php');
            exit();
        }
        return true;
    }
    
    public function login($email, $senha) {
        require_once __DIR__ . '/../config.php';
        require_once __DIR__ . '/../models/Usuario.php';
        
        $usuarioModel = new \Usuario();
        $resultado = $usuarioModel->login($email, $senha);
        
        if ($resultado['success']) {
            $usuario = $resultado['usuario'];
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_telefone'] = $usuario['telefone'] ?? '';
            $_SESSION['usuario_endereco'] = $usuario['endereco'] ?? '';
            $_SESSION['usuario_tipo'] = $usuario['tipo'] ?? 'cliente';
            $_SESSION['usuario_logado'] = true;
            
            return [
                'success' => true,
                'message' => 'Login realizado com sucesso!'
            ];
        }
        
        return $resultado;
    }
    
    public function logout() {
        session_destroy();
        header('Location: ../pages/login.php');
        exit();
    }
}

// Função global para compatibilidade
function verificarAutenticacao() {
    $authController = new AuthController();
    return $authController->verificarAutenticacao();
}

function verificarAdmin() {
    $authController = new AuthController();
    return $authController->verificarAdmin();
}
?>
