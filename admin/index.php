<?php
define('TIPO', 'admin');

//require_once ('../core/filter/filter.php');

$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$pagina = $url[0];


$rotas = [
    'dashboard' => ['DashboardController', 'index'],
    'perfil' => ['PerfilController', 'index'],
    'clientes' => ['ClientesController', 'index'],
    'servicos' => ['ServicosController', 'index'],
    'configuracoes' => ['ConfiguracoesController', 'index'],
    'logout' => ['LogoutController', 'index'],
    'processos' => ['ProcessosController', 'index'],

    'noticias' => ['NoticiasController', 'index'],
    'noticias-listar' => ['NoticiasController', 'listar'],
    'noticias-store' => ['NoticiasController', 'store'],
    'noticias-editar' => ['NoticiasController', 'edit'],
    'noticias-delete' => ['NoticiasController', 'delete'],
    
    'sobre' => ['SobreController', 'index'],
    'areas-atuacao' => ['AreasAtuacaoController', 'index'],
    'contato' => ['ContatoController', 'index'],
    'arquivos' => ['DashboardController', 'arquivos'],
    '404' => ['ErrorController', 'index']
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

$controller = new $controller_class();
$controller->$metodo(isset($url[1]) ? $url[1] : null);
