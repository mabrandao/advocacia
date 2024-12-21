<?php

require_once 'model/Model.php';

class LogSistemaModel extends Model {
    
    public function __construct() {
        $this->table = 'logs_sistema';
    }

    public function fields() {
        return [
            'id',
            'usuario_id',
            'acao',
            'tabela',
            'registro_id',
            'dados',
            'ip'
        ];
    }

    public function rules() {
        return [
            'usuario_id' => ['required'],
            'acao' => ['required', 'max:255'],
            'ip' => ['required', 'max:45']
        ];
    }

    public function registrarLog($data) {
        try {
            // Sanitiza os dados
            $data = parent::sanitizeData($data);
            
            $conn = Database::getInstance();
            $sql = "INSERT INTO {$this->table} 
                    (usuario_id, acao, tabela, registro_id, dados, ip) 
                    VALUES 
                    (:usuario_id, :acao, :tabela, :registro_id, :dados, :ip)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':usuario_id' => $data['usuario_id'],
                ':acao' => $data['acao'],
                ':tabela' => $data['tabela'] ?? null,
                ':registro_id' => $data['registro_id'] ?? null,
                ':dados' => is_array($data['dados']) ? json_encode($data['dados']) : $data['dados'],
                ':ip' => $data['ip']
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao registrar log: " . $e->getMessage());
            return false;
        }
    }

    public function getLogsPorUsuario($usuario_id) {
        try {
            // Sanitiza o input
            $usuario_id = parent::sanitizeValue($usuario_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT l.*, u.nome as usuario_nome 
                    FROM {$this->table} l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    WHERE l.usuario_id = :usuario_id 
                    AND l.deleted_at IS NULL 
                    ORDER BY l.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar logs do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function getLogsPorAcao($acao) {
        try {
            // Sanitiza o input
            $acao = parent::sanitizeValue($acao);
            
            $conn = Database::getInstance();
            $sql = "SELECT l.*, u.nome as usuario_nome 
                    FROM {$this->table} l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    WHERE l.acao = :acao 
                    AND l.deleted_at IS NULL 
                    ORDER BY l.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':acao', $acao);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar logs por ação: " . $e->getMessage());
            return false;
        }
    }

    public function getLogsPorTabela($tabela) {
        try {
            // Sanitiza o input
            $tabela = parent::sanitizeValue($tabela);
            
            $conn = Database::getInstance();
            $sql = "SELECT l.*, u.nome as usuario_nome 
                    FROM {$this->table} l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    WHERE l.tabela = :tabela 
                    AND l.deleted_at IS NULL 
                    ORDER BY l.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tabela', $tabela);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar logs por tabela: " . $e->getMessage());
            return false;
        }
    }

    public function getLogsPorIP($ip) {
        try {
            // Sanitiza o input
            $ip = parent::sanitizeValue($ip);
            
            $conn = Database::getInstance();
            $sql = "SELECT l.*, u.nome as usuario_nome 
                    FROM {$this->table} l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    WHERE l.ip = :ip 
                    AND l.deleted_at IS NULL 
                    ORDER BY l.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':ip', $ip);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar logs por IP: " . $e->getMessage());
            return false;
        }
    }

    public function getLogsPorPeriodo($dataInicio, $dataFim) {
        try {
            // Sanitiza os inputs
            $dataInicio = parent::sanitizeValue($dataInicio);
            $dataFim = parent::sanitizeValue($dataFim);
            
            $conn = Database::getInstance();
            $sql = "SELECT l.*, u.nome as usuario_nome 
                    FROM {$this->table} l 
                    LEFT JOIN usuarios u ON l.usuario_id = u.id 
                    WHERE DATE(l.created_at) BETWEEN :data_inicio AND :data_fim 
                    AND l.deleted_at IS NULL 
                    ORDER BY l.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar logs por período: " . $e->getMessage());
            return false;
        }
    }

    public function limparLogsAntigos($dias = 90) {
        try {
            // Sanitiza o input
            $dias = parent::sanitizeValue($dias);
            
            $conn = Database::getInstance();
            $sql = "DELETE FROM {$this->table} 
                    WHERE created_at < DATE_SUB(NOW(), INTERVAL :dias DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao limpar logs antigos: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasLogs($periodo = 'hoje') {
        try {
            $conn = Database::getInstance();
            
            $where = "";
            switch($periodo) {
                case 'hoje':
                    $where = "AND DATE(l.created_at) = CURDATE()";
                    break;
                case 'semana':
                    $where = "AND YEARWEEK(l.created_at) = YEARWEEK(NOW())";
                    break;
                case 'mes':
                    $where = "AND MONTH(l.created_at) = MONTH(NOW()) AND YEAR(l.created_at) = YEAR(NOW())";
                    break;
            }
            
            $sql = "SELECT 
                        COUNT(*) as total_logs,
                        COUNT(DISTINCT l.usuario_id) as total_usuarios,
                        COUNT(DISTINCT l.ip) as total_ips,
                        COUNT(DISTINCT l.acao) as total_acoes,
                        MAX(l.created_at) as ultima_acao
                    FROM {$this->table} l 
                    WHERE l.deleted_at IS NULL 
                    {$where}";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas de logs: " . $e->getMessage());
            return false;
        }
    }
}
