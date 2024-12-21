<?php

require_once 'model/Model.php';

class ProfissionalModel extends Model {
    
    public function __construct() {
        $this->table = 'profissionais';
    }

    public function fields() {
        return [
            'id',
            'nome',
            'email',
            'telefone',
            'celular',
            'cpf',
            'rg',
            'data_nascimento',
            'especialidade',
            'numero_registro',
            'orgao_registro',
            'data_registro',
            'status',
            'foto',
            'endereco',
            'cidade',
            'estado',
            'cep',
            'observacoes',
            'usuario_id'
        ];
    }

    public function rules() {
        return [
            'nome' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'cpf' => ['required', 'max:14'],
            'especialidade' => ['required', 'max:100'],
            'numero_registro' => ['required', 'max:50'],
            'orgao_registro' => ['required', 'max:50'],
            'status' => ['required', 'max:50']
        ];
    }

    public function getProfissionalPorEmail($email) {
        try {
            // Sanitiza o input
            $email = $this->sanitizeValue($email);
            
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE email = :email 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar profissional por email: " . $e->getMessage());
            return false;
        }
    }

    public function getProfissionalPorRegistro($numero_registro, $orgao_registro) {
        try {
            // Sanitiza os inputs
            $numero_registro = $this->sanitizeValue($numero_registro);
            $orgao_registro = $this->sanitizeValue($orgao_registro);
            
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE numero_registro = :numero_registro 
                    AND orgao_registro = :orgao_registro 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':numero_registro', $numero_registro);
            $stmt->bindParam(':orgao_registro', $orgao_registro);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar profissional por registro: " . $e->getMessage());
            return false;
        }
    }

    public function getProfissionaisPorEspecialidade($especialidade) {
        try {
            // Sanitiza o input
            $especialidade = $this->sanitizeValue($especialidade);
            
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE especialidade = :especialidade 
                    AND status = 'ativo' 
                    AND deleted_at IS NULL 
                    ORDER BY nome ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':especialidade', $especialidade);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar profissionais por especialidade: " . $e->getMessage());
            return false;
        }
    }

    public function getProfissionaisDisponiveis() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT p.*, 
                           COUNT(pp.id) as processos_ativos 
                    FROM {$this->table} p 
                    LEFT JOIN processo_profissionais pp ON p.id = pp.profissional_id 
                    AND pp.status = 'ativo' 
                    AND pp.data_fim IS NULL 
                    WHERE p.status = 'ativo' 
                    AND p.deleted_at IS NULL 
                    GROUP BY p.id 
                    HAVING processos_ativos < 10 
                    ORDER BY processos_ativos ASC, p.nome ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar profissionais disponíveis: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarStatus($id, $status, $observacoes = null) {
        try {
            // Sanitiza os inputs
            $id = $this->sanitizeValue($id);
            $status = $this->sanitizeValue($status);
            $observacoes = $observacoes ? $this->sanitizeValue($observacoes) : null;
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET status = :status, 
                        observacoes = CASE 
                            WHEN observacoes IS NULL THEN :observacoes
                            ELSE CONCAT(observacoes, ' | ', :observacoes)
                        END 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':status' => $status,
                ':observacoes' => $observacoes
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar status do profissional: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarFoto($id, $foto) {
        try {
            // Sanitiza os inputs
            $id = $this->sanitizeValue($id);
            $foto = $this->sanitizeValue($foto);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET foto = :foto 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':foto' => $foto
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar foto do profissional: " . $e->getMessage());
            return false;
        }
    }

    public function buscarProfissionais($termo, $especialidade = null) {
        try {
            // Sanitiza os inputs
            $termo = $this->sanitizeValue($termo);
            $especialidade = $especialidade ? $this->sanitizeValue($especialidade) : null;
            
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE (nome LIKE :termo 
                    OR email LIKE :termo 
                    OR numero_registro LIKE :termo 
                    OR especialidade LIKE :termo) 
                    AND deleted_at IS NULL 
                    ORDER BY nome ASC";
            if ($especialidade) {
                $sql .= " AND especialidade = :especialidade";
            }
            $stmt = $conn->prepare($sql);
            $termo = "%{$termo}%";
            $stmt->bindParam(':termo', $termo);
            if ($especialidade) {
                $stmt->bindParam(':especialidade', $especialidade);
            }
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar profissionais: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasProfissionais() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(*) as total_profissionais,
                        COUNT(CASE WHEN status = 'ativo' THEN 1 END) as ativos,
                        COUNT(CASE WHEN status = 'inativo' THEN 1 END) as inativos,
                        COUNT(DISTINCT especialidade) as total_especialidades,
                        COUNT(DISTINCT orgao_registro) as total_orgaos,
                        AVG(TIMESTAMPDIFF(YEAR, data_registro, CURDATE())) as media_anos_registro
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas dos profissionais: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasProcessos($profissional_id) {
        try {
            // Sanitiza o input
            $profissional_id = $this->sanitizeValue($profissional_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(DISTINCT pp.processo_id) as total_processos,
                        COUNT(CASE WHEN pp.status = 'ativo' AND pp.data_fim IS NULL THEN 1 END) as processos_ativos,
                        COUNT(CASE WHEN pp.responsavel_principal = 1 THEN 1 END) as como_responsavel_principal,
                        MIN(pp.data_inicio) as primeiro_processo,
                        MAX(pp.data_inicio) as processo_mais_recente,
                        COUNT(DISTINCT p.area_atuacao_id) as areas_atuacao_distintas
                    FROM processo_profissionais pp 
                    LEFT JOIN processos p ON pp.processo_id = p.id 
                    WHERE pp.profissional_id = :profissional_id 
                    AND pp.deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':profissional_id', $profissional_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas de processos do profissional: " . $e->getMessage());
            return false;
        }
    }

    public function getEspecialidades() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT DISTINCT especialidade, 
                           COUNT(*) as total_profissionais 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    GROUP BY especialidade 
                    ORDER BY total_profissionais DESC, especialidade ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar especialidades: " . $e->getMessage());
            return false;
        }
    }
}
