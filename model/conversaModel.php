<?php

require_once 'model/Model.php';

class ConversaModel extends Model {
    
    public function __construct() {
        $this->table = 'conversas';
    }

    public function fields() {
        return [
            'id',
            'cliente_id',
            'assunto',
            'processo_id',
            'status',
            'data_ultima_mensagem'
        ];
    }

    public function rules() {
        return [
            'cliente_id' => ['required'],
            'processo_id' => ['required']
        ];
    }

    public function getConversasCliente($cliente_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT c.*, p.numero_processo, p.titulo as processo_titulo,
                           (SELECT COUNT(*) FROM mensagens m 
                            WHERE m.conversa_id = c.id 
                            AND m.lida = 0 
                            AND m.deleted_at IS NULL) as mensagens_nao_lidas 
                    FROM {$this->table} c 
                    LEFT JOIN processos p ON c.processo_id = p.id 
                    WHERE c.cliente_id = :cliente_id 
                    AND c.deleted_at IS NULL 
                    ORDER BY c.data_ultima_mensagem DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar conversas do cliente: " . $e->getMessage());
            return false;
        }
    }

    public function getConversasProcesso($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT c.*, cl.nome as cliente_nome,
                           (SELECT COUNT(*) FROM mensagens m 
                            WHERE m.conversa_id = c.id 
                            AND m.lida = 0 
                            AND m.deleted_at IS NULL) as mensagens_nao_lidas 
                    FROM {$this->table} c 
                    LEFT JOIN clientes cl ON c.cliente_id = cl.id 
                    WHERE c.processo_id = :processo_id 
                    AND c.deleted_at IS NULL 
                    ORDER BY c.data_ultima_mensagem DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar conversas do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getConversasNaoLidas() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT c.*, cl.nome as cliente_nome, p.numero_processo,
                           COUNT(m.id) as total_nao_lidas 
                    FROM {$this->table} c 
                    LEFT JOIN clientes cl ON c.cliente_id = cl.id 
                    LEFT JOIN processos p ON c.processo_id = p.id 
                    INNER JOIN mensagens m ON c.id = m.conversa_id 
                    WHERE m.lida = 0 
                    AND c.deleted_at IS NULL 
                    AND m.deleted_at IS NULL 
                    GROUP BY c.id 
                    ORDER BY c.data_ultima_mensagem DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar conversas não lidas: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarUltimaMensagem($conversa_id) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET data_ultima_mensagem = NOW() 
                    WHERE id = :conversa_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':conversa_id', $conversa_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao atualizar última mensagem: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarStatus($conversa_id, $status) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET status = :status 
                    WHERE id = :conversa_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':conversa_id', $conversa_id);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao atualizar status da conversa: " . $e->getMessage());
            return false;
        }
    }

    public function getMensagens($conversa_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT m.*, u.nome as remetente_nome 
                    FROM mensagens m 
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

    public function marcarMensagensComoLidas($conversa_id, $usuario_id) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE mensagens 
                    SET lida = 1, data_leitura = NOW() 
                    WHERE conversa_id = :conversa_id 
                    AND remetente_id != :usuario_id 
                    AND lida = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':conversa_id', $conversa_id);
            $stmt->bindParam(':usuario_id', $usuario_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao marcar mensagens como lidas: " . $e->getMessage());
            return false;
        }
    }
}
