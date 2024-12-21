<?php

require_once 'model/Model.php';

class AgendaModel extends Model {
    
    public function __construct() {
        $this->table = 'agenda';
    }

    public function fields() {
        return [
            'id',
            'processo_id',
            'tipo',
            'data_hora',
            'local',
            'descricao',
            'status',
            'observacoes',
            'link_virtual',
            'lembrete_enviado'
        ];
    }

    public function rules() {
        return [
            'tipo' => ['required', 'max:50'],
            'data_hora' => ['required'],
            'local' => ['required', 'max:255'],
            'descricao' => ['required']
        ];
    }

    public function getCompromissosDia($data) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, p.titulo as processo_titulo, p.numero_processo 
                    FROM {$this->table} a 
                    LEFT JOIN processos p ON a.processo_id = p.id 
                    WHERE DATE(a.data_hora) = :data 
                    AND a.deleted_at IS NULL 
                    AND a.status != 'cancelada' 
                    ORDER BY a.data_hora ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data', $data);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar compromissos do dia: " . $e->getMessage());
            return false;
        }
    }

    public function getCompromissosPeriodo($dataInicio, $dataFim) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, p.titulo as processo_titulo, p.numero_processo 
                    FROM {$this->table} a 
                    LEFT JOIN processos p ON a.processo_id = p.id 
                    WHERE DATE(a.data_hora) BETWEEN :data_inicio AND :data_fim 
                    AND a.deleted_at IS NULL 
                    ORDER BY a.data_hora ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar compromissos do período: " . $e->getMessage());
            return false;
        }
    }

    public function getCompromissosProcesso($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE processo_id = :processo_id 
                    AND deleted_at IS NULL 
                    ORDER BY data_hora DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar compromissos do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getProximosCompromissos($limite = 5) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, p.titulo as processo_titulo, p.numero_processo 
                    FROM {$this->table} a 
                    LEFT JOIN processos p ON a.processo_id = p.id 
                    WHERE a.data_hora >= NOW() 
                    AND a.deleted_at IS NULL 
                    AND a.status != 'cancelada' 
                    ORDER BY a.data_hora ASC 
                    LIMIT :limite";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar próximos compromissos: " . $e->getMessage());
            return false;
        }
    }

    public function marcarLembreteEnviado($id) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET lembrete_enviado = 1 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao marcar lembrete como enviado: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarStatus($id, $status) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET status = :status 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao atualizar status do compromisso: " . $e->getMessage());
            return false;
        }
    }
}
