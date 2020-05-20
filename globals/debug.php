<?php

    const DEBUG_LOG_FILENAME = 'log.txt';

    /**
     * создает/открывает лог.
     */
    function log_initLog()
    {
        file_put_contents(DEBUG_LOG_FILENAME, "\r\n\r\n*****************************\r\nrouter: " . date("Y-m-d H:i:s") . " " . ($_SERVER['REQUEST_METHOD']??'') . "\r\n", FILE_APPEND);
    }

    function logout($outTxt)
    {
        file_put_contents(DEBUG_LOG_FILENAME, date("Y-m-d H:i:s")." $outTxt\r\n", FILE_APPEND);
    }

    /**
     * для отладки
     * @param $par
     */
    function echobr($par)
    {
        echo "${par}<br>";
    }
