<?php

require_once 'model/Model.php';

class SolicitacaoModel extends Model {
    
    public function __construct() {
        $this->table = 'solicitacoes';
    }

    public function fields() {
        return [
            'id',
            'usuario_solicitante_id',
            'tipo_solicitacao',
            'descricao',
            'status',
            'prioridade',
            'data_limite',
            'processo_id',
            'usuario_responsavel_id',
            'data_conclusao',
            'observacoes'
        ];
    }

    public function rules() {
        return [
            'usuario_solicitante_id' => ['required'],
            'tipo_solicitacao' => ['required', 'max:100'],
            'descricao' => ['required'],
            'status' => ['required', 'max:50'],
            'prioridade' => ['required', 'max:50']
        ];
    }

    public function criarSolicitacao($data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            
            $conn = Database::getInstance();
            $sql = "INSERT INTO {$this->table} 
                    (usuario_solicitante_id, tipo_solicitacao, descricao, 
                     status, prioridade, data_limite, processo_id, 
                     usuario_responsavel_id, observacoes) 
                    VALUES 
                    (:usuario_solicitante_id, :tipo_solicitacao, :descricao, 
                     :status, :prioridade, :data_limite, :processo_id, 
                     :usuario_responsavel_id, :observacoes)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':usuario_solicitante_id' => $data['usuario_solicitante_id'],
                ':tipo_solicitacao' => $data['tipo_solicitacao'],
                ':descricao' => $data['descricao'],
                ':status' => $data['status'] ?? 'pendente',
                ':prioridade' => $data['prioridade'],
                ':data_limite' => $data['data_limite'] ?? null,
                ':processo_id' => $data['processo_id'] ?? null,
                ':usuario_responsavel_id' => $data['usuario_responsavel_id'] ?? null,
                ':observacoes' => $data['observacoes'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao criar solicitação: " . $e->getMessage());
            return false;
        }
    }

    public function getSolicitacoesUsuario($usuario_id, $tipo = 'solicitante') {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT s.*, 
                           us.nome as solicitante_nome,
                           ur.nome as responsavel_nome,
                           p.numero_processo,
                           p.titulo as processo_titulo
                    FROM {$this->table} s 
                    LEFT JOIN usuarios us ON s.usuario_solicitante_id = us.id 
                    LEFT JOIN usuarios ur ON s.usuario_responsavel_id = ur.id 
                    LEFT JOIN processos p ON s.processo_id = p.id 
                    WHERE ";
            
            if ($tipo == 'solicitante') {
                $sql .= "s.usuario_solicitante_id = :usuario_id";
            } else {
                $sql .= "s.usuario_responsavel_id = :usuario_id";
            }
            
            $sql .= " AND s.deleted_at IS NULL 
                    ORDER BY s.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar solicitações do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function getSolicitacoesProcesso($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT s.*, 
                           us.nome as solicitante_nome,
                           ur.nome as responsavel_nome
                    FROM {$this->table} s 
                    LEFT JOIN usuarios us ON s.usuario_solicitante_id = us.id 
                    LEFT JOIN usuarios ur ON s.usuario_responsavel_id = ur.id 
                    WHERE s.processo_id = :processo_id 
                    AND s.deleted_at IS NULL 
                    ORDER BY s.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar solicitações do processo: " . $e->getMessage());
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
                        data_conclusao = CASE 
                            WHEN :status = 'concluido' THEN NOW() 
                            ELSE NULL 
                        END,
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
            error_log("Erro ao atualizar status da solicitação: " . $e->getMessage());
            return false;
        }
    }

    public function atribuirResponsavel($id, $usuario_responsavel_id) {
        try {
            // Sanitiza os inputs
            $id = $this->sanitizeValue($id);
            $usuario_responsavel_id = $this->sanitizeValue($usuario_responsavel_id);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET usuario_responsavel_id = :usuario_responsavel_id 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':usuario_responsavel_id' => $usuario_responsavel_id
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atribuir responsável: " . $e->getMessage());
            return false;
        }
    }

    public function getSolicitacoesPendentes() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT s.*, 
                           us.nome as solicitante_nome,
                           ur.nome as responsavel_nome,
                           p.numero_processo,
                           p.titulo as processo_titulo
                    FROM {$this->table} s 
                    LEFT JOIN usuarios us ON s.usuario_solicitante_id = us.id 
                    LEFT JOIN usuarios ur ON s.usuario_responsavel_id = ur.id 
                    LEFT JOIN processos p ON s.processo_id = p.id 
                    WHERE s.status = 'pendente' 
                    AND s.deleted_at IS NULL 
                    ORDER BY s.prioridade DESC, s.created_at ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar solicitações pendentes: " . $e->getMessage());
            return false;
        }
    }

    public function getSolicitacoesVencidas() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT s.*, 
                           us.nome as solicitante_nome,
                           ur.nome as responsavel_nome,
                           p.numero_processo,
                           p.titulo as processo_titulo
                    FROM {$this->table} s 
                    LEFT JOIN usuarios us ON s.usuario_solicitante_id = us.id 
                    LEFT JOIN usuarios ur ON s.usuario_responsavel_id = ur.id 
                    LEFT JOIN processos p ON s.processo_id = p.id 
                    WHERE s.status != 'concluido' 
                    AND s.data_limite < CURDATE() 
                    AND s.deleted_at IS NULL 
                    ORDER BY s.data_limite ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar solicitações vencidas: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasSolicitacoes($periodo = 'mes') {
        try {
            $conn = Database::getInstance();
            
            $where = "";
            switch($periodo) {
                case 'dia':
                    $where = "AND DATE(s.created_at) = CURDATE()";
                    break;
                case 'semana':
                    $where = "AND YEARWEEK(s.created_at) = YEARWEEK(NOW())";
                    break;
                case 'mes':
                    $where = "AND MONTH(s.created_at) = MONTH(NOW()) AND YEAR(s.created_at) = YEAR(NOW())";
                    break;
            }
            
            $sql = "SELECT 
                        COUNT(*) as total_solicitacoes,
                        COUNT(CASE WHEN status = 'pendente' THEN 1 END) as pendentes,
                        COUNT(CASE WHEN status = 'em_andamento' THEN 1 END) as em_andamento,
                        COUNT(CASE WHEN status = 'concluido' THEN 1 END) as concluidas,
                        COUNT(CASE WHEN status != 'concluido' AND data_limite < CURDATE() THEN 1 END) as vencidas,
                        AVG(CASE 
                            WHEN status = 'concluido' 
                            THEN TIMESTAMPDIFF(HOUR, created_at, data_conclusao) 
                        END) as media_tempo_conclusao
                    FROM {$this->table} s 
                    WHERE s.deleted_at IS NULL 
                    {$where}";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas das solicitações: " . $e->getMessage());
            return false;
        }
    }
}
