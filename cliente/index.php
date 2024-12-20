<?php

define('TIPO', 'cliente');

require_once '../core/filter/auth.php';
// Obtém a URL atual
$url = isset($_GET['url']) ? $_GET['url'] : 'painel';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Define a página atual
$pagina = $url[0];

$rotas = [
    'painel',
    'processos',
    'ver-processo',
    'faturas',
    'ver-fatura',
    'documentos',
    'perfil'
];

require_once 'assets/header.php';

// Verifica se a página solicitada existe nas rotas
if (in_array($pagina, $rotas)) {
    require_once 'pages/'.$pagina.'.php';
} else {
    // Página não encontrada
    require_once 'pages/404.php';
}

require_once 'assets/footer.php';