<?php

include_once('ControllerAdmin.php');
include_once(__DIR__ . '../../../model/noticiasModel.php');

class NoticiasController extends ControllerAdmin {
    private $model;
    private $redirects;

    public function __construct() {
        parent::__construct(); // Chama o construtor da classe pai
        $this->model = new NoticiasModel();
    }
    public function index() {     
        $dados['title'] = 'Notícias';  
        $dados['filds'] = $this->model->fields_data();
        return $this->loadPage('noticias', $dados);

    }

    public function listar() {    

        // Retorna o JSON diretamente
        echo $this->model->getDataTable($this->getPost());
    }

    public function store() {   

        if (empty($this->getPost())) {            
            return $this->redirects->redirectMessage('back', 'Sem dados!', 'noticias', 'error');
        }

        if ($this->model->create($this->getPost())) {
            return $this->redirects->redirectMessage(1, 'Noticia cadastrada com sucesso!', 'admin/noticias', 'success');
        } else {
            return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
        }
        
        return $this->loadPage('noticias', ['campos' => $this->getPost(), 'error' => $this->model->get_erro()]);
       
    }

    public function edit() {     

        if (empty($this->getPost())) {            
            return $this->redirects->redirectMessage('back', 'Sem dados!', '', 'error');
        }

        if (empty($this->getPost()['id'])) {
            return $this->redirects->redirectMessage('back', 'Sem ID da noticia!', '', 'error');
        }

        if ($this->model->update($this->getPost())) {
            return $this->redirects->redirectMessage(1, 'Noticia atualizada com sucesso!', 'admin/noticias', 'success');
        } else {
            return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
        }
    }

   
}