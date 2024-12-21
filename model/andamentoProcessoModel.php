<?php

require_once 'model/Model.php';

class AndamentoProcessoModel extends Model {
    
    public function __construct() {
        $this->table = 'andamentos_processo';
    }

    public function fields() {
        return [
            'id',
            'processo_id',
            'data_andamento',
            'tipo_andamento',
            'descricao',
            'usuario_id',
            'arquivo_anexo'
        ];
    }

    public function rules() {
        return [
            'processo_id' => ['required'],
            'data_andamento' => ['required'],
            'tipo_andamento' => ['required', 'max:100'],
            'descricao' => ['required'],
            'usuario_id' => ['required']
        ];
    }

    public function getAndamentosProcesso($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT ap.*, u.nome as usuario_nome 
                    FROM {$this->table} ap 
                    LEFT JOIN usuarios u ON ap.usuario_id = u.id 
                    WHERE ap.processo_id = :processo_id 
                    AND ap.deleted_at IS NULL 
                    ORDER BY ap.data_andamento DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar andamentos do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getUltimosAndamentos($limite = 10) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT ap.*, u.nome as usuario_nome, p.numero_processo, p.titulo as processo_titulo 
                    FROM {$this->table} ap 
                    LEFT JOIN usuarios u ON ap.usuario_id = u.id 
                    LEFT JOIN processos p ON ap.processo_id = p.id 
                    WHERE ap.deleted_at IS NULL 
                    ORDER BY ap.data_andamento DESC 
                    LIMIT :limite";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar últimos andamentos: " . $e->getMessage());
            return false;
        }
    }

    public function getAndamentosPorTipo($tipo_andamento) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT ap.*, u.nome as usuario_nome, p.numero_processo 
                    FROM {$this->table} ap 
                    LEFT JOIN usuarios u ON ap.usuario_id = u.id 
                    LEFT JOIN processos p ON ap.processo_id = p.id 
                    WHERE ap.tipo_andamento = :tipo_andamento 
                    AND ap.deleted_at IS NULL 
                    ORDER BY ap.data_andamento DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tipo_andamento', $tipo_andamento);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar andamentos por tipo: " . $e->getMessage());
            return false;
        }
    }

    public function getAndamentosPeriodo($dataInicio, $dataFim) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT ap.*, u.nome as usuario_nome, p.numero_processo 
                    FROM {$this->table} ap 
                    LEFT JOIN usuarios u ON ap.usuario_id = u.id 
                    LEFT JOIN processos p ON ap.processo_id = p.id 
                    WHERE DATE(ap.data_andamento) BETWEEN :data_inicio AND :data_fim 
                    AND ap.deleted_at IS NULL 
                    ORDER BY ap.data_andamento DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar andamentos por período: " . $e->getMessage());
            return false;
        }
    }

    public function getAndamentosUsuario($usuario_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT ap.*, p.numero_processo, p.titulo as processo_titulo 
                    FROM {$this->table} ap 
                    LEFT JOIN processos p ON ap.processo_id = p.id 
                    WHERE ap.usuario_id = :usuario_id 
                    AND ap.deleted_at IS NULL 
                    ORDER BY ap.data_andamento DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar andamentos do usuário: " . $e->getMessage());
            return false;
        }
    }
}
