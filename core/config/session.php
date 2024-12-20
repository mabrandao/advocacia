<?php

class Session 
{
    public function __construct() {
       self::start();
    }

    private function start() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function destroy() {
        session_destroy();
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }   

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }else {
            return false;
        }
    }

    public function existe($key) {
        return isset($_SESSION[$key]);
    }

    public function getFlash($key) {
        if($this->existe($key)){
            $flash = $_SESSION[$key];
            unset($_SESSION[$key]);
        }else{
            $flash = "";
        }
        
        return $flash;
    }

    public function setFlash($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function restart() {
        $this->start();
    }

    public function destruir() {
        $this->destroy();
    }
}