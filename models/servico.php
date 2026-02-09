<?php
class Servico {
    private $conn;
    private $table_name = "servicos";
    
    public function __construct() {
        require_once __DIR__ . '/../config.php';
        global $pdo;
        $this->conn = $pdo;
    }
    
    public function criar($dados) {
        try {
            $sql = "INSERT INTO " . $this->table_name . " 
                    (usuario_id, tipo_servico, descricao, preco, status, data_agendamento) 
                    VALUES (:usuario_id, :tipo_servico, :descricao, :preco, :status, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':usuario_id', $dados['usuario_id']);
            $stmt->bindParam(':tipo_servico', $dados['tipo_servico']);
            $stmt->bindParam(':descricao', $dados['descricao']);
            $stmt->bindParam(':preco', $dados['preco']);
            $stmt->bindParam(':status', $dados['status']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'id' => $this->conn->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Erro ao criar serviço'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro no banco: ' . $e->getMessage()];
        }
    }
    
    public function buscarPorUsuario($usuario_id) {
        // Servicos table doesn't have usuario_id, return empty array
        return [];
    }
    
    public function listarAtivos() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY nome";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return ['success' => true, 'servicos' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
    
    public function listarCategorias() {
        $sql = "SELECT DISTINCT categoria FROM " . $this->table_name . " WHERE ativo = 1 AND categoria IS NOT NULL ORDER BY categoria";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return ['success' => true, 'categorias' => $stmt->fetchAll(PDO::FETCH_COLUMN)];
    }
    
    public function buscarPorId($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function atualizarStatus($id, $status) {
        try {
            $sql = "UPDATE " . $this->table_name . " SET status = :status";
            if ($status === 'concluido') {
                $sql .= ", data_conclusao = NOW()";
            }
            $sql .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function buscarTodos() {
        $sql = "SELECT s.*, u.nome as usuario_nome, u.email as usuario_email 
                FROM " . $this->table_name . " s 
                LEFT JOIN usuarios u ON s.usuario_id = u.id 
                ORDER BY s.data_agendamento DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>