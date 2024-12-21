<?php

require_once 'core/config/database.php';

/**
 * Classe Model - Classe base para operações no banco de dados
 * 
 * Esta classe fornece métodos básicos para operações CRUD (Create, Read, Update, Delete)
 * com proteção contra SQL Injection e sanitização de dados.
 * 
 * @package Model
 * @author Marcos Brandão	
 * @since 1.0
 * @version 1.0
 */
class Model {
    protected $table;
    protected $dados;
    
    /**
     * Sanitiza dados de entrada para prevenir SQL Injection e XSS
     * 
     * @param mixed $data Dados a serem sanitizados (string ou array)
     * @return mixed Dados sanitizados
     */
    protected function sanitizeData($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Remove caracteres especiais das chaves
                $cleanKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
                
                // Limpa os valores
                if (is_array($value)) {
                    $data[$cleanKey] = $this->sanitizeData($value);
                } else {
                    $data[$cleanKey] = $this->sanitizeValue($value);
                }
                
                // Remove a chave antiga se foi modificada
                if ($key !== $cleanKey) {
                    unset($data[$key]);
                }
            }
        } else {
            $data = $this->sanitizeValue($data);
        }
        return $data;
    }
    
    /**
     * Sanitiza um valor individual
     * 
     * @param mixed $value Valor a ser sanitizado
     * @return mixed Valor sanitizado
     */
    protected function sanitizeValue($value) {
        if ($value === null) {
            return null;
        }
        
        // Remove tags HTML e PHP
        $value = strip_tags($value);
        
        // Converte caracteres especiais em entidades HTML
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        
        // Remove espaços extras
        $value = trim($value);
        
        return $value;
    }
    
    /**
     * Insere dados em uma tabela
     * 
     * @param string $tabela Nome da tabela
     * @param array $data Array associativo com os dados a serem inseridos
     * @return bool True em caso de sucesso, False em caso de erro
     */
    public function create($data) {
        
        // Sanitiza os dados
        $data = $this->sanitizeData($data);
        
        // Prepara os campos e valores para o INSERT
        $campos = array_keys($data);
        $valores = array_values($data);
        $placeholders = array_fill(0, count($campos), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $campos) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
                
        try {
            $conn = Database::getInstance();
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($valores);
            Database::close();
            return $result;
        } catch(PDOException $e) {
            error_log("Erro na inserção: " . $e->getMessage());
            Database::close();
            return false;
        }
    }
    
    /**
     * Busca todos os registros de uma tabela
     * 
     * @param string $tabela Nome da tabela
     * @param string $data Campos a serem retornados (padrão: "*")
     * @param string $where Condição WHERE (opcional)
     * @param string $order Ordenação (opcional)
     * @return array Registros encontrados
     */
    public function findAll($data = "*", $where = "", $order = "") {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT $data FROM {$this->table}";
            
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            
            if (!empty($order)) {
                $sql .= " ORDER BY $order";
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            Database::close();
            return $result;
        } catch(PDOException $e) {
            error_log("Erro na consulta: " . $e->getMessage());
            Database::close();
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Busca um registro específico pelo ID
     * 
     * @param string $tabela Nome da tabela
     * @param int $id ID do registro
     * @param string $data Campos a serem retornados (padrão: "*")
     * @return array|false Registro encontrado ou false
     */
    public function find($id, $data = "*") {
        try {
            $conn = Database::getInstance();
            $sql = "SELECT $data FROM {$this->table} WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            Database::close();
            return $result;
        } catch(PDOException $e) {
            error_log("Erro na consulta: " . $e->getMessage());
            Database::close();
            return false;
        }
    }
    
    /**
     * Atualiza um registro
     * 
     * @param string $tabela Nome da tabela
     * @param array $data Dados a serem atualizados
     * @param int $id ID do registro
     * @return bool True em caso de sucesso, False em caso de erro
     */
    public function update($data, $id) {
        
        $data = $this->sanitizeData($data);
        
        // Prepara os campos para o UPDATE
        $sets = [];
        $valores = [];
        foreach ($data as $campo => $valor) {
            $sets[] = "{$campo} = ?";
            $valores[] = $valor;
        }
        
        // Adiciona o ID no final do array de valores
        $valores[] = $id;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " 
                WHERE id = ? LIMIT 1";
                
        try {
            $conn = Database::getInstance();
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute($valores);
            Database::close();
            return $result;
        } catch(PDOException $e) {
            error_log("Erro na atualização: " . $e->getMessage());
            Database::close();
            return false;
        }
    }
    
    /**
     * Exclusão lógica de um registro (soft delete)
     * 
     * @param int $id ID do registro
     * @param string $tabela Nome da tabela
     * @return bool True em caso de sucesso, False em caso de erro
     */
    public function delete($id) {
        try {
            $conn = Database::getInstance();
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();
            Database::close();
            return $result;
        } catch(PDOException $e) {
            error_log("Erro na exclusão: " . $e->getMessage());
            Database::close();  
            return false;
        }
    }

    /**
     * Armazena os dados para validação
     * @var array
     */
    private $data = [];

    /**
     * Armazena os erros de validação
     * @var array
     */
    private $errors = [];
    
    /**
     * Regras de validação disponíveis
     * @var array
     */
    private $validationRules = [
        'required' => [
            'rule' => 'validateRequired',
            'message' => 'O campo {field} é obrigatório'
        ],
        'email' => [
            'rule' => 'validateEmail',
            'message' => 'O campo {field} deve ser um e-mail válido'
        ],
        'min_length' => [
            'rule' => 'validateMinLength',
            'message' => 'O campo {field} deve ter no mínimo {param} caracteres'
        ],
        'max_length' => [
            'rule' => 'validateMaxLength',
            'message' => 'O campo {field} deve ter no máximo {param} caracteres'
        ],
        'matches' => [
            'rule' => 'validateMatches',
            'message' => 'O campo {field} deve ser igual ao campo {param}'
        ]
    ];
    
    /**
     * Validação de dados
     * 
     * @param array $data Dados a serem validados
     * @param array $rules Regras de validação
     * @return bool True se válido, False se inválido
     */
    public function validateData($data, $rules) {
        $this->data = $data; // Store the data for validation
        $this->errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $value = isset($data[$field]) ? $data[$field] : null;
            
            // Se for string, converte para array
            if (is_string($ruleSet)) {
                $ruleSet = explode('|', $ruleSet);
            }
            
            foreach ($ruleSet as $rule) {
                $param = null;
                
                // Verifica se a regra tem parâmetro
                if (is_string($rule) && strpos($rule, '[') !== false) {
                    preg_match('/(.+?)\[(.*?)\]/', $rule, $matches);
                    $rule = $matches[1];
                    $param = $matches[2];
                }
                
                // Se for array com mensagem personalizada
                $message = null;
                if (is_array($rule)) {
                    $message = $rule[1];
                    $rule = $rule[0];
                }
                
                // Verifica se a regra existe
                if (isset($this->validationRules[$rule])) {
                    $method = $this->validationRules[$rule]['rule'];
                    if (!$this->$method($value, $param)) {
                        $defaultMessage = $this->validationRules[$rule]['message'];
                        $errorMessage = $message ?? $defaultMessage;
                        $errorMessage = str_replace('{field}', $field, $errorMessage);
                        $errorMessage = str_replace('{param}', $param, $errorMessage);
                        $this->errors[$field][] = $errorMessage;
                    }
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Retorna os erros de validação
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Valida se o campo é obrigatório
     */
    private function validateRequired($value) {
        if (is_array($value)) {
            return !empty($value);
        }
        return trim($value) !== '';
    }
    
    /**
     * Valida se é um email válido
     */
    private function validateEmail($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida o tamanho mínimo
     */
    private function validateMinLength($value, $min) {
        return mb_strlen($value) >= $min;
    }
    
    /**
     * Valida o tamanho máximo
     */
    private function validateMaxLength($value, $max) {
        return mb_strlen($value) <= $max;
    }
    
    /**
     * Valida se um campo é igual a outro
     */
    private function validateMatches($value, $field) {
        return $value === $this->data[$field];
    }

    public function getDataTableData($params, $searchableColumns = [], $orderableColumns = []) {
        try {
            $conn = Database::getInstance();
            
            // Parâmetros do DataTables
            $draw = $params['draw'];
            $start = $params['start'];
            $length = $params['length'];
            $search = $params['search']['value'];
            $orderColumn = isset($params['order'][0]['column']) ? $params['order'][0]['column'] : null;
            $orderDir = isset($params['order'][0]['dir']) ? $params['order'][0]['dir'] : 'ASC';
            
            // Se não houver colunas de busca definidas, usar todas as colunas ordenáveis
            if (empty($searchableColumns)) {
                $searchableColumns = $orderableColumns;
            }
            
            // Construção da query base
            $baseQuery = "FROM {$this->table}";
            $whereClause = "";
            
            // Adiciona busca se houver
            if (!empty($search) && !empty($searchableColumns)) {
                $searchConditions = [];
                foreach ($searchableColumns as $column) {
                    $searchConditions[] = "$column LIKE :search";
                }
                $whereClause = " WHERE (" . implode(" OR ", $searchConditions) . ")";
            }
            
            // Query para contar total de registros
            $totalQuery = "SELECT COUNT(*) as total " . $baseQuery;
            $stmt = $conn->query($totalQuery);
            $recordsTotal = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Query para contar registros filtrados
            $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
            $stmt = $conn->prepare($filteredQuery);
            if (!empty($search) && !empty($searchableColumns)) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }
            $stmt->execute();
            $recordsFiltered = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Definir ordenação
            $orderByClause = "";
            if ($orderColumn !== null && isset($orderableColumns[$orderColumn])) {
                $orderByClause = " ORDER BY " . $orderableColumns[$orderColumn] . " " . $orderDir;
            }
            
            // Query principal
            $mainQuery = "SELECT * " . $baseQuery . $whereClause . $orderByClause . " LIMIT :start, :length";
            
            $stmt = $conn->prepare($mainQuery);
            if (!empty($search) && !empty($searchableColumns)) {
                $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }
            $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
            $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formatação da resposta no padrão do DataTables
            $response = [
                'draw' => (int)$draw,
                'recordsTotal' => (int)$recordsTotal,
                'recordsFiltered' => (int)$recordsFiltered,
                'data' => $data
            ];
            
            Database::close();
            return json_encode($response);
            
        } catch(PDOException $e) {
            error_log("Erro no DataTables: " . $e->getMessage());
            Database::close();
            return json_encode([
                'error' => $e->getMessage(),
                'draw' => (int)$params['draw'],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }
}