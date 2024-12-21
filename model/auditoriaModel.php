<?php

require_once 'model/Model.php';

class AuditoriaModel extends Model {
    
    public function __construct() {
        $this->table = 'auditoria';
    }

    public function fields() {
        return [
            'id',
            'usuario_id',
            'tabela',
            'acao',
            'dados',
            'data_hora'
        ];
    }

    public function rules() {
        return [
            'usuario_id' => ['required'],
            'tabela' => ['required', 'max:100'],
            'acao' => ['required', 'max:255']
        ];
    }

    public function registrarAcao($usuario_id, $tabela, $acao, $dados = null) {
        try {
            $conn = Database::getInstance();
            $sql = "INSERT INTO {$this->table} 
                    (usuario_id, tabela, acao, dados) 
                    VALUES (:usuario_id, :tabela, :acao, :dados)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':tabela' => $tabela,
                ':acao' => $acao,
                ':dados' => is_array($dados) ? json_encode($dados) : $dados
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao registrar ação na auditoria: " . $e->getMessage());
            return false;
        }
    }

    public function getAcoesPorUsuario($usuario_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, u.nome as usuario_nome 
                    FROM {$this->table} a 
                    LEFT JOIN usuarios u ON a.usuario_id = u.id 
                    WHERE a.usuario_id = :usuario_id 
                    ORDER BY a.data_hora DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar ações do usuário: " . $e->getMessage());
            return false;
        }
    }

    public function getAcoesPorTabela($tabela) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, u.nome as usuario_nome 
                    FROM {$this->table} a 
                    LEFT JOIN usuarios u ON a.usuario_id = u.id 
                    WHERE a.tabela = :tabela 
                    ORDER BY a.data_hora DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tabela', $tabela);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar ações da tabela: " . $e->getMessage());
            return false;
        }
    }

    public function getAcoesPorPeriodo($dataInicio, $dataFim) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, u.nome as usuario_nome 
                    FROM {$this->table} a 
                    LEFT JOIN usuarios u ON a.usuario_id = u.id 
                    WHERE DATE(a.data_hora) BETWEEN :data_inicio AND :data_fim 
                    ORDER BY a.data_hora DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':data_inicio', $dataInicio);
            $stmt->bindParam(':data_fim', $dataFim);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar ações por período: " . $e->getMessage());
            return false;
        }
    }

    public function getUltimasAcoes($limite = 100) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT a.*, u.nome as usuario_nome 
                    FROM {$this->table} a 
                    LEFT JOIN usuarios u ON a.usuario_id = u.id 
                    ORDER BY a.data_hora DESC 
                    LIMIT :limite";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar últimas ações: " . $e->getMessage());
            return false;
        }
    }

    public function limparAuditoriasAntigas($dias = 90) {
        try {
            $conn = Database::getInstance();
            $sql = "DELETE FROM {$this->table} 
                    WHERE data_hora < DATE_SUB(NOW(), INTERVAL :dias DAY)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao limpar auditorias antigas: " . $e->getMessage());
            return false;
        }
    }
}
