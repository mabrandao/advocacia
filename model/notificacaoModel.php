<?php

require_once 'model/Model.php';

class NotificacaoModel extends Model {
    
    public function __construct() {
        $this->table = 'notificacoes';
    }

    public function fields() {
        return [
            'id',
            'usuario_id',
            'titulo',
            'mensagem',
            'tipo',
            'lida',
            'data_leitura'
        ];
    }

    public function rules() {
        return [
            'usuario_id' => ['required'],
            'titulo' => ['required', 'max:255'],
            'mensagem' => ['required'],
            'tipo' => ['required', 'max:50']
        ];
    }

    public function criarNotificacao($data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            
            $conn = Database::getInstance();
            $sql = "INSERT INTO {$this->table} 
                    (usuario_id, titulo, mensagem, tipo) 
                    VALUES 
                    (:usuario_id, :titulo, :mensagem, :tipo)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':usuario_id' => $data['usuario_id'],
                ':titulo' => $data['titulo'],
                ':mensagem' => $data['mensagem'],
                ':tipo' => $data['tipo']
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
            return false;
        }
    }

    public function getNotificacoesUsuario($usuario_id, $apenas_nao_lidas = false) {
        try {
            // Sanitiza o input
            $usuario_id = $this->sanitizeValue($usuario_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE usuario_id = :usuario_id 
                    AND deleted_at IS NULL";
            
            if ($apenas_nao_lidas) {
                $sql .= " AND lida = 0";
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar notificações do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function marcarComoLida($notificacao_id) {
        try {
            // Sanitiza o input
            $notificacao_id = $this->sanitizeValue($notificacao_id);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET lida = 1, data_leitura = NOW() 
                    WHERE id = :notificacao_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':notificacao_id', $notificacao_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao marcar notificação como lida: " . $e->getMessage());
            return false;
        }
    }

    public function marcarTodasComoLidas($usuario_id) {
        try {
            // Sanitiza o input
            $usuario_id = $this->sanitizeValue($usuario_id);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET lida = 1, data_leitura = NOW() 
                    WHERE usuario_id = :usuario_id 
                    AND lida = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao marcar todas notificações como lidas: " . $e->getMessage());
            return false;
        }
    }

    public function getNotificacoesPorTipo($tipo) {
        try {
            // Sanitiza o input
            $tipo = $this->sanitizeValue($tipo);
            
            $conn = Database::getInstance();
            $sql = "SELECT n.*, u.nome as usuario_nome 
                    FROM {$this->table} n 
                    LEFT JOIN usuarios u ON n.usuario_id = u.id 
                    WHERE n.tipo = :tipo 
                    AND n.deleted_at IS NULL 
                    ORDER BY n.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar notificações por tipo: " . $e->getMessage());
            return false;
        }
    }

    public function limparNotificacoesAntigas($dias = 30) {
        try {
            // Sanitiza o input
            $dias = $this->sanitizeValue($dias);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET deleted_at = NOW() 
                    WHERE created_at < DATE_SUB(NOW(), INTERVAL :dias DAY) 
                    AND lida = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao limpar notificações antigas: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasNotificacoes($usuario_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(*) as total_notificacoes,
                        COUNT(CASE WHEN lida = 0 THEN 1 END) as nao_lidas,
                        COUNT(CASE WHEN lida = 1 THEN 1 END) as lidas,
                        COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as ultimas_24h,
                        MAX(created_at) as ultima_notificacao
                    FROM {$this->table} 
                    WHERE usuario_id = :usuario_id 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas de notificações: " . $e->getMessage());
            return false;
        }
    }

    public function notificarUsuarios($usuarios, $dados) {
        try {
            // Sanitiza os dados
            $usuarios = $this->sanitizeData($usuarios);
            $dados = $this->sanitizeData($dados);
            
            $conn = Database::getInstance();
            $sql = "INSERT INTO {$this->table} 
                    (usuario_id, titulo, mensagem, tipo) 
                    VALUES 
                    (:usuario_id, :titulo, :mensagem, :tipo)";
            $stmt = $conn->prepare($sql);
            
            foreach ($usuarios as $usuario_id) {
                $stmt->execute([
                    ':usuario_id' => $usuario_id,
                    ':titulo' => $dados['titulo'],
                    ':mensagem' => $dados['mensagem'],
                    ':tipo' => $dados['tipo']
                ]);
            }
            
            return true;
        } catch(PDOException $e) {
            error_log("Erro ao notificar usuários: " . $e->getMessage());
            return false;
        }
    }
}
