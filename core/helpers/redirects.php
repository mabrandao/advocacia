<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

class Redirects {
    
    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /**
    * Redireciona o usuário para a página anterior
    * @return void
    */
    public function back() {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
    * Redireciona o usuário para a home
    * @return void
    */
    public function home() {
        header("Location: " . BASE_URL);
        exit;
    }

    /**
    * Retorna a sessão do usuário
    * @return Session
    */  
    public function sessao() {
        return $this->session;
    }

    /**
    * Redireciona o usuário para uma URL específica 
    * @param string $url - URL de destino já com a base URL
    * @return void
    */
    public function redirect($url) {
        $this->home().$url;
        exit;
    }

    /**
    * Redireciona o usuário para uma URL específica com uma mensagem de feedback
    *
    * @param int|string $metodo    - Método de redirecionamento: 1 (redireciona), 'back' ou 'home'
    * @param string|null $tipo     - Tipo da mensagem: 'success', 'error', 'warning', 'info' ou null
    * @param string $message       - Mensagem a ser exibida
    * @param string $url          - URL de destino (usado apenas quando $metodo = 1)
    * @return void
    */
    public function redirectMessage($metodo, $message, $url, $tipo = null): void {   
        switch ($tipo) {
            case 'success':
                $this->session ->setFlash('success', $message);  
                break;
            case 'error':
                $this->session->setFlash('error', $message);
                break;
            case 'warning':
                $this->session->setFlash('warning', $message);
                break;
            case 'info':
                $this->session->setFlash('info', $message);
                break;
            default:
                $this->session->setFlash('default', $message);
                break;
        }

        switch ($metodo) {
            case 'back':
                $this->back();
                break;
            case 'home':
                $this->home();
                break;
            default:
                $this->redirect($url);
                break;
        }
    }

    /**
    * Redireciona o usuário para a sua página inicial com base em seu tipo de usuário da sessão
    * @return void
    */
    public function redirectInicio(): void { 
        if ($this->session->existe('login')) {
            $tp = $this->session->get('tipo');
            $this->redirect($tp.'/dashboard.php');        
        }else{
            $this->home();
        }
    }   

    /**
    * Finaliza a sessão do usuário e redireciona para a página de login com uma mensagem
    * @return void
    */
    public function logout(): void {    
        $this->session->destruir();
        $this->session->restart();
        $this->redirectMessage(1,'success', 'Você saiu com Segurança!', 'login');
    }

    public function depurarSession() {
        $sessao = $this->sessao();  
        $message = "<pre>";
        $message .= print_r($sessao);
        $message .= "</pre>" ;
       
        return $message;
    }

}

$redirect = new Redirects();