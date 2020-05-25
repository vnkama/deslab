<?php
    if (defined("ERROR_PHP")) die("error.php already included");
    define("ERROR_PHP",1);


/////////////////////////////////////////
/////////////////////////////////////////
//
// глбоальная переменнная - тип обработчика
// применятся СТРОГО В ОДНОМ СЛУЧАЕ !!!. при обработке фатальных ошибок
// для того чтобы определить куда выодить дамп ошибки (на экран, в лог или в через POST в браузер)

$global_err_routeType = null;




//
// первичная инициализация
//
function err_initError()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    set_error_handler('err_phpErrorHandler');
    set_exception_handler('err_phpExceptionHandler');

    //setFatalOutType(FATAL_OUT_ECHO);
}



//
//
//
function global_setRouteType($routeType)
{
    global $global_err_routeType;
    if (!is_null($global_err_routeType)) {
        die("setSql error");
    }
    $global_err_routeType = $routeType;
}



//
//
//
function err_getRouteType()
{
    global $global_err_routeType;
    if (!is_string($global_err_routeType)) {
        die("err_getRouteType error");
    }
    return $global_err_routeType;
}



//
// вызывается ядром PHP автоматически при внутренних ошибках PHP
//
function err_phpErrorHandler($errno, $errMsg, $errfile, $errline)
{
    err_handleFatalError_die('phpErrorHandler',$errfile, $errline, $errMsg);        //обязательно вызовет die

    //этот код не выполнится никогда

}



//
// вызывается автоматически при внутренних ошибках PHP
//
function err_phpExceptionHandler($ex)
{
    err_handleFatalError_die('phpExceptionHandler',$ex->getFile(), $ex->getLine(), $ex->getMessage());        //обязательно вызовет die

    //этот код не выполнится никогда
}

/**
 * унифицированный обработчикк фатальных-неустранимых ошибок, вызывается из:
 *
 * 1. из catch в main()
 * 2. phpErrorHandler()
 * 3. phpExceptionHandler()
 * 4. напрямую вызывать функцию нельзя !!!! вызывайтие throw new Error.
 *
 * @param $handlerName
 * @param $file
 * @param $line
 * @param $errMsg
 */
function err_handleFatalError_die($handlerName,$file,$line,$errMsg)
{
    //подготовим текст сообщения
    $errMsgRN = err_prepareErrorMessage($handlerName,$file,$line,$errMsg,"\r\n");

    $debugBacktrace 	= print_r(debug_backtrace(),true);
    $debugBacktrace		= substr($debugBacktrace,0,5000);       //ограничим длинну дампа 5000 байт
    $debugBacktrace 	= str_replace("\n","\r\n",$debugBacktrace);					    //чтобы проводник корректно выводил, добавим \r
    $debugBacktrace		= str_replace("/var/www/www-root/data/www",'',$debugBacktrace);   //вырежем пути, чтоб ыне захламлять

    $errMsgRN		= "$errMsgRN\r\n\r\ndebug_backtrace:\r\n$debugBacktrace";

    switch (err_getRouteType()) {
        case 'GET':
            $errMsgBR = str_replace("\r\n",'<br>',$errMsgRN);		//для вывода на экран заменим \r\n на <br>
            echo substr($errMsgBR,0,5000);                          //ограничим длинну дампа
            break;

        case 'POST':
            break;
    }

    logout($errMsgRN);  //выведем в лог



    die();
}


/**
 * готвит текст сообщения
 */
function err_prepareErrorMessage($handlerName,$file,$line,$errMsg,$lineEnd)
{
    $msg = "Error handled: $handlerName$lineEnd";
    $date = date("d-m-Y H:i:s");
    $msg .= "$date$lineEnd";
    $msg .= "File: $file, line: $line$lineEnd";
    $msg .= "messages: $errMsg$lineEnd$lineEnd";

    return $msg;
}


