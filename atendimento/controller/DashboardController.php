<?php

include_once('ControllerAtendimento.php');

class DashboardController extends ControllerAtendimento {

    public function index() {
        $dados['title'] = 'Dashboard';
        $this->loadPage('dashboard', $dados);

    }
}