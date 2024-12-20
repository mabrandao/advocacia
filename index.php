<?php

include_once 'core/helpers/redirects.php';
// Obtém a URL atual
$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Define a página atual
$pagina = $url[0];

// Define as rotas permitidas
$rotas = [
    'home',
    'sobre',
    'areas-atuacao',
    'contato',
    'blog'
];

$titulos = [
    'home' => 'Home',
    'sobre' => 'Sobre',
    'areas-atuacao' => 'Áreas de Atuação',
    'contato' => 'Contato',
    'blog' => 'Blog'
];

$arquivo = 'site/pages/'.$pagina.'.php';  

require_once 'site/assets/header.php';

if (!file_exists($arquivo)) {    
    require_once 'site/pages/404.php';
    require_once 'site/assets/footer.php';
    exit;
}

// Verifica se a página solicitada existe nas rotas
if (in_array($pagina, $rotas)) {    
    require_once $arquivo;
} else {
    require_once 'site/pages/404.php';
}

require_once 'site/assets/footer.php';