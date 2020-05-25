<?php

namespace core;


//
//
//
final class Authorization
{
    private static $_instance;

    //констурктор приватный, чтобы исключить оздание класса через new
    private function __construct() {}

    public static function getInst() {
        if (self::$_instance === null) {
            self::$_instance = new self;
            self::$accessLevel = 0;
        }

        return self::$_instance;
    }

    // __clone, __wakeup объявляются приватными чтобы исклбючить копирование объекта
    private function __clone() {}
    private function __wakeup() {}

    ///////////////////////////////////////////////

    static private $accessLevel;

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

    public function logout() {
        $_SESSION['username'] = 'guest';
    }

    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

}
