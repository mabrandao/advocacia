<?php

require_once 'model/Model.php';

class DocumentoModel extends Model {
    
    public function __construct() {
        $this->table = 'documentos';
    }

    public function fields() {
        return [
            'id',
            'nome',
            'descricao',
            'arquivo',
            'tipo_documento',
            'processo_id',
            'cliente_id',
            'usuario_id',
            'categoria',
            'tags',
            'tamanho_arquivo',
            'formato'
        ];
    }

    public function rules() {
        return [
            'nome' => ['required', 'max:255'],
            'arquivo' => ['required'],
            'tipo_documento' => ['required']
        ];
    }

    public function getDocumentosCliente($cliente_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT d.*, u.nome as usuario_nome 
                    FROM {$this->table} d 
                    LEFT JOIN usuarios u ON d.usuario_id = u.id 
                    WHERE d.cliente_id = :cliente_id 
                    AND d.deleted_at IS NULL 
                    ORDER BY d.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente_id', $cliente_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos do cliente: " . $e->getMessage());
            return false;
        }
    }

    public function getDocumentosProcesso($processo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT d.*, u.nome as usuario_nome, pd.tipo_documento as tipo_processo, pd.status, pd.versao 
                    FROM {$this->table} d 
                    LEFT JOIN usuarios u ON d.usuario_id = u.id 
                    LEFT JOIN processo_documentos pd ON d.id = pd.documento_id 
                    WHERE d.processo_id = :processo_id 
                    AND d.deleted_at IS NULL 
                    ORDER BY d.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getDocumentosPorCategoria($categoria) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT d.*, u.nome as usuario_nome 
                    FROM {$this->table} d 
                    LEFT JOIN usuarios u ON d.usuario_id = u.id 
                    WHERE d.categoria = :categoria 
                    AND d.deleted_at IS NULL 
                    ORDER BY d.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos por categoria: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorTags($tags) {
        if (!is_array($tags)) {
            $tags = explode(',', $tags);
        }
        
        try {
            $conn = Database::getInstance();
            $placeholders = str_repeat('?,', count($tags) - 1) . '?';
            $sql = "SELECT d.*, u.nome as usuario_nome 
                    FROM {$this->table} d 
                    LEFT JOIN usuarios u ON d.usuario_id = u.id 
                    WHERE d.tags REGEXP CONCAT('(', ?, ')') 
                    AND d.deleted_at IS NULL 
                    ORDER BY d.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $tagPattern = implode('|', array_map('preg_quote', $tags));
            $stmt->execute([$tagPattern]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar documentos por tags: " . $e->getMessage());
            return false;
        }
    }

    public function getVersoes($documento_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT pd.*, u.nome as usuario_upload 
                    FROM processo_documentos pd 
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

    public function atualizarVersao($documento_id, $data) {
        try {
            $conn = Database::getInstance();
            
            // Pega a última versão
            $sql = "SELECT MAX(versao) as ultima_versao 
                    FROM processo_documentos 
                    WHERE documento_id = :documento_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':documento_id', $documento_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $novaVersao = ($result['ultima_versao'] ?? 0) + 1;
            
            // Insere nova versão
            $sql = "INSERT INTO processo_documentos 
                    (processo_id, documento_id, tipo_documento, data_upload, status, versao, hash_arquivo, usuario_upload_id) 
                    VALUES (:processo_id, :documento_id, :tipo_documento, NOW(), :status, :versao, :hash_arquivo, :usuario_upload_id)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':documento_id' => $documento_id,
                ':tipo_documento' => $data['tipo_documento'],
                ':status' => $data['status'],
                ':versao' => $novaVersao,
                ':hash_arquivo' => $data['hash_arquivo'],
                ':usuario_upload_id' => $data['usuario_upload_id']
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar versão do documento: " . $e->getMessage());
            return false;
        }
    }
}
