<?php


namespace core;


final class Authorization
{
    private static $_instance;

    private function __construct() {
    }

    public static function getInst() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    // __clone, __wakeup объявляются приватными чтобы исклбючить копирование объекта
    private function __clone() {}
    private function __wakeup() {}

    ///////////////////////////////////////////////

    public function isAdmin()
    {
        return (($_SESSION['username'] ?? '') === 'admin');
    }

    public function login($login,$password)
    {
        $loginRes = false;

        if ($login == 'admin' && $password === '123') {
            $_SESSION['username'] = 'admin';
            $loginRes = true;
        }
        else {
            $_SESSION['username'] = 'guest';
        }

        return $loginRes;
    }

    public function logoff() {
        $_SESSION['username'] = 'guest';
    }
}
