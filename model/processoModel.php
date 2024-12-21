<?php

require_once 'model/Model.php';

class ProcessoModel extends Model {
    
    public function __construct() {
        $this->table = 'processos';
    }

    public function fields() {
        return [
            'id',
            'cliente_id',
            'area_atuacao_id',
            'numero_processo',
            'titulo',
            'descricao',
            'valor_causa',
            'honorarios',
            'status',
            'prioridade',
            'data_distribuicao',
            'data_conclusao',
            'comarca',
            'vara',
            'juiz'
        ];
    }

    public function rules() {
        return [
            'cliente_id' => ['required'],
            'area_atuacao_id' => ['required'],
            'titulo' => ['required', 'max:255'],
            'valor_causa' => ['required'],
            'honorarios' => ['required']
        ];
    }

    public function getProcessosCliente($cliente_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT p.*, aa.nome as area_atuacao 
                    FROM {$this->table} p 
                    LEFT JOIN areas_atuacao aa ON p.area_atuacao_id = aa.id 
                    WHERE p.cliente_id = :cliente_id 
                    AND p.deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar processos do cliente: " . $e->getMessage());
            return false;
        }
    }

    public function getProcessosAdvogado($advogado_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT p.*, aa.nome as area_atuacao, c.nome as cliente_nome 
                    FROM {$this->table} p 
                    LEFT JOIN areas_atuacao aa ON p.area_atuacao_id = aa.id 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    INNER JOIN processo_profissionais pp ON p.id = pp.processo_id 
                    WHERE pp.profissional_id = :advogado_id 
                    AND p.deleted_at IS NULL 
                    AND pp.deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':advogado_id', $advogado_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar processos do advogado: " . $e->getMessage());
            return false;
        }
    }

    public function getProcessoCompleto($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT p.*, 
                           aa.nome as area_atuacao,
                           c.nome as cliente_nome,
                           GROUP_CONCAT(DISTINCT prof.nome) as advogados,
                           COUNT(DISTINCT d.id) as total_documentos,
                           COUNT(DISTINCT a.id) as total_andamentos
                    FROM {$this->table} p 
                    LEFT JOIN areas_atuacao aa ON p.area_atuacao_id = aa.id 
                    LEFT JOIN clientes c ON p.cliente_id = c.id 
                    LEFT JOIN processo_profissionais pp ON p.id = pp.processo_id 
                    LEFT JOIN profissionais prof ON pp.profissional_id = prof.id
                    LEFT JOIN processo_documentos pd ON p.id = pd.processo_id
                    LEFT JOIN documentos d ON pd.documento_id = d.id
                    LEFT JOIN andamentos_processo a ON p.id = a.processo_id
                    WHERE p.id = :processo_id 
                    AND p.deleted_at IS NULL
                    GROUP BY p.id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar processo completo: " . $e->getMessage());
            return false;
        }
    }

    public function getAndamentos($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT ap.*, u.nome as usuario_nome 
                    FROM andamentos_processo ap 
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

    public function addAndamento($data) {
        try {
            $conn = Database::getInstance();
            $sql = "INSERT INTO andamentos_processo 
                    (processo_id, data_andamento, tipo_andamento, descricao, usuario_id, arquivo_anexo) 
                    VALUES (:processo_id, :data_andamento, :tipo_andamento, :descricao, :usuario_id, :arquivo_anexo)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':data_andamento' => $data['data_andamento'],
                ':tipo_andamento' => $data['tipo_andamento'],
                ':descricao' => $data['descricao'],
                ':usuario_id' => $data['usuario_id'],
                ':arquivo_anexo' => $data['arquivo_anexo'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao adicionar andamento: " . $e->getMessage());
            return false;
        }
    }

    public function getDocumentos($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT d.*, pd.tipo_documento, pd.status, pd.versao,
                           u.nome as usuario_upload 
                    FROM processo_documentos pd 
                    INNER JOIN documentos d ON pd.documento_id = d.id 
                    LEFT JOIN usuarios u ON pd.usuario_upload_id = u.id 
                    WHERE pd.processo_id = :processo_id 
                    AND pd.deleted_at IS NULL 
                    ORDER BY pd.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos do processo: " . $e->getMessage());
            return false;
        }
    }

    public function addDocumento($data) {
        try {
            $conn = Database::getInstance();
            
            // Primeiro insere o documento
            $sql1 = "INSERT INTO documentos 
                    (nome, descricao, arquivo, tipo_documento, processo_id, categoria, tags, tamanho_arquivo, formato) 
                    VALUES (:nome, :descricao, :arquivo, :tipo_documento, :processo_id, :categoria, :tags, :tamanho_arquivo, :formato)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute([
                ':nome' => $data['nome'],
                ':descricao' => $data['descricao'],
                ':arquivo' => $data['arquivo'],
                ':tipo_documento' => $data['tipo_documento'],
                ':processo_id' => $data['processo_id'],
                ':categoria' => $data['categoria'],
                ':tags' => $data['tags'],
                ':tamanho_arquivo' => $data['tamanho_arquivo'],
                ':formato' => $data['formato']
            ]);
            
            $documento_id = $conn->lastInsertId();
            
            // Depois insere a relação processo_documentos
            $sql2 = "INSERT INTO processo_documentos 
                    (processo_id, documento_id, tipo_documento, data_upload, status, versao, hash_arquivo, usuario_upload_id) 
                    VALUES (:processo_id, :documento_id, :tipo_documento, NOW(), :status, :versao, :hash_arquivo, :usuario_upload_id)";
            $stmt2 = $conn->prepare($sql2);
            return $stmt2->execute([
                ':processo_id' => $data['processo_id'],
                ':documento_id' => $documento_id,
                ':tipo_documento' => $data['tipo_documento'],
                ':status' => $data['status'],
                ':versao' => 1,
                ':hash_arquivo' => $data['hash_arquivo'],
                ':usuario_upload_id' => $data['usuario_upload_id']
            ]);
            
        } catch(PDOException $e) {
            error_log("Erro ao adicionar documento: " . $e->getMessage());
            return false;
        }
    }
}
