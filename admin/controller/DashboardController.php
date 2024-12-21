<?php

include_once('ControllerAdmin.php');

class DashboardController extends ControllerAdmin {

    public function index() {
        $dados['title'] = 'Dashboard';
        $this->loadPage('dashboard', $dados);

    }
}