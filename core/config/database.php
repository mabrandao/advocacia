<?php
require_once 'constants.php';

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->conn;
    }

    /**
     * Fecha a conexão com o banco de dados
     * @return void
     */
    public static function close() {
        if (self::$instance !== null) {
            self::$instance->conn = null;
            self::$instance = null;
        }
    }

    // Previne que a classe seja clonada
    private function __clone() {}

    // Previne que a classe seja deserializada
    public function __wakeup() {}
}
?>
