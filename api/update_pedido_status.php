<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once '../config.php';

if(!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header('Location: ../admin/pedidos.php');
exit();
