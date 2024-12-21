<?php

require_once 'model/Model.php';

class AreaAtuacaoModel extends Model {
    
    public function __construct() {
        $this->table = 'areas_atuacao';
    }

    public function fields() {
        return [
            'id',
            'nome',
            'descricao',
            'icone',
            'ativo'
        ];
    }

    public function rules() {
        return [
            'nome' => ['required', 'max:100'],
            'ativo' => ['required']
        ];
    }

    public function getAreasAtivas() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE ativo = 1 
                    AND deleted_at IS NULL 
                    ORDER BY nome ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar áreas ativas: " . $e->getMessage());
            return false;
        }
    }

    public function getAreaComProcessos() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT aa.*, COUNT(p.id) as total_processos 
                    FROM {$this->table} aa 
                    LEFT JOIN processos p ON aa.id = p.area_atuacao_id AND p.deleted_at IS NULL 
                    WHERE aa.ativo = 1 
                    AND aa.deleted_at IS NULL 
                    GROUP BY aa.id 
                    ORDER BY aa.nome ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar áreas com processos: " . $e->getMessage());
            return false;
        }
    }

    public function getProcessosPorArea($area_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT p.*, c.nome as cliente_nome 
                    FROM processos p 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    WHERE p.area_atuacao_id = :area_id 
                    AND p.deleted_at IS NULL 
                    ORDER BY p.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':area_id', $area_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar processos por área: " . $e->getMessage());
            return false;
        }
    }

    public function toggleStatus($id) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET ativo = NOT ativo 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erro ao alterar status da área: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasArea($area_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(p.id) as total_processos,
                        COUNT(CASE WHEN p.status = 'em_andamento' THEN 1 END) as processos_andamento,
                        COUNT(CASE WHEN p.status = 'finalizado' THEN 1 END) as processos_finalizados,
                        SUM(p.valor_causa) as valor_total_causas,
                        AVG(p.valor_causa) as valor_medio_causas
                    FROM {$this->table} aa 
                    LEFT JOIN processos p ON aa.id = p.area_atuacao_id AND p.deleted_at IS NULL 
                    WHERE aa.id = :area_id 
                    AND aa.deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':area_id', $area_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas da área: " . $e->getMessage());
            return false;
        }
    }
}
