<?php

// Ambiente (local ou production)
define('APP_ENV', getenv('APP_ENV') ?: 'local');

// URLs
define('BASE_URL', APP_ENV === 'production' ? getenv('APP_URL') : 'http://localhost/advocacia/');
define('ASSETS_URL', BASE_URL . 'site/assets/');

// Configurações do Banco de Dados
if (APP_ENV === 'production') {
    define('DB_HOST', getenv('MYSQL_HOST'));
    define('DB_USER', getenv('MYSQL_USER'));
    define('DB_PASS', getenv('MYSQL_PASSWORD'));
    define('DB_NAME', getenv('MYSQL_DATABASE'));
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'agencia');
}

define('DB_CHARSET', 'utf8');
define('DB_COLLATION', 'utf8_general_ci');

// Configurações de Email
define('EMAIL_HOST', getenv('EMAIL_HOST') ?: 'smtp.gmail.com');
define('EMAIL_PORT', getenv('EMAIL_PORT') ?: 587);
define('EMAIL_USER', getenv('EMAIL_USER') ?: 'email');
define('EMAIL_PASS', getenv('EMAIL_PASS') ?: 'senha');

define('PROJECT_TITLE', 'Advocacia');

function base_url() {
    return BASE_URL;
}

function assets_url() {
    return ASSETS_URL;
}

?>