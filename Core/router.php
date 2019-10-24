<?php
//единая точка входа для всех запросов к сайту
//файл обрабатывает запросы типа GET, POST, вызовы от linux, втч cron, exec

//файл подкючается в /public/index.php

//$_SERVER['REQUEST_URI'] 	содержит:

//для обычного GET запрсоа (перехода по гиперссылке):	'/myadress'
//для GET запроса с параметрами:				        '/myadress?logout=1'
//принудителный вызове cron через           	        '/run'					этот /run принудительно подставляет апач
//http-POST вызов от браузера	    		            '/start.php'			подставляем мы при POST запросе
//cron											        не определен, isset(..) === false


namespace Core;

use Core\Controller;

use Error;
use Exception;

if (!defined("START_PHP")) die('Directly run of router.php is rejected');
    if (defined("ROUTER_PHP")) die('Seconary include router.php is rejected');
    define('ROUTER_PHP',1);

    //единая точка входа для всех запросов к сайту
    //запросы вида GET, POST, и вызовы от linux, втч cron, exec


    require_once 	'config.php';       //настройки
    require_once   'routes.php';

    // Добавлять сообщения обо всех ошибках, кроме E_NOTICE
    error_reporting(E_ALL);
    ini_set('display_errors','On');

    //кодировка
    //задается втч для корректной работы регулярок на русском
    mb_regex_encoding(UTF8);


    date_default_timezone_set('Europe/Moscow');


    //автозагрузчик классов
    spl_autoload_register(
        function ($class){
            require_once '../'.str_replace('\\','/',$class).'.php';
        }
    );


    $routeType = null;  //должен быть доступен в catch




try {

        //запуск лог файла
        logStart();

        //подключаемся к базе данных
        $g_sql = new Mysql();



        $httpRequestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $uri1 = $_SERVER['REQUEST_URI'] ?? '';

        //число параметров комндной строки
        $argc = router_getArgc();


        $routeName = null;

        if ($httpRequestMethod === 'GET' && !$argc) {
            //у нас GET запрос

            session_start();
            $idClient = $_SESSION['clientLogin'] ?? 'guest';  // ну или admin

            $routeType = 'get';
            $routeName = router_getRouteName_GET();


        }
        elseif ($httpRequestMethod === 'POST' && !$argc) {
            //  у нас POST запрос
            $routeType = 'post';

            $routeName = router_getRouteName_POST();
        }
/*        elseif ($argc && empty($httpRequestMethod)) {
            //у нас запрос cli
            $routeType = 'cli';

            $routeName = router_getRouteName_CLI();
        }*/
        else {
            //неизвестный тип обработчика
            throw new Error();
        }


        logout("routeType $routeType,routeName $routeName ");


        //ищем путь
        $routeKey = null;



        foreach ($arrRoutes as $key => $arrRoute) {

            logout("foreach, ${arrRoute['type']}, $routeType, ${arrRoute['routeName']}, $routeName");

           if ($arrRoute['type'] === $routeType && $arrRoute['routeName'] === $routeName) {
               logout("if ok");
               $routeKey = $key;
               break;
           }
        }
        if (is_null($routeKey)) throw new Error();


        $controllerClass = '\\Core\\'.$arrRoutes[$routeKey]['Controller'];
        $controller = new $controllerClass();

        $funcName = $arrRoutes[$routeKey]['func'];

        if (is_null($arrRoutes[$routeKey]['param']))
            $controller->$funcName();
        else {
            $controller->$funcName($arrRoutes[$routeKey]['param']);
        }




} catch (\Throwable $ex) {

    $outBR = "catch Error<br>routeType $routeType<br>file: ".$ex->getFile(). ", ".$ex->getLine(). "<br>message: ". $ex->getMessage();
    $outCR = "catch Error\r\nrouteType $routeType\r\nfile: ".$ex->getFile(). ", ".$ex->getLine(). "\r\nmessage: ". $ex->getMessage();

    switch ($routeType) {

        case 'get':
            logout($outCR);
            redirect404();
            break;

        case 'post':
            logout($outCR);
            $arrPostAnswer =  [500 => 'AnswerStatus',$outBR => 'errorText' ];
            echo json_encode($arrPostAnswer);
            break;

        case 'cli':
            logout($outCR);
            break;

        default:
            logout($outCR);
            break;
    }

    die();
}

/**
 * функцтия нужна для обращени к глобальной переменной $g_sql
 * 1)чтобы не писать в начале каждой функции global $g_sql;
 * 2)чтобы исключить случайную порчу $g_sql
 *
 * @return Mysql
 */
