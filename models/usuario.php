<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";
    
    public function __construct() {
        require_once __DIR__ . '/../config.php';
        global $pdo;
        $this->conn = $pdo;
    }
    
    public function criar($dados) {
        try {
            // Verificar se email já existe
            if ($this->emailExiste($dados['email'])) {
                return ['success' => false, 'message' => 'Este email já está cadastrado.'];
            }
            
            // Hash da senha
            $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO " . $this->table_name . " 
                    (nome, email, senha, telefone, cpf, endereco, bairro, cidade, estado, cep, tipo, data_cadastro) 
                    VALUES (:nome, :email, :senha, :telefone, :cpf, :endereco, :bairro, :cidade, :estado, :cep, :tipo, NOW())";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':email', $dados['email']);
            $stmt->bindParam(':senha', $senha_hash);
            $stmt->bindParam(':telefone', $dados['telefone']);
            $stmt->bindParam(':cpf', $dados['cpf']);
            $stmt->bindParam(':endereco', $dados['endereco']);
            $stmt->bindParam(':bairro', $dados['bairro']);
            $stmt->bindParam(':cidade', $dados['cidade']);
            $stmt->bindParam(':estado', $dados['estado']);
            $stmt->bindParam(':cep', $dados['cep']);
            $stmt->bindParam(':tipo', $dados['tipo']);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Usuário criado com sucesso.', 'id' => $this->conn->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'Erro ao criar usuário.'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()];
        }
    }
    
    public function checkEmail($email) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function emailExiste($email) {
        $sql = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function buscarPorId($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function login($email, $senha) {
        $usuario = $this->buscarPorEmail($email);
        
        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            return ['success' => false, 'message' => 'Email ou senha incorretos.'];
        }
        
        return ['success' => true, 'usuario' => $usuario];
    }
    
    public function atualizarSenha($id, $nova_senha) {
        try {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            
            $sql = "UPDATE " . $this->table_name . " SET senha = :senha WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':senha', $senha_hash);
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function buscarPorFirebaseUid($firebase_uid) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE firebase_uid = :firebase_uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':firebase_uid', $firebase_uid);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function vincularContaGoogle($user_id, $firebase_uid) {
        $sql = "UPDATE " . $this->table_name . " SET 
                firebase_uid = :firebase_uid,
                provider = 'google',
                ultimo_login = NOW()
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        $stmt->bindParam(':firebase_uid', $firebase_uid);
        
        return $stmt->execute();
    }
    
    public function atualizarUltimoLogin($user_id) {
        $sql = "UPDATE " . $this->table_name . " SET ultimo_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $user_id);
        
        return $stmt->execute();
    }
    
    public function criarUsuarioGoogle($dados) {
        $sql = "INSERT INTO " . $this->table_name . " 
                (firebase_uid, email, nome, provider, tipo, data_cadastro) 
                VALUES (:firebase_uid, :email, :nome, :provider, :tipo, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':firebase_uid', $dados['firebase_uid']);
        $stmt->bindParam(':email', $dados['email']);
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':provider', $dados['provider']);
        $stmt->bindParam(':tipo', $dados['tipo']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function atualizarFotoPerfil($id, $foto_path) {
        try {
            $sql = "UPDATE " . $this->table_name . " SET 
                    foto_perfil = :foto,
                    foto_perfil_url = :foto_url
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':foto', $foto_path);
            $stmt->bindParam(':foto_url', $foto_path); // Same path for now
            $stmt->bindParam(':id', $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function atualizar($id, $dados) {
        try {
            $sql = "UPDATE " . $this->table_name . " SET 
                    nome = :nome,
                    telefone = :telefone,
                    cpf = :cpf,
                    endereco = :endereco,
                    bairro = :bairro,
                    cidade = :cidade,
                    estado = :estado,
                    cep = :cep
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nome', $dados['nome']);
            $stmt->bindParam(':telefone', $dados['telefone']);
            $stmt->bindParam(':cpf', $dados['cpf']);
            $stmt->bindParam(':endereco', $dados['endereco']);
            $stmt->bindParam(':bairro', $dados['bairro']);
            $stmt->bindParam(':cidade', $dados['cidade']);
            $stmt->bindParam(':estado', $dados['estado']);
            $stmt->bindParam(':cep', $dados['cep']);
            
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>