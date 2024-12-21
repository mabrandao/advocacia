<?php

if ($redirect->sessao()->existe('login')) {
    if ( $redirect->sessao()->get('tipo') != TIPO) { 
        $redirect->redirectMessage(1, 'Página restrita a '.$redirect->sessao()->get('tipo') . '<br> Contate o Administrador!', 'login', 'warning');
    }
}else{
    $redirect->redirectMessage(1, 'Página restrita, Faça login!', 'login', 'error');
}


