<?php
include_once('../core/helpers/redirects.php');
include_once('../core/helpers/views_helpers.php');

class ControllerAdmin 
{
    private $redirects;
    private $session;
    protected $viewHelper;

    public function __construct() {
        $this->redirects = new Redirects();
        $this->session = new Session();        
    }

    public function loadPage($page, $params = array()) {
        // Extrai as variáveis do array para ficarem disponíveis na view
        extract($params);
        
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
 * @return array|null
 */
public function getPost($allowedTags = []) {
    // Verifica se houve POST se não houver, retorna null
    if (empty($_POST)) {
        return null;
    }

    $post = [];
    foreach ($_POST as $key => $value) {
        // Pula campos do sistema
        if (in_array($key, ['csrf_token', 'action'])) {
            continue;
        }

        // Mantém arrays intactos para o DataTables
        if (in_array($key, ['columns', 'order', 'search'])) {
            $post[$key] = $value;
            continue;
        }

        // Sanitização básica para outros campos
        if (is_array($value)) {
            $post[$key] = array_map(function($item) use ($allowedTags, $key) {
                return $this->sanitizeValue($item, $allowedTags[$key] ?? []);
            }, $value);
        } else {
            $post[$key] = $this->sanitizeValue($value, $allowedTags[$key] ?? []);
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
        // Pula campos do sistema
        if (in_array($key, ['csrf_token', 'action'])) {
            continue;
        }

        $get[$key] = $this->sanitizeValue($value);
    }
    return $get;
}

/**
 * Sanitiza um valor individual
 * @param mixed $value Valor a ser sanitizado
 * @param array $allowedTags Tags HTML permitidas para este campo
 * @return mixed
 */
protected function sanitizeValue($value, array $allowedTags = []) {
    if (is_null($value)) {
        return null;
    }

    // Converte para string se não for
    $value = (string) $value;

    // Remove caracteres invisíveis e espaços extras
    $value = trim(preg_replace('/\s+/', ' ', $value));
    
    // Remove caracteres null bytes
    $value = str_replace(chr(0), '', $value);

    // Sanitiza HTML se houver tags permitidas
    if (!empty($allowedTags)) {
        $value = strip_tags($value, $allowedTags);
    } else {
        $value = strip_tags($value);
    }

    // Converte caracteres especiais em entidades HTML
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Remove scripts maliciosos
    $value = preg_replace('/(javascript|vbscript|expression|applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base|onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragend|ondragenter|ondragleave|ondragover|ondragstart|ondrop|onerror|onerrorupdate|onfilterchange|onfinish|onfocus|onfocusin|onfocusout|onhelp|onkeydown|onkeypress|onkeyup|onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|onmouseleave|onmousemove|onmouseout|onmouseover|onmouseup|onmousewheel|onmove|onmoveend|onmovestart|onpaste|onpropertychange|onreadystatechange|onreset|onresize|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|onunload)/i', '', $value);
    
    return $value;
}

}