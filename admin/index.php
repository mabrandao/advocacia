<?php
define('TIPO', 'admin');

//require_once ('../core/filter/filter.php');

$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$pagina = $url[0];

$rotas = [
    'dashboard' => ['DashboardController', 'index', 'Dashboard'],
    'perfil' => ['PerfilController', 'index', 'Perfil'],
    'clientes' => ['ClientesController', 'index', 'Clientes'],
    'servicos' => ['ServicosController', 'index', 'Servicos'],
    'configuracoes' => ['ConfiguracoesController', 'index', 'Configuracoes'],
    'logout' => ['LogoutController', 'index'],
    'processos' => ['ProcessosController', 'index', 'Processos'],

    'noticias' => ['NoticiasController', 'index', 'Noticias'],
    'noticias-listar' => ['NoticiasController', 'listar', ''],
    'noticias-store' => ['NoticiasController', 'store', 'Cadastrar Noticia'],
    'noticias-edit' => ['NoticiasController', 'edit', 'Editar Noticia'],
    'noticias-delete' => ['NoticiasController', 'delete', ''],
    
    'sobre' => ['SobreController', 'index', 'Sobre'],
    'areas-atuacao' => ['AreasAtuacaoController', 'index', 'Áreas de Atuação'],
    'contato' => ['ContatoController', 'index', 'Contato'],
    '404' => ['ErrorController', 'index', 'Página Não Encontrada']
];

$rota = isset($rotas[$pagina]) ? $rotas[$pagina] : $rotas['404'];

$arquivo = 'controller/'.$rota[0].'.php';  

if (!file_exists($arquivo)) {   
    echo $error = "Controller: <strong>$arquivo</strong> não encontrado!";
    include_once 'controller/ErrorController.php';
    return;
} 

require_once $arquivo;
$controller_class = $rota[0];
$metodo = $rota[1];
$titulo = $rota[2];

$controller = new $controller_class();
$controller->$metodo();
