<?php
    require 'autoload.php';
    require 'consts.php';
    require 'debug.php';
    require 'error.php';



/////////////////////////////////////////
/////////////////////////////////////////
/////////////////////////////////////////

    //глобальный объект  $global_Mysql, база данных

    //делаем базу данных глобальной
    //т.к. в данном приложеии база данных только одна
    //база открывается в начале работы приложения и остается открытой на потяжении всей работы приложения



    $global_Mysql =null;    //объект базы данных


    /**
     *  функция применяется для сокращения кода при обращения к базе даннх
     *
     *  в коде можно писать sql()->select(...) итп
     *
     * @return \core\Mysql
     */
    function sql()
    {
        global $global_Mysql;
        return $global_Mysql;
    }

/////////////////////////////////////////
/////////////////////////////////////////
/////////////////////////////////////////
// глбоальная переменнная - тип обработчика
// применятся СТРОГО В ОДНОМ СЛУЧАЕ. при обработке фатальных ошибок
// для того чтобы определить куда выодить дамп ошибки ( на экран, в лог или в через POST в браузер)

    $global_routeType=null;

    function global_setRouteType($routeType)
    {
        global $global_routeType;
        $global_routeType = $routeType;
    }

    function global_getRouteType()
    {
        global $global_routeType;
        return $global_routeType;
    }

/////////////////////////////////////////
/////////////////////////////////////////
/////////////////////////////////////////


    function main()
    {
        //настройка обработчиков ошибок
        err_initError();

        //вывод лога
        log_initLog();

        $router = null;     // объявлем до try {, иначе обработчик ошибко не видет $router

        try {

            //инициируем базу данных - глобальный объект
            global $global_Mysql;
            $global_Mysql = new \core\Mysql(require '../config/MysqlConfig.php');


            // создаем и заупскаем роутер
            $router = new \core\Router();
            $router->run();



        } catch (\Error $ex) {

            err_catchFatalError('catch Error', $ex->getFile(), $ex->getLine(), $ex->getMessage());

        } catch (\Exception $ex) {

            err_catchFatalError('catch Exception', $ex->getFile(), $ex->getLine(), $ex->getMessage());

        } catch (\Throwable $ex) {

            err_catchFatalError('catch Throwable', $ex->getFile(), $ex->getLine(), $ex->getMessage());

        }

    }
