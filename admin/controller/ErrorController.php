<?php

include_once('ControllerAdmin.php');

class ErrorController extends ControllerAdmin {
    public function index() {
        $dados['titulo'] = 'Página Não Encontrada';
        $this->loadPage('error', $dados);
    }
}