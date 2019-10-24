<?php
/*главная страница*/

/*вызовем html шаблон старницы*/
required_once('StandartTemlate.php');



try {

    //подключаемся к базе данных
    $sql = new core\Mysql();

    //
    if (!is_array($_SERVER) || empty($_SERVER) || !is_array($_GET) || !is_array($_POST)) throw new Error();


    //$_SERVER['REQUEST_URI'] 	содержит:

    //для обычного GET запрсоа (перехода по гиперссылке):	'/myadress'
    //для GET запроса с параметрами:				        '/myadress?logout=1'
    //принудителный вызове cron через ISP manager/ 	        '/run'					этот /run принудительно подставляет апач
    //http-POST вызов от браузера					        '/start.php'			подставляем мы при POST запросе
    //cron											        не определен, isset(..) == false


    //кол-во аргументов из командной строки
    //при вызове от HTTP $argc = 0
    $argc = \core\router_getArgs();

    $HttpRequestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

    //кроме http запрос может быть и от linux !!!!!
    $RequestMethod = '';
    $internalUrl = '';

    \core\logout("HttpRequestMethod $HttpRequestMethod");


    if ($HttpRequestMethod == 'GET' && !count($_GET) && !count($_POST)) {
        // GET без параметров
        $RequestMethod = 'GET';

        $internalUrl = \core\router_getShortUrlFromGET(0);
    }
    elseif ($HttpRequestMethod == 'GET' && count($_GET) && !count($_POST)) {
        // GET c параметрами
        $RequestMethod = 'GET_PARAMS';
        $internalUrl = router_getShortUrlFromGET(1);

    }
    elseif ($HttpRequestMethod == 'POST' && !count($_GET) && count($_POST)) {
        // POST запрос
        $RequestMethod = 'POST';
        if (count($_GET) || !count($_POST)) throw new Error();
        $internalUrl = \core\router_getUrlFromPOST();
    }
    else {
        // неизвествнй тип запроса
        throw new Error();
    }


    logout("internalUrl $internalUrl");

    switch ($internalUrl) {
        case 'index':
            switch ($RequestMethod){
                case 'GET':
                    //просто показ страницы
                    //$controller = new IndexController();
                    //$controller->show();
                    break;

                case 'POST':
                    logout('post-request');

                    //запросы от органов управления
                    //$controller = new IndexController();
                    //$controller->OnControl();
                    break;

                default:
                    throw new Error();  //неизвестный тип запроса
            }
            break;

        case 'add-worker':
            //$controller = new AddWorkerController($RequestMethod);
            break;

        case 'add-bonus':
            //$controller = new AddWorkerController($RequestMethod);
            break;

        case 'set-dollar':
            //$controller = new AddWorkerController($RequestMethod);
            break;

        case '404':
            include('404.html');
            //$controller = new AddWorkerController($RequestMethod);
            break;

        default:
            \core\redirect404();
            break;
    }



}catch (Error $ex) {
    $out = 'catch Error<br> file: '.$ex->getFile(). "<br>line: ".$ex->getLine(). "<br>message: ". $ex->getMessage();
    if ($RequestMethod == 'GET')
        echo $out;
    else
        logout($out);
    die();

}catch (Exception $ex) {
    $out = 'catch Exception<br> file: '.$ex->getFile(). "<br>line: ".$ex->getLine(). "<br>message: ". $ex->getMessage();
    if ($RequestMethod == 'GET')
        echo $out;
    else
        logout($out);
    die();
}

logout('EXIT');

exit();



/**
 * генератор кода для скрытого поля формы
 */
function generateHiddenValue()
{
    return md5(date('Y-m-d').'sometext');
}

/**
 * @return bool|mixed
 *
 * достает из POST имя клиента. и проверяет кореектность
 *
 * return - проверенное имя или false при ошибке
 */
function post2name()
{
    if (!isset($_POST['name'])) return false;
    $name = $_POST['name'];

    if (!preg_match('#^[A-zА-ЯЁа-яё][A-zА-ЯЁа-яё\d\s_\-]{0,63}$#u',$name)) return false;

    return $name;
}

/**
 * @return bool|mixed
 *
 * достает из POST e-mail клиента. и проверяет коректность
 *
 * return - проверенное имя или false при ошибке
 */
function post2email()
{
    if (!isset($_POST['email'])) return false;
    $email = $_POST['email'];
    if (!preg_match('#^([a-z0-9_-]{1,127}\.){0,4}[a-z0-9_-]{1,127}@[a-z0-9_-]{1,127}(\.[a-z0-9_-]{1,127}){0,4}\.[a-z]{2,6}$#',$email)) return false;

    return $email;
}

/**
 * @return bool|mixed
 *
 * достает из POST сообщение клиента.
 * проверяет коректность. проверка дублирует провеку в браузером  нужда только при взломе
 *
 * htmlentites от инъекций
 *
 * return - проверенное имя или false
 */
function post2message()
{
    if (!isset($_POST['message'])) return false;
    $message = $_POST['message'];

    $len = strlen($message);
    if (!$len || $len > 1000 ) return false;

    //проверка на белый спиcок символов
    // если найдем любой символ кроме перечисленных - ошибка
    if (preg_match('/[^A-zА-Яа-яЁё\d\s!@#$%^&*(){}_+=?<>,.;:|№\[\]\-\"\\\'\\\/]/',$message)) return false;

    // защита от XSS
    $message = htmlentities($message);


    return $message;
}


















/**
 * @param $mess
 * отладочный вывод в лог файл
 */
function logout($mess)
{
    file_put_contents('log.txt',"\r\n".date("Y-m-d H:i:s")." >$mess\r\n",FILE_APPEND);
}

function logout_s($mess)
{
    file_put_contents('log.txt',"\r\n".date("Y-m-d H:i:s")." >$mess START\r\n",FILE_APPEND);
}

function logout_r($mess)
{
    file_put_contents('log.txt',"\r\n".date("Y-m-d H:i:s")." >$mess RETURN\r\n",FILE_APPEND);
}


spl_autoload_register(
    function ($class){
        //echo "class1:".$class.'<br>';
        $class = '../'.str_replace('\\','/',$class).'.php';
        //echo "class2:".$class.'<br>';
        require_once $class;
        //echobr( 'ret');
    }
);
