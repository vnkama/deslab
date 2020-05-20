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

//use Core\Controller;


use Exception;

if (!defined("START_PHP")) die('Directly run of router.php is rejected');
    if (defined("ROUTER_PHP")) die('Seconary include router.php is rejected');
    define('ROUTER_PHP',1);

    //единая точка входа для всех запросов к сайту
    //запросы вида GET, POST, и вызовы от linux, втч cron, exec


    require_once 'config.php';       //настройки
    require_once 'routes.php';

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

///////////////////////
//      глобальные переменные
//

        $g_sql          = null;     // доступ через функции sql()
        $g_accessLevel  = 'guest';  // getAccessLevel(),




try {

        //запуск лог файла
        logStart();

        //подключаемся к базе данных
        $g_sql = new Mysql();




        exit();


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
            $arrPostAnswer =  ['errorMessage' => $outBR];
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

function getAccessLevel()
{
    global $g_accessLevel;
    return $g_accessLevel;
}

/**
 * @param $accessLevel
 * @param int $toSession    если 1 , то записываем и в SESSION тоже
 */
function setAccessLevel($accessLevel,$toSession=0)
{
    global $g_accessLevel;
    $g_accessLevel = $accessLevel;

    if ($toSession){
        $_SESSION['accessLevel'] = $accessLevel;
    }
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
 * фактический адрес страницы (оно же имя роута) на которую был запрос хранится в поле $_POST['routeName']
 *
 * прводятся проверкт корректности адреса
 *
 * @return mixed - фактичсеский адрес старницы вида 'index'  без домена,слешей параметров итп
 * @throws Exception
 */
function router_getRouteName_POST()
{
    if ($_SERVER['REQUEST_URI'] !== '/start.php') throw new Exception($_SERVER['REQUEST_URI']);

    if (!isset($_POST['routeName'])) throw new Exception();

    $len = strlen($_POST['routeName'] ?? '');
    if ($len < 2 || $len > MAX_STRLEN) throw new Exception();


    //проверка адреса на формат и белый список символов
    $mask = "#^([A-z][A-z\d\-_]*)$#";
    if (!preg_match($mask, $_POST['routeName'], $arrMatches)) {
        throw new Exception();
    }

    // все ок вернем
    return $arrMatches[1];
}








function redirect404()
{
    header("HTTP/1.0 404 Not Found");	// вернем 404 браузеру
    //
   // include ('404.html');

    $className = __NAMESPACE__.'\\'.'Controller';
    $controller = new $className();
    $controller->routeView(['templateFile' => 'template.html','title'=>'ошибка 404','body_class'=>'p03','body_html'=>'404.html']);
    exit();
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