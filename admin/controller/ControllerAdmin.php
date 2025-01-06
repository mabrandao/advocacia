<?php
include_once('../core/helpers/redirects.php');
require_once('../core/config/session.php');
include_once('../core/helpers/views_helpers.php');

class ControllerAdmin 
{
    protected $session;
    protected $redirects;
    protected $viewHelper;

    public function __construct() {
        $this->session = new Session();
        $this->redirects = new Redirects();
        $this->viewHelper = new ViewsHelpers();
    }

    /**
     * Carrega uma página do painel administrativo
     * @param string $page Nome da página a ser carregada
     * @param array $params Parâmetros para a página
     */
    protected function loadPage($page, $params = []) {
        if (!empty($params)) {
            extract($params);
        }
        
        // Disponibiliza as variáveis para as views
        $session = $this->session;
        $redirects = $this->redirects;
      
        include_once('pages/include/header.php');
        include_once("pages/{$page}.php");
        include_once('pages/include/footer.php');
    }

    public function dd($var) {
        //verifica se o argumeto é um array ou json
        if (is_array($var) || is_object($var)) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        } elseif (is_string($var)) {
            echo '<pre>';
            print_r(json_decode($var));
            echo '</pre>';
        }else {
            echo '<p>' . $var . '</p>';
        }
    }

    /**
     * Retorna os dados do POST sanitizados no formato array associativo
     * @param array $allowedTags Array de tags HTML permitidas por campo
     * @return array|false
     */
    public function getPost($allowedTags = []) {
        // Verifica se houve POST se não houver, retorna null
        if (empty($_POST)) {
            return false;
        }

        $post = [];
        foreach ($_POST as $key => $value) {
            if (is_array($value)) {
                $post[$key] = $value;
            } else {
                // Se houver tags permitidas para este campo, usa strip_tags com essas tags
                if (isset($allowedTags[$key])) {
                    $post[$key] = strip_tags($value, $allowedTags[$key]);
                } else {
                    // Se não houver tags permitidas, remove todas as tags
                    $post[$key] = strip_tags($value);
                }
            }
        }
        return $post;
    }

    /**
     * Retorna os dados do GET sanitizados no formato array associativo
     * @return array|null
     */
    public function getGET() {
        // Verifica se houve GET se não houver, retorna null
        if (empty($_GET)) {
            return null;
        }

        $get = [];
        foreach ($_GET as $key => $value) {
            if (is_array($value)) {
                $get[$key] = $value;
            } else {
                $get[$key] = strip_tags($value);
            }
        }
        return $get;
    }

    protected function slug($titulo) {
        $slug = strtolower(trim($titulo));
        $slug = preg_replace('/[^0-9a-z]/', '', $slug);
        return $slug;
    }
}