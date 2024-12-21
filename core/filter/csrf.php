<?php

/**
 * Classe para proteção contra ataques CSRF (Cross-Site Request Forgery)
 * A validação é feita automaticamente para todas as requisições POST
 */
class CSRF {
    /**
     * Nome do token CSRF na sessão e no formulário
     */
    const TOKEN_NAME = 'csrf_token';
    
    /**
     * Tempo de expiração do token em segundos (30 minutos)
     */
    const TOKEN_LIFETIME = 1800;

    /**
     * URLs que não precisam de validação CSRF
     */
    private static $excludedPaths = [
        '/api/webhook', // exemplo de URL excluída
        '/api/external' // exemplo de URL excluída
    ];

    /**
     * Construtor que inicia a validação automática
     */
    public function __construct() {
        // Inicia a sessão se ainda não foi iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Executa a validação automática para requisições POST
        $this->validateRequest();
    }

    /**
     * Valida automaticamente a requisição
     */
    private function validateRequest() {
        // Se não for POST, não precisa validar
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        // Verifica se a URL atual está na lista de exclusão
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (in_array($currentPath, self::$excludedPaths)) {
            return;
        }

        // Se for AJAX, valida com o token do header
        if (self::isAjax()) {
            if (!self::checkAjax()) {
                self::handleError('Token CSRF inválido na requisição AJAX');
            }
            return;
        }

        // Valida o token do formulário
        if (!self::check()) {
            self::handleError('Token CSRF inválido no formulário');
        }
    }

    /**
     * Gera um novo token CSRF
     * 
     * @return string Token gerado
     */
    public static function generateToken() {
        // Gera um token aleatório
        $token = bin2hex(random_bytes(32));
        
        // Armazena o token e o timestamp na sessão
        $_SESSION[self::TOKEN_NAME] = [
            'token' => $token,
            'timestamp' => time()
        ];
        
        return $token;
    }

    /**
     * Gera o campo hidden com o token CSRF para o formulário
     * 
     * @return string HTML do campo hidden
     */
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $token . '">';
    }

    /**
     * Verifica se o token CSRF é válido
     * 
     * @return bool True se o token for válido, False caso contrário
     */
    public static function check() {
        // Verifica se existe token na sessão
        if (!isset($_SESSION[self::TOKEN_NAME])) {
            self::logError('Token CSRF não encontrado na sessão');
            return false;
        }

        // Obtém dados do token da sessão
        $sessionToken = $_SESSION[self::TOKEN_NAME]['token'];
        $timestamp = $_SESSION[self::TOKEN_NAME]['timestamp'];

        // Verifica se o token expirou
        if (time() - $timestamp > self::TOKEN_LIFETIME) {
            self::logError('Token CSRF expirado');
            return false;
        }

        // Verifica se o token foi enviado no POST
        if (!isset($_POST[self::TOKEN_NAME])) {
            self::logError('Token CSRF não encontrado no POST');
            return false;
        }

        // Verifica se o token do POST corresponde ao da sessão
        if (!hash_equals($sessionToken, $_POST[self::TOKEN_NAME])) {
            self::logError('Token CSRF inválido');
            return false;
        }

        // Remove o token usado da sessão
        unset($_SESSION[self::TOKEN_NAME]);
        
        return true;
    }

    /**
     * Trata os erros de validação CSRF
     * 
     * @param string $message Mensagem de erro
     */
    private static function handleError($message) {
        self::logError($message);
        
        // Se for uma requisição AJAX, retorna erro em JSON
        if (self::isAjax()) {
            header('Content-Type: application/json');
            http_response_code(403);
            die(json_encode(['error' => $message]));
        }
        
        // Se for uma requisição normal, redireciona para página de erro
        http_response_code(403);
        die('Erro de validação CSRF: ' . $message);
    }

    /**
     * Registra erros de CSRF no log
     * 
     * @param string $message Mensagem de erro
     */
    private static function logError($message) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'message' => $message,
            'request_uri' => $_SERVER['REQUEST_URI']
        ];

        // Registra no arquivo de log
        error_log(
            json_encode($logData) . "\n",
            3,
            dirname(__FILE__) . '/../../logs/csrf_errors.log'
        );
    }

    /**
     * Verifica se é uma requisição AJAX
     * 
     * @return bool True se for AJAX, False caso contrário
     */
    private static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Retorna cabeçalho CSRF para requisições AJAX
     * 
     * @return string Token CSRF para usar no header X-CSRF-Token
     */
    public static function getAjaxToken() {
        $token = self::generateToken();
        return $token;
    }

    /**
     * Verifica token CSRF em requisições AJAX
     * 
     * @return bool True se o token for válido, False caso contrário
     */
    private static function checkAjax() {
        $headers = getallheaders();
        if (!isset($headers['X-CSRF-Token'])) {
            self::logError('Token CSRF não encontrado no header da requisição AJAX');
            return false;
        }

        if (!isset($_SESSION[self::TOKEN_NAME]['token'])) {
            self::logError('Token CSRF não encontrado na sessão');
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_NAME]['token'], $headers['X-CSRF-Token']);
    }

    /**
     * Adiciona uma URL à lista de exclusão
     * 
     * @param string $path Caminho da URL a ser excluída da validação
     */
    public static function excludePath($path) {
        self::$excludedPaths[] = $path;
    }
}

// Inicia a proteção CSRF automaticamente
new CSRF();

/**
 * Exemplo de uso em um formulário:
 * 
 * <form method="POST" action="/submit">
 *     <?php echo CSRF::getTokenField(); ?>
 *     <!-- outros campos do formulário -->
 * </form>
 * 
 * Exemplo de uso em uma requisição AJAX:
 * 
 * $.ajax({
 *     url: '/api/endpoint',
 *     type: 'POST',
 *     headers: {
 *         'X-CSRF-Token': '<?php echo CSRF::getAjaxToken(); ?>'
 *     },
 *     data: formData,
 *     success: function(response) {
 *         // handle response
 *     }
 * });
 * 
 * Para excluir uma URL da validação:
 * CSRF::excludePath('/api/webhook');
 */