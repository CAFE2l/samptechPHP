<?php
// models/Agendamento.php

require_once __DIR__ . '/../config/database.php';

class Agendamento {
    private $conn;
    private $table_name = "agendamentos";

    public $id;
    public $usuario_id;
    public $servico_id;
    public $data_agendamento;
    public $descricao_problema;
    public $status;
    public $valor_orcamento;
    public $observacoes;
    public $data_conclusao;
    public $status_pagamento;
    public $metodo_pagamento;
    public $codigo_transacao;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Criar novo agendamento
    public function criar($dados) {
        try {
            // Verificar se já existe agendamento para esse horário
            $query_check = "SELECT id FROM " . $this->table_name . " 
                           WHERE data_agendamento = :data_agendamento 
                           AND status NOT IN ('cancelado')";
            
            $stmt_check = $this->conn->prepare($query_check);
            $stmt_check->bindParam(":data_agendamento", $dados['data_agendamento']);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'Este horário já está ocupado. Por favor, escolha outro horário.'
                ];
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     (usuario_id, servico_id, data_agendamento, descricao_problema, 
                      status, valor_orcamento, observacoes, status_pagamento) 
                      VALUES 
                     (:usuario_id, :servico_id, :data_agendamento, :descricao_problema, 
                      'pendente', :valor_orcamento, :observacoes, 'pendente')";

            $stmt = $this->conn->prepare($query);

            // Bind dos valores
            $stmt->bindParam(":usuario_id", $dados['usuario_id']);
            $stmt->bindParam(":servico_id", $dados['servico_id']);
            $stmt->bindParam(":data_agendamento", $dados['data_agendamento']);
            $stmt->bindParam(":descricao_problema", $dados['descricao_problema']);
            $stmt->bindParam(":valor_orcamento", $dados['valor_orcamento']);
            $stmt->bindParam(":observacoes", $dados['observacoes']);

            if ($stmt->execute()) {
                $agendamento_id = $this->conn->lastInsertId();
                
                // Atualizar horário como indisponível
                $this->atualizarHorario($dados['data_agendamento']);
                
                return [
                    'success' => true,
                    'id' => $agendamento_id,
                    'message' => 'Agendamento criado com sucesso!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar agendamento.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no sistema: ' . $e->getMessage()
            ];
        }
    }

    // Atualizar horário como indisponível
    private function atualizarHorario($data_agendamento) {
        try {
            $data = date('Y-m-d', strtotime($data_agendamento));
            $hora = date('H:i:s', strtotime($data_agendamento));
            
            $query = "UPDATE horarios_disponiveis 
                      SET disponivel = FALSE, agendamento_id = :agendamento_id
                      WHERE data = :data AND hora = :hora";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":agendamento_id", $this->id);
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":hora", $hora);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Listar agendamentos do usuário
    public function listarPorUsuario($usuario_id, $filtro_status = null) {
        try {
            $query = "SELECT a.*, s.nome as servico_nome, s.preco as servico_preco,
                             DATE_FORMAT(a.data_agendamento, '%d/%m/%Y %H:%i') as data_formatada,
                             CASE 
                                 WHEN a.status = 'pendente' THEN 'Pendente'
                                 WHEN a.status = 'confirmado' THEN 'Confirmado'
                                 WHEN a.status = 'em_andamento' THEN 'Em Andamento'
                                 WHEN a.status = 'concluido' THEN 'Concluído'
                                 WHEN a.status = 'cancelado' THEN 'Cancelado'
                                 ELSE a.status
                             END as status_formatado
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
                'message' => 'Erro ao listar agendamentos: ' . $e->getMessage()
            ];
        }
    }

    // Buscar agendamento por ID
    public function buscarPorId($id) {
        try {
            $query = "SELECT a.*, s.nome as servico_nome, s.descricao as servico_descricao,
                             s.preco as servico_preco, s.garantia as servico_garantia,
                             DATE_FORMAT(a.data_agendamento, '%d/%m/%Y %H:%i') as data_formatada,
                             u.nome as usuario_nome, u.email as usuario_email, u.telefone as usuario_telefone
                      FROM " . $this->table_name . " a
                      INNER JOIN servicos s ON a.servico_id = s.id
                      INNER JOIN usuarios u ON a.usuario_id = u.id
                      WHERE a.id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'agendamento' => $stmt->fetch(PDO::FETCH_ASSOC)
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Agendamento não encontrado.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar agendamento: ' . $e->getMessage()
            ];
        }
    }

    // Atualizar status do agendamento
    public function atualizarStatus($id, $status) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":status", $status);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Status atualizado com sucesso!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao atualizar status.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no sistema: ' . $e->getMessage()
            ];
        }
    }

    // Cancelar agendamento
    public function cancelar($id, $usuario_id = null) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = 'cancelado'
                      WHERE id = :id";
            
            if ($usuario_id) {
                $query .= " AND usuario_id = :usuario_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            
            if ($usuario_id) {
                $stmt->bindParam(":usuario_id", $usuario_id);
            }
            
            if ($stmt->execute()) {
                // Liberar horário
                $this->liberarHorario($id);
                
                return [
                    'success' => true,
                    'message' => 'Agendamento cancelado com sucesso!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao cancelar agendamento.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no sistema: ' . $e->getMessage()
            ];
        }
    }

    // Liberar horário quando cancelar
    private function liberarHorario($agendamento_id) {
        try {
            $query = "UPDATE horarios_disponiveis 
                      SET disponivel = TRUE, agendamento_id = NULL
                      WHERE agendamento_id = :agendamento_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":agendamento_id", $agendamento_id);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Buscar horários disponíveis
    public function buscarHorariosDisponiveis($data = null) {
        try {
            $query = "SELECT * FROM horarios_disponiveis 
                      WHERE disponivel = TRUE";
            
            if ($data) {
                $query .= " AND data = :data";
            }
            
            $query .= " ORDER BY data, hora";
            
            $stmt = $this->conn->prepare($query);
            
            if ($data) {
                $stmt->bindParam(":data", $data);
            }
            
            $stmt->execute();
            
            return [
                'success' => true,
                'horarios' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar horários: ' . $e->getMessage()
            ];
        }
    }

    // Verificar promoção
    public function verificarPromocao($codigo) {
        try {
            $query = "SELECT p.*, s.nome as servico_nome 
                      FROM promocoes p
                      LEFT JOIN servicos s ON p.servico_id = s.id
                      WHERE p.codigo = :codigo 
                      AND p.ativo = TRUE 
                      AND (p.valido_ate IS NULL OR p.valido_ate >= CURDATE())
                      AND (p.usos_maximos IS NULL OR p.usos_atual < p.usos_maximos)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":codigo", $codigo);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $promocao = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Incrementar uso
                $this->incrementarUsoPromocao($promocao['id']);
                
                return [
                    'success' => true,
                    'promocao' => $promocao
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Código promocional inválido ou expirado.'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao verificar promoção: ' . $e->getMessage()
            ];
        }
    }

    // Incrementar uso da promoção
    private function incrementarUsoPromocao($promocao_id) {
        try {
            $query = "UPDATE promocoes 
                      SET usos_atual = usos_atual + 1
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $promocao_id);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>  