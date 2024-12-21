<?php

/**
 * Classe para controle de limite de requisições (Rate Limiting)
 * A validação é feita automaticamente quando o arquivo é incluído
 */
class Throttle {
    /**
     * Instância única da classe (singleton)
     */
    private static $instance = null;

    /**
     * Diretório para armazenar os arquivos de controle
     */
    private $storageDir;

    /**
     * Limite padrão de requisições por minuto
     */
    private $requestsPerMinute;

    /**
     * Lista de IPs que não devem ser limitados
     */
    private static $whitelist = [
        '127.0.0.1',
        'localhost'
    ];

    /**
     * URLs que não devem ser limitadas
     */
    private static $excludedPaths = [
        '/login',
        '/logout',
        '/assets/',
        '/public/'
    ];

    /**
     * Construtor
     * 
     * @param int $requestsPerMinute Limite de requisições por minuto
     */
    private function __construct($requestsPerMinute = 60) {
        $this->requestsPerMinute = $requestsPerMinute;
        $this->storageDir = dirname(__FILE__) . '/../../storage/throttle/';
        
        if (!file_exists($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }

        // Limpa arquivos antigos a cada 100 requisições
        if (rand(1, 100) === 1) {
            $this->cleanup();
        }

        // Executa a validação automaticamente
        $this->validateRequest();
    }

    /**
     * Obtém a instância única da classe
     * 
     * @param int $requestsPerMinute Limite de requisições por minuto
     * @return Throttle
     */
    public static function getInstance($requestsPerMinute = 60) {
        if (self::$instance === null) {
            self::$instance = new self($requestsPerMinute);
        }
        return self::$instance;
    }

    /**
     * Valida a requisição atual automaticamente
     */
    private function validateRequest() {
        // Verifica se a URL atual está na lista de exclusão
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        foreach (self::$excludedPaths as $path) {
            if (strpos($currentPath, $path) === 0) {
                return;
            }
        }

        // Verifica o limite de requisições
        if (!$this->check()) {
            $this->handleLimitExceeded();
        }
    }

    /**
     * Trata o erro quando o limite é excedido
     */
    private function handleLimitExceeded() {
        $waitTime = $this->getWaitTime();
        
        header('Retry-After: ' . $waitTime);
        header('HTTP/1.1 429 Too Many Requests');

        if ($this->isAjax()) {
            die(json_encode([
                'error' => 'Limite de requisições excedido',
                'message' => 'Por favor, aguarde ' . $waitTime . ' segundos antes de tentar novamente.',
                'wait' => $waitTime,
                'remaining' => $this->getRemainingRequests()
            ]));
        }

        // Para requisições normais, mostra uma página de erro amigável
        die('
            <html>
            <head>
                <title>Limite de Requisições Excedido</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
                    h1 { color: #444; }
                    .message { color: #666; margin: 20px 0; }
                    .timer { font-size: 1.2em; color: #e74c3c; }
                </style>
            </head>
            <body>
                <h1>Limite de Requisições Excedido</h1>
                <div class="message">
                    Por favor, aguarde alguns segundos antes de tentar novamente.<br>
                    Tempo restante: <span class="timer">' . $waitTime . ' segundos</span>
                </div>
            </body>
            </html>
        ');
    }

    /**
     * Verifica se é uma requisição AJAX
     */
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Verifica se o IP atual atingiu o limite de requisições
     * 
     * @return bool True se pode continuar, False se atingiu o limite
     */
    private function check() {
        $ip = $this->getClientIp();

        if (in_array($ip, self::$whitelist)) {
            return true;
        }

        $requests = $this->getRequests($ip);
        $requests = array_filter($requests, function($timestamp) {
            return $timestamp > time() - 60;
        });

        $requests[] = time();
        $this->saveRequests($ip, $requests);

        return count($requests) <= $this->requestsPerMinute;
    }

    /**
     * Obtém as requisições do IP
     */
    private function getRequests($ip) {
        $file = $this->storageDir . md5($ip) . '.json';
        
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }

    /**
     * Salva as requisições do IP
     */
    private function saveRequests($ip, $requests) {
        $file = $this->storageDir . md5($ip) . '.json';
        file_put_contents($file, json_encode($requests));
    }

    /**
     * Obtém o IP do cliente
     */
    private function getClientIp() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Limpa arquivos de controle antigos
     */
    private function cleanup() {
        $files = glob($this->storageDir . '*.json');
        foreach ($files as $file) {
            if (filemtime($file) < time() - 3600) {
                unlink($file);
            }
        }
    }

    /**
     * Adiciona um IP à whitelist
     */
    public static function addToWhitelist($ip) {
        self::$whitelist[] = $ip;
    }

    /**
     * Remove um IP da whitelist
     */
    public static function removeFromWhitelist($ip) {
        $key = array_search($ip, self::$whitelist);
        if ($key !== false) {
            unset(self::$whitelist[$key]);
        }
    }

    /**
     * Adiciona uma URL à lista de exclusão
     */
    public static function excludePath($path) {
        self::$excludedPaths[] = $path;
    }

    /**
     * Retorna o tempo restante até a próxima requisição permitida
     */
    private function getWaitTime($ip = null) {
        $ip = $ip ?: $this->getClientIp();
        $requests = $this->getRequests($ip);
        
        if (empty($requests)) {
            return 0;
        }

        $oldestAllowed = time() - 60;
        $validRequests = array_filter($requests, function($timestamp) use ($oldestAllowed) {
            return $timestamp > $oldestAllowed;
        });

        if (count($validRequests) < $this->requestsPerMinute) {
            return 0;
        }

        return min($validRequests) + 60 - time();
    }

    /**
     * Retorna quantas requisições ainda podem ser feitas
     */
    private function getRemainingRequests($ip = null) {
        $ip = $ip ?: $this->getClientIp();
        $requests = $this->getRequests($ip);
        
        $oldestAllowed = time() - 60;
        $validRequests = array_filter($requests, function($timestamp) use ($oldestAllowed) {
            return $timestamp > $oldestAllowed;
        });

        return max(0, $this->requestsPerMinute - count($validRequests));
    }
}

// Inicia o controle de requisições automaticamente
// com limite de 60 requisições por minuto
Throttle::getInstance(60);
