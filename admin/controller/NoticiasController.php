<?php

include_once('ControllerAdmin.php');
include_once(__DIR__ . '../../../model/noticiasModel.php');

class NoticiasController extends ControllerAdmin {
    private $model;

    public function __construct() {
        parent::__construct(); // Chama o construtor da classe pai
        $this->model = new NoticiasModel();
    }

    public function index() {     
        $dados['titulo'] = 'Administrar';  
        $dados['filds'] = $this->model->fields_data();
        return $this->loadPage('noticias', $dados);
    }

    public function listar() {
        // Retorna o JSON diretamente
        echo $this->model->getDataTable($this->getPost());
    }

    /**
     * Armazena uma nova notícia no sistema
     * @return mixed
     */
    public function store() { 

        if ($this->getPost() == false) {    
            $dados['titulo'] = 'Cadastrar';
            $dados['filds'] = $this->model->fields(); 
            $dados['tipo'] = "store";       
            return $this->loadPage('noticias-edit', $dados);
        }

        try {

            $dados_form = $this->getPost();

            $dados_form['slug'] = $this->slug($dados_form['titulo']);

            // Tenta criar a notícia
            if ($this->model->create($dados_form)) {
                return $this->redirects->redirectMessage(
                    1, 
                    'Notícia cadastrada com sucesso!', 
                    'admin/noticias', 
                    'success'
                );
            }

            // Se houver erros de validação, configura as mensagens de erro
            $erros = $this->model->get_erro();
            $errors = '';
            
            if (!empty($erros)) {
                if (is_array($erros)) {
                    foreach ($erros as $campo => $mensagens) {
                        if (is_array($mensagens)) {
                            foreach ($mensagens as $mensagem) {
                                $errors .= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . "<br>";
                            }
                        } else {
                            $errors .= htmlspecialchars($mensagens, ENT_QUOTES, 'UTF-8') . "<br>";
                        }
                    }
                } else {
                    $errors .= htmlspecialchars($erros, ENT_QUOTES, 'UTF-8');
                }

                return $this->redirects->redirectMessage(
                    'back',
                    'Por favor, corrija os erros no formulário:<br>' . $errors,
                    '',
                    'error'
                );
            }
        } catch (\Exception $e) {
            // Log do erro para debugging
            error_log("Erro ao cadastrar notícia: " . $e->getMessage());
            
            return $this->redirects->redirectMessage(
                'back',
                'Ocorreu um erro inesperado. Tente novamente mais tarde.',
                '',
                'error'
            );
        }
    }

    public function edit($id = null) {   
        if (empty($id)) {
            return $this->redirects->redirectMessage('back', 'Sem Noticia para editar!', '', 'error');
        }

        if ($this->getPost() != false) {
            $dados_form = $this->getPost();
            $dados_form['slug'] = $this->slug($dados_form['titulo']);
            if ($this->model->update($id, $dados_form)) {
                return $this->redirects->redirectMessage(1, 'Noticia atualizada com sucesso!', 'admin/noticias', 'success');
            }
            return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
        }

        if ($this->model->find($id)) {
            $dados['titulo'] = 'Editar';
            $dados['filds'] = $this->model->fields_data();
            $dados['noticia'] = $this->model->find($id);
            $dados['tipo'] = "editar";
            return $this->loadPage('noticias-edit', $dados);
        }
        
        return $this->redirects->redirectMessage('back', 'Noticia não encontrada!', '', 'error');
    }

    public function delete($id = null) {
        if (empty($id)) {
            return $this->redirects->redirectMessage('back', 'Sem ID da noticia para excluir!', '', 'error');
        }

        if ($this->model->delete($id)) {
            return $this->redirects->redirectMessage(1, 'Noticia excluída com sucesso!', 'admin/noticias', 'success');
        }

        return $this->redirects->redirectMessage('back', $this->model->get_erro(), '', 'error');
    }
}