function sql()
{
    global $g_sql;
    return $g_sql;
}



/**
 * для отладки
 * @param $par
 */
function echobr($par)
{
    echo "${par}<br>";
}



/**
 *
 * вытаскиевает url страницы и проверяет его на корерктность - белый спиcок символов
 * url берем из $_SERVER['REQUEST_URI']. он там без домена
 * строку GET параметров в $_SERVER['REQUEST_URI'э
 *
 * @return mixed|string         возаращает
 *          0 - при ошибке
 *          url страницы без домена, слеша и парметров.
 *          например для $_SERVER['REQUEST_URI'] = '/hello-123?par=vnakama&par2=123';
 *          вернет 'hello-123'
 *
 */
function router_getRouteName_GET()
{
    $url = null;        // без слеша и парметров , например 'index'

    if (!isset($_SERVER['REQUEST_URI'])) throw new Error();

    $len = strlen($_SERVER['REQUEST_URI']);
    if ($len < 1 || $len > MAX_STRLEN) return 0;

    $url        = $_SERVER['REQUEST_URI'];
    $countGET   = count($_GET) ?? 0;

    //вариант когда адрес сотоит только из слеша (главная страница)
    if ($url==='/') {
        $url = 'index';
    }
    elseif (substr($url,0,2) == '/?') {
        //главная c GET парметрами
        $url = 'index';
    }
    elseif (!count($_GET)) {
        //вариант GET без парметров
        $mask = "#^(/([A-z][A-z\d\-_]*))$#";
        if (preg_match($mask, $url, $arrMatches)) {
            $url = $arrMatches[2];
        }
    }
    elseif (count($_GET)) {
        //вариант GET c параметрами
        $mask = "#^(/([A-z][A-z\d\-_]*))\?#";

        if (preg_match($mask, $url, $arrMatches)) {
            $url = $arrMatches[2];
        }
    }

    return ($url) ? $url : 0;
}


/**
 * функция извлекает фактический адрес страницы на которую идет POST запрос
 * все POST запросы идут через index.php
 * фактический адрес страницы на которую был запрос хранится в поле $_POST['url']
 *
 * прводятся проверкт корректности адреса
 *
 * @return mixed - фактичсеский адрес старницы вида 'index'  без домена,слешей параметров итп
 * @throws Exception
 */
function router_getRouteName_POST()
{
    if ($_SERVER['REQUEST_URI'] !== '/index.php') throw new Exception($_SERVER['REQUEST_URI']);

    if (!isset($_POST['url'])) throw new Exception();

    $len = strlen($_POST['url'] ?? '');
    if ($len < 1 || $len > MAX_STRLEN) throw new Exception();

    //проверка адреса на формат и белый список символов
    $mask = "#^([A-z][A-z\d\-_]*)$#";
    if (!preg_match($mask, $_POST['url'], $arrMatches)) throw new Exception();

    // все ок вернем
    return $arrMatches[1];
}



/**
 * возвращает количесвто параметров командоной строки при вызове скрипта от linux
 * если параметров нет вернет 0
 * если вызов не от linux то вернем 0
 *
 *
 *  * @return int
 */
function router_getArgc()
{
    return (int)((isset($_SERVER['argc']) && ($_SERVER['argc']<=10 )) ? $_SERVER['argc'] : 0);
}


/**
 * создает/открывает лог.
 */
function logStart()
{
    file_put_contents('log.txt', "\r\n\r\n*****************************\r\nrouter: " . date("Y-m-d H:i:s") . " " . $_SERVER['REQUEST_METHOD'] . "\r\n", FILE_APPEND);
}

function logout($outTxt)
{
    file_put_contents('log.txt', date("Y-m-d H:i:s")." $outTxt\r\n", FILE_APPEND);
}

function redirect404()
{
    header("HTTP/1.0 404 Not Found");	// вернем 404
    include ('404.html');
}

/**
 * проверка  $_SERVER['REQUEST_URI'] для CLI вызова
 */
/*function testRequestUriCli()
{
    $requestUri =  ($_SERVER['REQUEST_URI']) ?? '';

    // читайте хелп на CRON_RUN_ENABLE
    if (CRON_RUN_ENABLE && $requestUri==='/run') $requestUri = '';

    // при CLI вызове  $_SERVER['REQUEST_URI'] должен быть пустой
    if ($requestUri !== '') throw new Error();
}*/