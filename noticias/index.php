<?php

// Obtém a URL atual
$url = isset($_GET['url']) ? $_GET['url'] : 'noticias';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Define a página atual
$pagina = $url[0];

$rotas = [
    'noticias',
    'ver',
    'pesquisar',
    'categorias'
];

require_once 'assets/header.php';

// Verifica se a página solicitada existe nas rotas de noticias
if (in_array($pagina, $rotas)) {
    require_once 'pages/'.$pagina.'.php';
} else {
    // Página não encontrada
    require_once 'pages/404.php';
}

require_once 'assets/footer.php';