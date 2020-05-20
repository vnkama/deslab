<?php
    function err_initError()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        set_error_handler('error_phpErrorHandler');
        set_exception_handler('error_phpExceptionHandler');

        //setFatalOutType(FATAL_OUT_ECHO);
    }

    //
    // вызывается автоматически при внутренних ошибках PHP
    //
    function error_phpErrorHandler($errno, $errMsg, $errfile, $errline)
    {
        err_catchFatalError('phpErrorHandler',$errfile, $errline, $errMsg);        //обязательно вызовет die

        //этот код не выполнисятникогда

    }

    //
    // вызывается автоматически при внутренних ошибках PHP
    //
    function error_phpExceptionHandler($ex)
    {
        err_catchFatalError('phpExceptionHandler',$ex->getFile(), $ex->getLine(), $ex->getMessage());        //обязательно вызовет die

        //этот код не выполнисятникогда
    }

/**
 * унифицированный обработчикк фатальных-неустранимых ошибок, вызывается из:
 *
 * 1. из catch в main()
 * 2. из phpErrorHandler()
 * 3. phpExceptionHandler
 * 4/ напрямую вызывать функцию нельзя !!!! вызывайтие throw new Error.
 *
 * @param $handlerName
 * @param $file
 * @param $line
 * @param $errMsg
 */
    function err_catchFatalError($handlerName,$file,$line,$errMsg)
    {
        //подготовим текст сообщения
        $errMsgRN = error_prepareErrorMessage($handlerName,$file,$line,$errMsg,"\r\n");

        $debugBacktrace 	= print_r(debug_backtrace(),true);
        $debugBacktrace		= substr($debugBacktrace,0,5000);       //ограничим длинну дампа 5000 байт
        $debugBacktrace 	= str_replace("\n","\r\n",$debugBacktrace);					    //чтобы проводник корректно выводил, добавим \r
        $debugBacktrace		= str_replace("/var/www/www-root/data/www",'',$debugBacktrace);   //вырежем пути, чтоб ыне захламлять

        $errMsgRN		= "$errMsgRN\r\n\r\ndebug_backtrace:\r\n$debugBacktrace";

        switch (global_getRouteType()) {
            case 'get':
                $errMsgBR = str_replace("\r\n",'<br>',$errMsgRN);		//для вывода на экран заменим \r\n на <br>
                echo substr($errMsgBR,0,5000);   //ограничим длинну дампа
                break;
        }

        logout($errMsgRN);  //выведем в лог



        die();
    }


    /**
     * готвит текст сообщения
     */
    function error_prepareErrorMessage($handlerName,$file,$line,$errMsg,$lineEnd)
    {
        $msg = "Error handled: $handlerName$lineEnd";
        $date = date("d-m-Y H:i:s");
        $msg .= "$date$lineEnd";
        $msg .= "File: $file, line: $line$lineEnd";
        $msg .= "messages: $errMsg$lineEnd$lineEnd";

        return $msg;
    }


