<?php
require_once __DIR__ . '/../config.php';

class Agendamento {
    private $conn;
    private $table_name = "agendamentos";

    public function __construct() {
        global $pdo;
        $this->conn = $pdo;
    }

    public function listarPorUsuario($usuario_id, $filtro_status = null) {
        try {
            $query = "SELECT a.*, s.nome as servico_nome, s.preco as servico_preco,
                             DATE_FORMAT(a.data_agendamento, '%Y-%m-%d %H:%i') as data_agendamento,
                             a.status
                      FROM " . $this->table_name . " a
                      INNER JOIN servicos s ON a.servico_id = s.id
                      WHERE a.usuario_id = :usuario_id";
            
            if ($filtro_status) {
                $query .= " AND a.status = :status";
            }
            
            $query .= " ORDER BY a.data_agendamento DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario_id", $usuario_id);
            
            if ($filtro_status) {
                $stmt->bindParam(":status", $filtro_status);
            }
            
            $stmt->execute();
            
            return [
                'success' => true,
                'agendamentos' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'agendamentos' => [],
                'message' => 'Erro ao listar agendamentos: ' . $e->getMessage()
            ];
        }
    }
}
?>
