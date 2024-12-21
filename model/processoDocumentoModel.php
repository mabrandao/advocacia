<?php

require_once 'model/Model.php';

class ProcessoDocumentoModel extends Model {
    
    public function __construct() {
        $this->table = 'processo_documentos';
    }

    public function fields() {
        return [
            'id',
            'processo_id',
            'documento_id',
            'tipo_documento',
            'data_upload',
            'status',
            'observacoes',
            'versao',
            'hash_arquivo',
            'tamanho_arquivo',
            'usuario_upload_id'
        ];
    }

    public function rules() {
        return [
            'processo_id' => ['required'],
            'documento_id' => ['required'],
            'tipo_documento' => ['required', 'max:50'],
            'data_upload' => ['required'],
            'status' => ['required', 'max:50'],
            'versao' => ['required'],
            'usuario_upload_id' => ['required']
        ];
    }

    public function getDocumentosProcesso($processo_id) {
        try {
            // Sanitiza o input
            $processo_id = $this->sanitizeValue($processo_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT pd.*, d.nome as documento_nome, d.arquivo, 
                           u.nome as usuario_nome 
                    FROM {$this->table} pd 
                    LEFT JOIN documentos d ON pd.documento_id = d.id 
                    LEFT JOIN usuarios u ON pd.usuario_upload_id = u.id 
                    WHERE pd.processo_id = :processo_id 
                    AND pd.deleted_at IS NULL 
                    ORDER BY pd.data_upload DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getDocumentosPorTipo($processo_id, $tipo_documento) {
        try {
            // Sanitiza os inputs
            $processo_id = $this->sanitizeValue($processo_id);
            $tipo_documento = $this->sanitizeValue($tipo_documento);
            
            $conn = Database::getInstance();
            $sql = "SELECT pd.*, d.nome as documento_nome, d.arquivo, 
                           u.nome as usuario_nome 
                    FROM {$this->table} pd 
                    LEFT JOIN documentos d ON pd.documento_id = d.id 
                    LEFT JOIN usuarios u ON pd.usuario_upload_id = u.id 
                    WHERE pd.processo_id = :processo_id 
                    AND pd.tipo_documento = :tipo_documento 
                    AND pd.deleted_at IS NULL 
                    ORDER BY pd.versao DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->bindParam(':tipo_documento', $tipo_documento);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos por tipo: " . $e->getMessage());
            return false;
        }
    }

    public function getVersoesDocumento($documento_id) {
        try {
            // Sanitiza o input
            $documento_id = $this->sanitizeValue($documento_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT pd.*, u.nome as usuario_nome 
                    FROM {$this->table} pd 
                    LEFT JOIN usuarios u ON pd.usuario_upload_id = u.id 
                    WHERE pd.documento_id = :documento_id 
                    AND pd.deleted_at IS NULL 
                    ORDER BY pd.versao DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':documento_id', $documento_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar versões do documento: " . $e->getMessage());
            return false;
        }
    }

    public function adicionarDocumento($data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            
            $conn = Database::getInstance();
            
            // Busca a última versão
            $sql = "SELECT MAX(versao) as ultima_versao 
                    FROM {$this->table} 
                    WHERE processo_id = :processo_id 
                    AND tipo_documento = :tipo_documento";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':tipo_documento' => $data['tipo_documento']
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $versao = ($result['ultima_versao'] ?? 0) + 1;
            
            // Insere o novo documento
            $sql = "INSERT INTO {$this->table} 
                    (processo_id, documento_id, tipo_documento, data_upload, 
                     status, observacoes, versao, hash_arquivo, 
                     tamanho_arquivo, usuario_upload_id) 
                    VALUES 
                    (:processo_id, :documento_id, :tipo_documento, NOW(), 
                     :status, :observacoes, :versao, :hash_arquivo, 
                     :tamanho_arquivo, :usuario_upload_id)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':documento_id' => $data['documento_id'],
                ':tipo_documento' => $data['tipo_documento'],
                ':status' => $data['status'],
                ':observacoes' => $data['observacoes'] ?? null,
                ':versao' => $versao,
                ':hash_arquivo' => $data['hash_arquivo'],
                ':tamanho_arquivo' => $data['tamanho_arquivo'],
                ':usuario_upload_id' => $data['usuario_upload_id']
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao adicionar documento: " . $e->getMessage());
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
                        observacoes = :observacoes 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':status' => $status,
                ':observacoes' => $observacoes
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar status do documento: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasDocumentos($processo_id) {
        try {
            // Sanitiza o input
            $processo_id = $this->sanitizeValue($processo_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(*) as total_documentos,
                        COUNT(DISTINCT tipo_documento) as tipos_distintos,
                        SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
                        SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                        MAX(versao) as maior_versao,
                        MAX(data_upload) as ultimo_upload
                    FROM {$this->table} 
                    WHERE processo_id = :processo_id 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas dos documentos: " . $e->getMessage());
            return false;
        }
    }
}
