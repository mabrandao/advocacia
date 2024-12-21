<?php

require_once 'model/Model.php';

class ProcessoTagModel extends Model {
    
    public function __construct() {
        $this->table = 'processo_tags';
    }

    public function fields() {
        return [
            'id',
            'processo_id',
            'tag',
            'cor',
            'usuario_id'
        ];
    }

    public function rules() {
        return [
            'processo_id' => ['required'],
            'tag' => ['required', 'max:50'],
            'usuario_id' => ['required']
        ];
    }

    public function getTagsProcesso($processo_id) {
        try {
            // Sanitiza o input
            $processo_id = $this->sanitizeValue($processo_id);
            
            $conn = Database::getInstance();
            $sql = "SELECT pt.*, u.nome as usuario_nome 
                    FROM {$this->table} pt 
                    LEFT JOIN usuarios u ON pt.usuario_id = u.id 
                    WHERE pt.processo_id = :processo_id 
                    AND pt.deleted_at IS NULL 
                    ORDER BY pt.tag ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':processo_id', $processo_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar tags do processo: " . $e->getMessage());
            return false;
        }
    }

    public function getProcessosPorTag($tag) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT pt.*, p.numero_processo, p.titulo as processo_titulo,
                           p.status as processo_status 
                    FROM {$this->table} pt 
                    LEFT JOIN processos p ON pt.processo_id = p.id 
                    WHERE pt.tag = :tag 
                    AND pt.deleted_at IS NULL 
                    ORDER BY p.created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tag', $tag);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar processos por tag: " . $e->getMessage());
            return false;
        }
    }

    public function adicionarTag($data) {
        try {
            // Sanitiza os dados
            $data = $this->sanitizeData($data);
            
            $conn = Database::getInstance();
            
            // Verifica se a tag já existe para este processo
            $sql = "SELECT id FROM {$this->table} 
                    WHERE processo_id = :processo_id 
                    AND tag = :tag 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':tag' => $data['tag']
            ]);
            
            if ($stmt->fetch()) {
                return false; // Tag já existe
            }
            
            // Insere a nova tag
            $sql = "INSERT INTO {$this->table} 
                    (processo_id, tag, cor, usuario_id) 
                    VALUES 
                    (:processo_id, :tag, :cor, :usuario_id)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $data['processo_id'],
                ':tag' => $data['tag'],
                ':cor' => $data['cor'] ?? null,
                ':usuario_id' => $data['usuario_id']
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao adicionar tag: " . $e->getMessage());
            return false;
        }
    }

    public function removerTag($processo_id, $tag_id) {
        try {
            // Sanitiza os inputs
            $processo_id = $this->sanitizeValue($processo_id);
            $tag_id = $this->sanitizeValue($tag_id);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET deleted_at = NOW() 
                    WHERE processo_id = :processo_id 
                    AND id = :tag_id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':processo_id' => $processo_id,
                ':tag_id' => $tag_id
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao remover tag: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarCorTag($tag_id, $cor) {
        try {
            // Sanitiza os inputs
            $tag_id = $this->sanitizeValue($tag_id);
            $cor = $this->sanitizeValue($cor);
            
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET cor = :cor 
                    WHERE id = :tag_id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':tag_id' => $tag_id,
                ':cor' => $cor
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar cor da tag: " . $e->getMessage());
            return false;
        }
    }

    public function getTagsMaisUsadas($limite = 10) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT tag, COUNT(*) as total, 
                           GROUP_CONCAT(DISTINCT cor) as cores_usadas 
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    GROUP BY tag 
                    ORDER BY total DESC 
                    LIMIT :limite";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar tags mais usadas: " . $e->getMessage());
            return false;
        }
    }

    public function getTagsSugeridas($processo_id) {
        try {
            // Sanitiza o input
            $processo_id = $this->sanitizeValue($processo_id);
            
            $conn = Database::getInstance();
            
            // Busca área de atuação do processo
            $sql = "SELECT area_atuacao_id FROM processos WHERE id = :processo_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':processo_id' => $processo_id]);
            $area = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$area) {
                return [];
            }
            
            // Busca tags mais usadas em processos da mesma área
            $sql = "SELECT pt.tag, COUNT(*) as total 
                    FROM {$this->table} pt 
                    JOIN processos p ON pt.processo_id = p.id 
                    WHERE p.area_atuacao_id = :area_id 
                    AND pt.deleted_at IS NULL 
                    AND pt.processo_id != :processo_id 
                    GROUP BY pt.tag 
                    ORDER BY total DESC 
                    LIMIT 5";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':area_id' => $area['area_atuacao_id'],
                ':processo_id' => $processo_id
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar tags sugeridas: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasTags() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(DISTINCT tag) as total_tags_distintas,
                        COUNT(*) as total_tags_aplicadas,
                        COUNT(DISTINCT processo_id) as processos_com_tags,
                        COUNT(DISTINCT usuario_id) as usuarios_criaram_tags,
                        AVG(tags_por_processo.total) as media_tags_por_processo
                    FROM {$this->table} pt
                    CROSS JOIN (
                        SELECT processo_id, COUNT(*) as total 
                        FROM {$this->table} 
                        WHERE deleted_at IS NULL 
                        GROUP BY processo_id
                    ) tags_por_processo
                    WHERE pt.deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas das tags: " . $e->getMessage());
            return false;
        }
    }
}
