<?php

define('BASE_URL', 'http://localhost/advocacia/');
define('ASSETS_URL', BASE_URL . 'site/assets/');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'agencia');
define('DB_CHARSET', 'utf8');
define('DB_COLLATION', 'utf8_general_ci');

define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_PORT', 587);
define('EMAIL_USER', 'email');
define('EMAIL_PASS', 'senha');

define('PROJECT_TITLE', 'Advocacia');

function base_url() {
    return BASE_URL ;
}

function assets_url() {
    return ASSETS_URL;
}

?>