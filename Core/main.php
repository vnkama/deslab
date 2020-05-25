<?php
    require 'consts.php';
    require 'debug.php';
    require 'error.php';




//
//
//
function main()
{
    //
    // установим автозагрузчик классов
    //
    spl_autoload_register(
        function ($class){
            require_once '../'.str_replace('\\','/',$class).'.php';
        }
    );





    /**
     *  функция применяется для сокращения кода при обращения к базе даннх
     *
     *  в коде можно писать sql()->select(...) итп
     *
     * @return \core\Mysql
     */




    /////////////////////////////////////////
    /////////////////////////////////////////
    /////////////////////////////////////////



        //настройка обработчиков ошибок
        err_initError();

        //вывод лога
        loginit();

        $router = null;     // объявлем до try {, иначе обработчик ошибко не видет $router

        try {

            //инициируем базу данных - глобальный объект
            global $global_Mysql;
            $global_Mysql = new \core\Mysql(require '../config/MysqlConfig.php');


            //кодировка. задается в.т.ч. (обязательна для корректной работы регулярок на русском)
            mb_regex_encoding(UTF8);

            date_default_timezone_set('Europe/Moscow');

            $this->arrMysqlConfig   = require('../custom/db.cfg.php');
            $this->arrRoutes        = require('../custom/routes.php');


            // создаем и заупскаем роутер
            $router = new \core\Router();
            $router->run();



        } catch (\Error $ex) {

            err_handleFatalError_die('catch Error', $ex->getFile(), $ex->getLine(), $ex->getMessage());

        } catch (\Exception $ex) {

            err_handleFatalError_die('catch Exception', $ex->getFile(), $ex->getLine(), $ex->getMessage());

        } catch (\Throwable $ex) {

            err_handleFatalError_die('catch Throwable', $ex->getFile(), $ex->getLine(), $ex->getMessage());

        }

} // main()
