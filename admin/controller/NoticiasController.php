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
        $dados['title'] = 'Administrar';  
        $dados['filds'] = $this->model->fields_data();
        return $this->loadPage('noticias', $dados);

    }

    public function listar() {
        // Retorna o JSON diretamente
        echo $this->model->getDataTable($this->getPost());
    }


    public function store() {   

        if (empty($this->getPost())) {    
            $dados['title'] = 'Cadastrar';
            $dados['filds'] = $this->model->fields(); 
            $dados['tipo'] = "store";       
            return $this->loadPage('noticias-edit', $dados);
        }

        if ($this->model->create($this->getPost())) {
            return $this->redirects->redirectMessage(1, 'Noticia cadastrada com sucesso!', 'admin/noticias', 'success');
        } 

        return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
        
    }

    public function edit() {     

        if (empty($this->getGET()['id']) & empty($this->getPost())) {
            return $this->redirects->redirectMessage('back', 'Sem ID da noticia para editar!', '', 'error');
        }

        if (isset($this->getPost()['id'])) {
            if ($this->model->update($this->getPost())) {
                return $this->redirects->redirectMessage(1, 'Noticia atualizada com sucesso!', 'admin/noticias', 'success');
            } else {
                return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
            }
        }

        if ($this->model->find($this->getGET()['id'])) {
            $dados['title'] = 'Editar';
            $dados['filds'] = $this->model->fields_data();
            $dados['noticia'] = $this->model->find($this->getGET()['id']);
            $dados['tipo'] = "edit";
            return $this->loadPage('noticias-edit', $dados);
        } else {
            return $this->redirects->redirectMessage('back', 'Noticia nao encontrada!', '', 'error');
        }

    }

    public function delete() {

        if (empty($this->getPost())) {            
            return $this->redirects->redirectMessage('back', 'Sem dados!', '', 'error');
        }

        if (empty($this->getPost()['id'])) {
            return $this->redirects->redirectMessage('back', 'Sem ID da noticia!', '', 'error');
        }

        if ($this->model->delete($this->getPost()['id'])) {
            return $this->redirects->redirectMessage(1, 'Noticia excluida com sucesso!', 'admin/noticias', 'success');
        } else {
            return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
        }
    }
}