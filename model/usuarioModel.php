<?php

require_once 'model/Model.php';

class UsuarioModel extends Model {
    
    public function __construct() {
        $this->table = 'usuarios';
    }

    public function fields() {
        return [
            'id',
            'nome',
            'email',
            'senha',
            'tipo',
            'status',
            'ultimo_acesso',
            'token_reset',
            'token_expiracao',
            'tentativas_login',
            'bloqueado_ate'
        ];
    }

    public function rules() {
        return [
            'nome' => ['required', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'senha' => ['required', 'min:6', 'max:255'],
            'tipo' => ['required'],
            'status' => ['required']
        ];
    }

    public function authenticate($email, $senha) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} WHERE email = :email AND deleted_at IS NULL LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Atualiza último acesso
                $this->updateLastAccess($usuario['id']);
                return $usuario;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Erro na autenticação: " . $e->getMessage());
            return false;
        }
    }

    private function updateLastAccess($id) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} SET ultimo_acesso = NOW() WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao atualizar último acesso: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($data) {
        // Hash da senha antes de salvar
        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        return $this->create($data);
    }

    public function updateUser($data, $id) {
        // Se houver senha no update, fazer hash
        if (isset($data['senha'])) {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }
        return $this->update($data, $id);
    }

    public function generatePasswordResetToken($email) {
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} SET token_reset = :token, token_expiracao = :expiracao 
                    WHERE email = :email AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiracao', $expiracao);
            $stmt->bindParam(':email', $email);
            
            if ($stmt->execute()) {
                return $token;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erro ao gerar token de reset: " . $e->getMessage());
            return false;
        }
    }

    public function validateResetToken($token) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT id FROM {$this->table} 
                    WHERE token_reset = :token 
                    AND token_expiracao > NOW() 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao validar token: " . $e->getMessage());
            return false;
        }
    }

    public function resetPassword($token, $novaSenha) {
        try {
            $usuario = $this->validateResetToken($token);
            if (!$usuario) {
                return false;
            }

            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET senha = :senha, token_reset = NULL, token_expiracao = NULL 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':id', $usuario['id']);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao resetar senha: " . $e->getMessage());
            return false;
        }
    }
}