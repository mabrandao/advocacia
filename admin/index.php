<?php
define('TIPO', 'admin');

require_once '../core/filter/auth.php';

$sessao = new Session();
// Obtém a URL atual
$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Define a página atual
$pagina = $url[0];

$rotas = [
    'dashboard',
    'perfil',
    'clientes',
    'servicos',
    'configuracoes',
    'logout',
    'processos',
    'noticias',
    'sobre',
    'areas-atuacao',
    'contato',
    '404'
];

$titulos = [
    'dashboard' => 'Dashboard',
    'perfil' => 'Meus Dados de Usuário',
    'clientes' => 'Meus Clientes',
    'servicos' => 'Meus Servicos',
    'configuracoes' => 'Minhas Configurações',
    'logout' => 'Logout',
    'processos' => 'Meus Processos',
    'noticias' => 'Notícias e Anúncios',
    'sobre' => 'Sobre',
    'areas-atuacao' => 'Áreas de Atuação',
    'contato' => 'Contato',
    '404' => 'Página Não Encontrada'
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
    $redirect->redirectMessage(1, 'Página não encontrada', '404', 'error');
}

require_once 'site/assets/footer.php';

echo $redirect->depurarSession();