<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/session.php';

class Redirects {
    
    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    /**
    * Redireciona o usuário para a página anterior de forma segura
    * @return void
    */
    public function back() {
        // Limpa qualquer saída anterior
        if (ob_get_length()) ob_clean();
        
        // Verifica se existe uma página anterior válida
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL;
        
        // Garante que o redirecionamento seja para o mesmo domínio
        if (!str_starts_with($referer, BASE_URL)) {
            $referer = BASE_URL;
        }
        
        // Define headers de segurança
        header_remove('X-Powered-By');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        header("Location: " . $referer);
        exit();
    }

    /**
    * Redireciona o usuário para a home de forma segura
    * @return void
    */
    public function home() {
        // Limpa qualquer saída anterior
        if (ob_get_length()) ob_clean();
        
        // Define headers de segurança
        header_remove('X-Powered-By');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        header("Location: " . BASE_URL);
        exit();
    }

    /**
    * Retorna a sessão do usuário
    * @return Session
    */  
    public function sessao() {
        return $this->session;
    }

    /**
     * Redireciona o usuário para uma URL específica de forma segura
     * @param string $url - URL de destino
     * @return void
     */
    public function redirect($url) {
        // Limpa qualquer saída anterior
        if (ob_get_length()) ob_clean();
        
        // Garante que a URL comece com BASE_URL
        if (!str_starts_with($url, BASE_URL)) {
            $url = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
        }
        
        // Define headers de segurança
        header_remove('X-Powered-By');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        header("Location: " . $url);
        exit();
    }

    /**
     * Redireciona o usuário para uma URL específica com uma mensagem de feedback
     *
     * @param int|string $metodo    - Método de redirecionamento: 1 (redireciona), 'back' ou 'home'
     * @param string $message       - Mensagem a ser exibida
     * @param string $url          - URL de destino (usado apenas quando $metodo = 1)
     * @param string|null $tipo     - Tipo da mensagem: 'success', 'error', 'warning', 'info' ou null
     * @return void
     */
    public function redirectMessage($metodo, $message, $url, $tipo = null): void {   
        if (empty($message)) {
            throw new \InvalidArgumentException('A mensagem não pode estar vazia');
        }
        
        // Define o tipo padrão se não for especificado
        $tipos_validos = ['success', 'error', 'warning', 'info'];
        $tipo = in_array($tipo, $tipos_validos) ? $tipo : 'default';
        
        // Define a mensagem na sessão
        $this->session->setFlash($tipo, $message);

        // Executa o redirecionamento apropriado
        switch ($metodo) {
            case 'back':
                $this->back();
                break;
            case 'home':
                $this->home();
                break;
            case 1:
                if (empty($url)) {
                    throw new \InvalidArgumentException('URL não pode estar vazia quando método é 1');
                }
                $this->redirect($url);
                break;
            default:
                throw new \InvalidArgumentException('Método de redirecionamento inválido');
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