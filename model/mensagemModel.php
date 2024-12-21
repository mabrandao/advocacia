<?php

require_once 'model/Model.php';

class MensagemModel extends Model {
    
    public function __construct() {
        $this->table = 'mensagens';
    }

    public function fields() {
        return [
            'id',
            'conversa_id',
            'remetente_id',
            'conteudo',
            'lida',
            'data_leitura'
        ];
    }

    public function rules() {
        return [
            'conversa_id' => ['required'],
            'remetente_id' => ['required'],
            'conteudo' => ['required']
        ];
    }

    public function enviarMensagem($data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            
            $conn = Database::getInstance();
            
            // Insere a mensagem
            $sql = "INSERT INTO {$this->table} 
                    (conversa_id, remetente_id, conteudo) 
                    VALUES 
                    (:conversa_id, :remetente_id, :conteudo)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                ':conversa_id' => $data['conversa_id'],
                ':remetente_id' => $data['remetente_id'],
                ':conteudo' => $data['conteudo']
            ]);
            
            if ($result) {
                // Atualiza a data da última mensagem na conversa
                $sql = "UPDATE conversas 
                        SET data_ultima_mensagem = NOW() 
                        WHERE id = :conversa_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':conversa_id' => $data['conversa_id']]);
                
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Erro ao enviar mensagem: " . $e->getMessage());
            return false;
        }
    }

    public function getMensagensConversa($conversa_id) {
        try {
            // Sanitiza o input
            $conversa_id = $this->sanitizeValue($conversa_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT m.*, u.nome as remetente_nome 
                    FROM {$this->table} m 
                    LEFT JOIN usuarios u ON m.remetente_id = u.id 
                    WHERE m.conversa_id = :conversa_id 
                    AND m.deleted_at IS NULL 
                    ORDER BY m.created_at ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':conversa_id', $conversa_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar mensagens da conversa: " . $e->getMessage());
            return false;
        }
    }

    public function getMensagensNaoLidas($usuario_id) {
        try {
            // Sanitiza o input
            $usuario_id = $this->sanitizeValue($usuario_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT m.*, u.nome as remetente_nome, c.assunto as conversa_assunto 
                    FROM {$this->table} m 
                    LEFT JOIN usuarios u ON m.remetente_id = u.id 
                    LEFT JOIN conversas c ON m.conversa_id = c.id 
                    WHERE m.lida = 0 
                    AND m.remetente_id != :usuario_id 
                    AND m.deleted_at IS NULL 
                    ORDER BY m.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar mensagens não lidas: " . $e->getMessage());
            return false;
        }
    }

    public function marcarComoLida($mensagem_id) {
        try {
            // Sanitiza o input
            $mensagem_id = $this->sanitizeValue($mensagem_id);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET lida = 1, data_leitura = NOW() 
                    WHERE id = :mensagem_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':mensagem_id', $mensagem_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao marcar mensagem como lida: " . $e->getMessage());
            return false;
        }
    }

    public function marcarTodasComoLidas($conversa_id, $usuario_id) {
        try {
            // Sanitiza os inputs
            $conversa_id = $this->sanitizeValue($conversa_id);
            $usuario_id = $this->sanitizeValue($usuario_id);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET lida = 1, data_leitura = NOW() 
                    WHERE conversa_id = :conversa_id 
                    AND remetente_id != :usuario_id 
                    AND lida = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':conversa_id', $conversa_id);
            $stmt->bindParam(':usuario_id', $usuario_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao marcar todas mensagens como lidas: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasConversa($conversa_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(*) as total_mensagens,
                        COUNT(CASE WHEN lida = 0 THEN 1 END) as nao_lidas,
                        MIN(created_at) as primeira_mensagem,
                        MAX(created_at) as ultima_mensagem,
                        COUNT(DISTINCT remetente_id) as total_participantes
                    FROM {$this->table} 
                    WHERE conversa_id = :conversa_id 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':conversa_id', $conversa_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas da conversa: " . $e->getMessage());
            return false;
        }
    }

    public function buscarMensagens($termo, $conversa_id = null) {
        try {
            // Sanitiza os inputs
            $termo = $this->sanitizeValue($termo);
            if ($conversa_id !== null) {
                $conversa_id = $this->sanitizeValue($conversa_id);
            }
            
            $conn = Database::getInstance();
            $sql = "SELECT m.*, u.nome as remetente_nome, c.assunto as conversa_assunto 
                    FROM {$this->table} m 
                    LEFT JOIN usuarios u ON m.remetente_id = u.id 
                    LEFT JOIN conversas c ON m.conversa_id = c.id 
                    WHERE m.conteudo LIKE :termo 
                    AND m.deleted_at IS NULL";
            
            if ($conversa_id) {
                $sql .= " AND m.conversa_id = :conversa_id";
            }
            
            $sql .= " ORDER BY m.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $termo = "%{$termo}%";
            $stmt->bindParam(':termo', $termo);
            
            if ($conversa_id) {
                $stmt->bindParam(':conversa_id', $conversa_id);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar mensagens: " . $e->getMessage());
            return false;
        }
    }
}
