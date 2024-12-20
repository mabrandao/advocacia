<?php

require_once __DIR__ . "/../helpers/redirects.php";

if ($redirect->sessao()->existe('login') == false) {
    $redirect->redirectMessage(1, 'Página restrita, Faça login!', 'login', 'error');
}

if ( $redirect->sessao()->get('tipo') != TIPO) { 
    $redirect->redirectMessage(1, 'Página restrita a '.$redirect->sessao()->get('tipo') . '<br> Contate o Administrador!', 'login', 'warning');
}
