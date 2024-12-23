<?php
define('TIPO', 'Cliente');

//require_once ('../core/filter/filter.php');

$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$pagina = $url[0];

$rotas = [
    'dashboard' => ['DashboardController', 'index', 'Dashboard'],
    'perfil' => ['PerfilController', 'index', 'Perfil'],
    'agendamentos' => ['AgendamentosController', 'index', 'Agendamentos'],
    'documentos' => ['DocumentosController', 'index', 'Documentos'],
    '404' => ['ErrorController', 'index', 'Página Não Encontrada']
];

$rota = isset($rotas[$pagina]) ? $rotas[$pagina] : $rotas['404'];
$controller_class = $rota[0];
$arquivo = 'controller/'.$controller_class.'.php';  

if (!file_exists($arquivo)) {   
    $error = "Controller: <strong>$arquivo</strong> não encontrado!";
    include_once 'controller/ErrorController.php';
    return;
} 

require_once $arquivo;
$metodo = $rota[1];
$titulo = $rota[2];

$controller = new $controller_class();
$controller->$metodo();
