<?php
class Pedido {
    private $conn;
    private $table_name = "pedidos";
    
    public function __construct() {
        require_once __DIR__ . '/../config.php';
        global $pdo;
        $this->conn = $pdo;
    }
    
    public function buscarPorUsuario($usuario_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = :usuario_id ORDER BY data_pedido DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
