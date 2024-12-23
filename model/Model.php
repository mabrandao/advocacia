<?php

require_once __DIR__ . '/../core/config/database.php';
require_once __DIR__ . '/../core/config/session.php';

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

abstract class Model {
    protected $table;
    protected $dados;
    protected $session;
    protected $erro;

    abstract protected function rules();
    abstract protected function fields();
    abstract protected function fields_data();

    public function __construct() {
        $this->session = new Session();
    }
    
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
     * Valida os dados de acordo com as regras definidas
     * @param array $data Dados a serem validados
     * @return array|bool Array com erros ou true se válido
     */
    protected function validate($data) {
        $errors = [];
        $rules = $this->rules();

        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                // Regra required
                if ($rule === 'required' && (!isset($data[$field]) || empty($data[$field]))) {
                    $errors[$field][] = "O campo {$field} é obrigatório";
                    continue;
                }

                // Regra de tamanho máximo
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field][] = "O campo {$field} deve ter no máximo {$max} caracteres";
                    }
                }

                // Regra de tamanho mínimo
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field][] = "O campo {$field} deve ter no mínimo {$min} caracteres";
                    }
                }

                // Regra de email
                if ($rule === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "O campo {$field} deve ser um email válido";
                }

                // Regra de número
                if ($rule === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field][] = "O campo {$field} deve ser um número";
                }

                // Regra de data
                if ($rule === 'date' && isset($data[$field])) {
                    $date = date_parse($data[$field]);
                    if ($date['error_count'] > 0) {
                        $errors[$field][] = "O campo {$field} deve ser uma data válida";
                    }
                }
            }
        }

        if (!empty($errors)) {           
            $this->erro = $errors;
            return false;
        }

        return true;
    }

    /**
     * Insere dados em uma tabela
     * @param array $data Array associativo com os dados a serem inseridos
     * @return bool True em caso de sucesso, false em caso de erro.
     */
    public function create($data) {
        // Valida os dados antes de inserir
        if (!$this->validate($data)) {
            return false;
        }

        // Sanitiza os dados
        $data = $this->sanitizeData($data);
        
        try {
            $conn = Database::getInstance();
            
            // Prepara os campos e valores para o SQL
            $fields = array_keys($data);
            $values = array_values($data);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $conn->prepare($sql);
            
            return $stmt->execute($values);
        } catch(PDOException $e) {
            error_log("Erro na inserção: " . $e->getMessage());
            $this->session->setFlash('error', 'Erro ao inserir registro: ' . $e->getMessage());
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
            $sql = "SELECT $data FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
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
     * @param array $data Dados a serem atualizados
     * @return bool True em caso de sucesso, False em caso de erro
     */
    public function update($data) {

        // Valida os dados antes de atualizar
        if (!$this->validate($data)) {
            return false;
        }

        // Sanitiza os dados
        $data = $this->sanitizeData($data);
        
        // Prepara os campos para o UPDATE
        $sets = [];
        $valores = [];
        $id = $data['id'];
        unset($data['id']);
        foreach ($data as $campo => $valor) {
            $sets[] = "{$campo} = ?";
            $valores[] = $valor;
        }        

        // Limpa os campos
        $valores = array_map(function($value) {
            return $value === null ? 'null' : $value;
        }, $valores);

        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " 
                WHERE id = :id LIMIT 1";
                
        try {
            $conn = Database::getInstance();
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute($valores);
            Database::close();
            return $result;
        } catch(PDOException $e) {
            error_log("Erro na atualização: " . $e->getMessage());
            $this->session->setFlash('error', 'Erro na atualização: ' . $e->getMessage());
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
            $this->erro = "Erro na exclusão: " . $e->getMessage();
            Database::close();  
            return false;
        }
    }
    


    /**
     * Gera os botões de ação para o DataTables
     * 
     * @param int $id ID do registro
     * @param string $baseUrl URL base para as ações
     * @return string HTML dos botões
     */
    protected function generateActionButtons($id, $baseUrl, $exibir) {

        $buttons = '<div class="btn-group">';        
        if (in_array('editar', $exibir)) {
            $buttons .= '<a href="' . base_url() . $baseUrl . '-editar/' . $id . '" ';
            $buttons .= 'class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Editar" title="Editar">';
            $buttons .= '<i class="bi bi-pencil"></i></a>';
        }
        
        if (in_array('excluir', $exibir)) {
            $buttons .= '<a href="' . base_url() . $baseUrl . '-excluir/' . $id . '" ';
            $buttons .= 'class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Excluir" title="Excluir" ';
            $buttons .= 'onclick="return confirm(\'Tem certeza que deseja excluir este registro?\');">';
            $buttons .= '<i class="bi bi-trash"></i></a>';
        }

        if (in_array('visualizar', $exibir)) {
            $buttons .= '<a href="' . base_url() . $baseUrl . '-visualizar/' . $id . '" ';
            $buttons .= 'class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Visualizar" title="Visualizar">';
            $buttons .= '<i class="bi bi-eye"></i></a>';
        }        
        $buttons .= '</div>';
        
        return $buttons;
    }
        
    /**
     * Método genérico para DataTables com suporte a busca, ordenação e paginação
     * 
     * @param array $params Parâmetros do DataTables
     * @return string JSON formatado para DataTables
     * 
     */
    public function getDataTable($params, $exibir = ['editar', 'excluir', 'visualizar']) {
        try {
            $conn = Database::getInstance();
            
            // Parâmetros da requisição
            $draw = $params['draw'] ?? 1;
            $start = $params['start'] ?? 0;
            $length = $params['length'] ?? 10;
            $search = $params['search']['value'] ?? '';
            
            // Campos disponíveis
            $fields = $this->fields_data();
            $selectFields = implode(', ', $fields);
            
            // Ordenação
            $orderColumn = isset($params['order'][0]['column']) ? $params['order'][0]['column'] : 0;
            $orderDir = isset($params['order'][0]['dir']) ? strtoupper($params['order'][0]['dir']) : 'ASC';
            
            // Validação da direção da ordenação
            if (!in_array($orderDir, ['ASC', 'DESC'])) {
                $orderDir = 'ASC';
            }
            
            // Construção da query base
            $baseQuery = "FROM {$this->table}";
            $whereConditions = [];
            $params = [];
            
            // Adiciona condição de busca
            if (!empty($search)) {
                $searchConditions = [];
                foreach ($fields as $field) {
                    $key = 'search_' . $field;
                    $searchConditions[] = "{$field} LIKE :{$key}";
                    $params[$key] = "%{$search}%";
                }
                $whereConditions[] = "(" . implode(" OR ", $searchConditions) . ")";
            }
            
            $whereConditions[] = "(deleted_at IS NULL)";
            
            // Monta cláusula WHERE final
            $whereClause = !empty($whereConditions) ? " WHERE " . implode(" AND ", $whereConditions) : "";
            
            // Query para contar total de registros
            $stmt = $conn->query("SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NULL");
            $totalRecords = $stmt->fetch()['total'];
            
            // Query para contar registros filtrados
            $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
            $stmt = $conn->prepare($filteredQuery);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
            $filteredRecords = $stmt->fetch()['total'];
            
            // Query principal
            $sql = "SELECT {$selectFields} " . $baseQuery . $whereClause;
            
            // Adiciona ordenação
            if (isset($fields[$orderColumn])) {
                $sql .= " ORDER BY " . $fields[$orderColumn] . " " . $orderDir;
            }
            
            // Adiciona limite e offset
            $sql .= " LIMIT :limit OFFSET :offset";
            
            // Prepara e executa a query principal
            $stmt = $conn->prepare($sql);
            
            // Bind dos parâmetros de busca
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            // Bind dos parâmetros de paginação
            $stmt->bindValue(':limit', (int)$length, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$start, PDO::PARAM_INT);
            
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Adiciona os botões de ação
            foreach ($data as &$row) {
                $row['acoes'] = $this->generateActionButtons($row['id'], $this->table, $exibir);
                if (isset($row['creat_at'])) {
                    $row['creat_at'] = date('d/m/Y - H:i', strtotime($row['creat_at']))."Hrs";
                }
                if (isset($row['update_at'])) {
                    $row['update_at'] = date('d/m/Y - H:i', strtotime($row['update_at']))."Hrs";
                }
                if (isset($row['deleted_at'])) {
                    $row['deleted_at'] = date('d/m/Y - H:i', strtotime($row['deleted_at']))."Hrs";
                }

            }
            
            return json_encode([
                'draw' => (int)$draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$filteredRecords,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Retorna o nome do controller atual
     */
    protected function getControllerName() {
        return $this->table;
    }

    public function get_erro() {
        return $this->erro;
    }
}