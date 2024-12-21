<?php

require_once 'model/Model.php';

class ProcessoProfissionalModel extends Model {
    
    public function __construct() {
        $this->table = 'processo_profissionais';
    }

    public function fields() {
        return [
            'id',
            'processo_id',
            'profissional_id',
            'funcao',
            'data_inicio',
            'data_fim',
            'status',
            'responsavel_principal',
            'observacoes'
        ];
    }

    public function rules() {
        return [
            'processo_id' => ['required'],
            'profissional_id' => ['required'],
            'funcao' => ['required', 'max:100'],
            'data_inicio' => ['required'],
            'status' => ['required', 'max:50']
        ];
    }

    public function getProfissionaisProcesso($processo_id) {
        try {
            // Sanitiza o input
            $processo_id = $this->sanitizeValue($processo_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT pp.*, p.nome as profissional_nome, p.email, p.telefone,
                           p.especialidade, p.numero_registro
                    FROM {$this->table} pp 
                    LEFT JOIN profissionais p ON pp.profissional_id = p.id 
                    WHERE pp.processo_id = :processo_id 
                    AND pp.deleted_at IS NULL 
                    ORDER BY pp.responsavel_principal DESC, pp.data_inicio DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar profissionais do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getProcessosProfissional($profissional_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT pp.*, p.numero_processo, p.titulo as processo_titulo,
                           p.status as processo_status
                    FROM {$this->table} pp 
                    LEFT JOIN processos p ON pp.processo_id = p.id 
                    WHERE pp.profissional_id = :profissional_id 
                    AND pp.deleted_at IS NULL 
                    ORDER BY pp.data_inicio DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':profissional_id', $profissional_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar processos do profissional: " . $e->getMessage());
            return false;
        }
    }

    public function atribuirProfissional($data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            
            $conn = Database::getInstance();
            
            // Verifica se já existe um responsável principal se este for marcado como tal
            if (isset($data['responsavel_principal']) && $data['responsavel_principal']) {
                $sql = "UPDATE {$this->table} 
                        SET responsavel_principal = 0 
                        WHERE processo_id = :processo_id 
                        AND responsavel_principal = 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':processo_id' => $data['processo_id']]);
            }
            
            // Insere o novo vínculo
            $sql = "INSERT INTO {$this->table} 
                    (processo_id, profissional_id, funcao, data_inicio, 
                     status, responsavel_principal, observacoes) 
                    VALUES 
                    (:processo_id, :profissional_id, :funcao, :data_inicio, 
                     :status, :responsavel_principal, :observacoes)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':profissional_id' => $data['profissional_id'],
                ':funcao' => $data['funcao'],
                ':data_inicio' => $data['data_inicio'],
                ':status' => $data['status'],
                ':responsavel_principal' => $data['responsavel_principal'] ?? 0,
                ':observacoes' => $data['observacoes'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atribuir profissional: " . $e->getMessage());
            return false;
        }
    }

    public function encerrarVinculo($id, $data_fim, $observacoes = null) {
        try {
            // Sanitiza os inputs
            $id = $this->sanitizeValue($id);
            $data_fim = $this->sanitizeValue($data_fim);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET data_fim = :data_fim,
                        status = 'encerrado',
                        observacoes = CASE 
                            WHEN observacoes IS NULL THEN :observacoes
                            ELSE CONCAT(observacoes, ' | ', :observacoes)
                        END
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':data_fim' => $data_fim,
                ':observacoes' => $observacoes
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao encerrar vínculo: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarResponsavelPrincipal($processo_id, $profissional_id) {
        try {
            // Sanitiza os inputs
            $processo_id = $this->sanitizeValue($processo_id);
            $profissional_id = $this->sanitizeValue($profissional_id);
            
            $conn = Database::getInstance();
            
            // Remove o responsável principal atual
            $sql = "UPDATE {$this->table} 
                    SET responsavel_principal = 0 
                    WHERE processo_id = :processo_id 
                    AND responsavel_principal = 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':processo_id' => $processo_id]);
            
            // Define o novo responsável principal
            $sql = "UPDATE {$this->table} 
                    SET responsavel_principal = 1 
                    WHERE processo_id = :processo_id 
                    AND profissional_id = :profissional_id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $processo_id,
                ':profissional_id' => $profissional_id
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar responsável principal: " . $e->getMessage());
            return false;
        }
    }

    public function getResponsavelPrincipal($processo_id) {
        try {
            // Sanitiza o input
            $processo_id = $this->sanitizeValue($processo_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT pp.*, p.nome as profissional_nome, p.email, 
                           p.telefone, p.especialidade, p.numero_registro
                    FROM {$this->table} pp 
                    LEFT JOIN profissionais p ON pp.profissional_id = p.id 
                    WHERE pp.processo_id = :processo_id 
                    AND pp.responsavel_principal = 1 
                    AND pp.deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar responsável principal: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasProfissional($profissional_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(DISTINCT processo_id) as total_processos,
                        COUNT(CASE WHEN responsavel_principal = 1 THEN 1 END) as total_como_responsavel,
                        COUNT(CASE WHEN status = 'ativo' AND data_fim IS NULL THEN 1 END) as processos_ativos,
                        MIN(data_inicio) as primeiro_processo,
                        MAX(data_inicio) as processo_mais_recente
                    FROM {$this->table} 
                    WHERE profissional_id = :profissional_id 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':profissional_id', $profissional_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas do profissional: " . $e->getMessage());
            return false;
        }
    }
}
