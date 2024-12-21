<?php
include_once('../core/helpers/redirects.php');
class ControllerAdmin 
{
    public function loadPage($page, $params = array()) {
        array_unshift($params);
        include_once('pages/include/header.php');
        include_once('pages/' . $page . '.php');
        include_once('pages/include/footer.php');
    }

    public function dd($var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}