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
        $sql = "SELECT p.*, 
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', pr.nome, 'price', ip.preco_unitario, 'quantity', ip.quantidade))
                 FROM itens_pedido ip 
                 JOIN produtos pr ON ip.produto_id = pr.id 
                 WHERE ip.pedido_id = p.id) as items
                FROM " . $this->table_name . " p 
                WHERE p.usuario_id = :usuario_id 
                ORDER BY p.data_pedido DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
