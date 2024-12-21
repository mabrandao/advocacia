<?php

require_once 'model/Model.php';

class FaturaModel extends Model {
    
    public function __construct() {
        $this->table = 'faturas';
    }

    public function fields() {
        return [
            'id',
            'cliente_id',
            'processo_id',
            'numero_fatura',
            'parcela',
            'total_parcelas',
            'valor',
            'desconto',
            'juros',
            'valor_total',
            'status_pagamento',
            'metodo_pagamento',
            'vencimento',
            'data_pagamento',
            'comprovante_pagamento',
            'observacoes'
        ];
    }

    public function rules() {
        return [
            'cliente_id' => ['required'],
            'processo_id' => ['required'],
            'valor' => ['required'],
            'vencimento' => ['required']
        ];
    }

    public function getFaturasCliente($cliente_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT f.*, p.numero_processo, p.titulo as processo_titulo 
                    FROM {$this->table} f 
                    LEFT JOIN processos p ON f.processo_id = p.id 
                    WHERE f.cliente_id = :cliente_id 
                    AND f.deleted_at IS NULL 
                    ORDER BY f.vencimento DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar faturas do cliente: " . $e->getMessage());
            return false;
        }
    }

    public function getFaturasProcesso($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE processo_id = :processo_id 
                    AND deleted_at IS NULL 
                    ORDER BY vencimento DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar faturas do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getFaturasVencidas() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT f.*, c.nome as cliente_nome, p.numero_processo 
                    FROM {$this->table} f 
                    LEFT JOIN clientes c ON f.cliente_id = c.id 
                    LEFT JOIN processos p ON f.processo_id = p.id 
                    WHERE f.vencimento < CURDATE() 
                    AND f.status_pagamento = 'pendente' 
                    AND f.deleted_at IS NULL 
                    ORDER BY f.vencimento ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar faturas vencidas: " . $e->getMessage());
            return false;
        }
    }

    public function getFaturasAVencer($dias = 30) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT f.*, c.nome as cliente_nome, p.numero_processo 
                    FROM {$this->table} f 
                    LEFT JOIN clientes c ON f.cliente_id = c.id 
                    LEFT JOIN processos p ON f.processo_id = p.id 
                    WHERE f.vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY) 
                    AND f.status_pagamento = 'pendente' 
                    AND f.deleted_at IS NULL 
                    ORDER BY f.vencimento ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar faturas a vencer: " . $e->getMessage());
            return false;
        }
    }

    public function registrarPagamento($fatura_id, $data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            $fatura_id = $this->sanitizeData($fatura_id);

            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET status_pagamento = 'pago',
                        data_pagamento = :data_pagamento,
                        metodo_pagamento = :metodo_pagamento,
                        comprovante_pagamento = :comprovante,
                        observacoes = :observacoes 
                    WHERE id = :fatura_id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':fatura_id' => $fatura_id,
                ':data_pagamento' => $data['data_pagamento'],
                ':metodo_pagamento' => $data['metodo_pagamento'],
                ':comprovante' => $data['comprovante'] ?? null,
                ':observacoes' => $data['observacoes'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao registrar pagamento: " . $e->getMessage());
            return false;
        }
    }

    public function parcelarFatura($fatura_id, $num_parcelas) {
        try {
            // Sanitiza os dados
            $fatura_id = $this->sanitizeData($fatura_id);
            $num_parcelas = $this->sanitizeData($num_parcelas);
            $conn = Database::getInstance();
            
            // Busca a fatura original
            $sql = "SELECT * FROM {$this->table} WHERE id = :fatura_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':fatura_id', $fatura_id);
            $stmt->execute();
            $fatura = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$fatura) {
                return false;
            }
            
            // Calcula valor das parcelas
            $valor_parcela = $fatura['valor_total'] / $num_parcelas;
            
            // Atualiza fatura original para cancelada
            $sql = "UPDATE {$this->table} 
                    SET status_pagamento = 'cancelado' 
                    WHERE id = :fatura_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':fatura_id' => $fatura_id]);
            
            // Cria novas faturas parceladas
            $sql = "INSERT INTO {$this->table} 
                    (cliente_id, processo_id, numero_fatura, parcela, total_parcelas, 
                     valor, valor_total, status_pagamento, vencimento) 
                    VALUES 
                    (:cliente_id, :processo_id, :numero_fatura, :parcela, :total_parcelas,
                     :valor, :valor_total, 'pendente', :vencimento)";
            $stmt = $conn->prepare($sql);
            
            for ($i = 1; $i <= $num_parcelas; $i++) {
                $vencimento = date('Y-m-d', strtotime("+" . ($i-1) . " month", strtotime($fatura['vencimento'])));
                $numero_fatura = $fatura['numero_fatura'] . "/P" . $i;
                
                $stmt->execute([
                    ':cliente_id' => $fatura['cliente_id'],
                    ':processo_id' => $fatura['processo_id'],
                    ':numero_fatura' => $numero_fatura,
                    ':parcela' => $i,
                    ':total_parcelas' => $num_parcelas,
                    ':valor' => $valor_parcela,
                    ':valor_total' => $valor_parcela,
                    ':vencimento' => $vencimento
                ]);
            }
            
            return true;
        } catch(PDOException $e) {
            error_log("Erro ao parcelar fatura: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasPagamento($periodo = 'mes') {
        try {
            $conn = Database::getInstance();
            
            $where = "";
            if ($periodo == 'mes') {
                $where = "AND MONTH(f.vencimento) = MONTH(CURRENT_DATE()) 
                         AND YEAR(f.vencimento) = YEAR(CURRENT_DATE())";
            } elseif ($periodo == 'ano') {
                $where = "AND YEAR(f.vencimento) = YEAR(CURRENT_DATE())";
            }
            
            $sql = "SELECT 
                        COUNT(*) as total_faturas,
                        SUM(CASE WHEN status_pagamento = 'pago' THEN 1 ELSE 0 END) as faturas_pagas,
                        SUM(CASE WHEN status_pagamento = 'pendente' THEN 1 ELSE 0 END) as faturas_pendentes,
                        SUM(valor_total) as valor_total,
                        SUM(CASE WHEN status_pagamento = 'pago' THEN valor_total ELSE 0 END) as valor_recebido,
                        SUM(CASE WHEN status_pagamento = 'pendente' THEN valor_total ELSE 0 END) as valor_pendente
                    FROM {$this->table} f 
                    WHERE deleted_at IS NULL 
                    {$where}";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas de pagamento: " . $e->getMessage());
            return false;
        }
    }
}
