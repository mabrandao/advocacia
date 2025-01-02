<?php

include_once('ControllerAdmin.php');

class DashboardController extends ControllerAdmin {

    public function index() {       
        $this->loadPage('dashboard');

    }

    public function arquivos() {       
        $this->loadPage('arquivos');

    }

}