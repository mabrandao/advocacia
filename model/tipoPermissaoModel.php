<?php

require_once 'model/Model.php';

class TipoPermissaoModel extends Model {
    
    public function __construct() {
        $this->table = 'tipos_permissao';
    }

    public function fields() {
        return [
            'id',
            'nome',
            'descricao',
            'nivel_acesso',
            'modulos_permitidos',
            'acoes_permitidas',
            'status'
        ];
    }

    public function rules() {
        return [
            'nome' => ['required', 'max:100'],
            'nivel_acesso' => ['required', 'integer'],
            'status' => ['required', 'max:50']
        ];
    }

    public function getTiposAtivos() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE status = 'ativo' 
                    AND deleted_at IS NULL 
                    ORDER BY nivel_acesso DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar tipos de permissão ativos: " . $e->getMessage());
            return false;
        }
    }

    public function getTipoPorNivel($nivel_acesso) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT * FROM {$this->table} 
                    WHERE nivel_acesso = :nivel_acesso 
                    AND status = 'ativo' 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nivel_acesso', $nivel_acesso);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar tipo de permissão por nível: " . $e->getMessage());
            return false;
        }
    }

    public function verificarPermissao($tipo_id, $modulo, $acao) {
        try {
            //sanitize inputs
            $tipo_id = $this->sanitizeValue($tipo_id);
            $modulo = $this->sanitizeValue($modulo);
            $acao = $this->sanitizeValue($acao);

            $conn = Database::getInstance();
            $sql = "SELECT modulos_permitidos, acoes_permitidas 
                    FROM {$this->table} 
                    WHERE id = :tipo_id 
                    AND status = 'ativo' 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tipo_id', $tipo_id);
            $stmt->execute();
            
            $permissao = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$permissao) {
                return false;
            }
            
            $modulos = json_decode($permissao['modulos_permitidos'], true);
            $acoes = json_decode($permissao['acoes_permitidas'], true);
            
            // Verifica se o módulo está permitido
            if (!in_array($modulo, $modulos)) {
                return false;
            }
            
            // Verifica se a ação está permitida
            if (!isset($acoes[$modulo]) || !in_array($acao, $acoes[$modulo])) {
                return false;
            }
            
            return true;
        } catch(PDOException $e) {
            error_log("Erro ao verificar permissão: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarModulosPermitidos($id, $modulos) {
        try {
            //sanitize inputs
            $id = $this->sanitizeValue($id);
            $modulos = $this->sanitizeValue($modulos);

            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET modulos_permitidos = :modulos 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':modulos' => json_encode($modulos)
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar módulos permitidos: " . $e->getMessage());
            return false;
        }
    }

    public function atualizarAcoesPermitidas($id, $acoes) {
        try {
            //sanitize inputs
            $id = $this->sanitizeValue($id);
            $acoes = $this->sanitizeValue($acoes);

            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} 
                    SET acoes_permitidas = :acoes 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':acoes' => json_encode($acoes)
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar ações permitidas: " . $e->getMessage());
            return false;
        }
    }

    public function getModulosPermitidos($tipo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT modulos_permitidos 
                    FROM {$this->table} 
                    WHERE id = :tipo_id 
                    AND status = 'ativo' 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tipo_id', $tipo_id);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? json_decode($resultado['modulos_permitidos'], true) : [];
        } catch(PDOException $e) {
            error_log("Erro ao buscar módulos permitidos: " . $e->getMessage());
            return false;
        }
    }

    public function getAcoesPermitidas($tipo_id) {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT acoes_permitidas 
                    FROM {$this->table} 
                    WHERE id = :tipo_id 
                    AND status = 'ativo' 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':tipo_id', $tipo_id);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? json_decode($resultado['acoes_permitidas'], true) : [];
        } catch(PDOException $e) {
            error_log("Erro ao buscar ações permitidas: " . $e->getMessage());
            return false;
        }
    }

    public function criarTipoPermissao($data) {
        try {
            //sanitize inputs
            $data = $this->sanitizeData($data);
            $conn = Database::getInstance();
            $sql = "INSERT INTO {$this->table} 
                    (nome, descricao, nivel_acesso, modulos_permitidos, 
                     acoes_permitidas, status) 
                    VALUES 
                    (:nome, :descricao, :nivel_acesso, :modulos_permitidos, 
                     :acoes_permitidas, :status)";
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute([
                ':nome' => $data['nome'],
                ':descricao' => $data['descricao'] ?? null,
                ':nivel_acesso' => $data['nivel_acesso'],
                ':modulos_permitidos' => json_encode($data['modulos_permitidos'] ?? []),
                ':acoes_permitidas' => json_encode($data['acoes_permitidas'] ?? []),
                ':status' => $data['status'] ?? 'ativo'
            ]);
        } catch(PDOException $e) {
            error_log("Erro ao criar tipo de permissão: " . $e->getMessage());
            return false;
        }
    }

    public function getHierarquiaPermissoes() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT id, nome, nivel_acesso, 
                           (SELECT COUNT(*) 
                            FROM usuarios 
                            WHERE tipo_permissao_id = tp.id 
                            AND deleted_at IS NULL) as total_usuarios 
                    FROM {$this->table} tp 
                    WHERE status = 'ativo' 
                    AND deleted_at IS NULL 
                    ORDER BY nivel_acesso DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar hierarquia de permissões: " . $e->getMessage());
            return false;
        }
    }

    public function getEstatisticasPermissoes() {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT 
                        COUNT(*) as total_tipos,
                        COUNT(DISTINCT nivel_acesso) as niveis_distintos,
                        MAX(nivel_acesso) as nivel_maximo,
                        MIN(nivel_acesso) as nivel_minimo,
                        (SELECT COUNT(DISTINCT modulo) 
                         FROM (
                             SELECT m.modulo 
                             FROM {$this->table} tp,
                             JSON_TABLE(tp.modulos_permitidos, '$[*]' COLUMNS (modulo VARCHAR(100) PATH '$')) m
                             WHERE tp.status = 'ativo' 
                             AND tp.deleted_at IS NULL
                         ) modulos) as total_modulos_sistema
                    FROM {$this->table} 
                    WHERE status = 'ativo' 
                    AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas das permissões: " . $e->getMessage());
            return false;
        }
    }
}
