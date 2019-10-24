<?php


    const DB_DATABASE   = 'db_jobtest_deslab';                  //доступк базе данных
    const DB_USER       = 'user_jobtest_deslab';
    const DB_PASSWORD   = 'TsT6vvhWSa';

    const CRON_RUN_ENABLE   = 1;                //при принудительном РУЧНОМ (а не по времени) вызове CRON, в $_SERVER['REQUEST_URI'] подставлется '/run'
    // CRON_RUN_ENABLE=1 убирает этот '/run'   , применяется при отладке


    const DIVBOTH 	    = "<div style='clear:both'></div>";     //сокрщение для удобства
    const UTF8			= 'UTF-8';                              //просто сокращение

    const MAX_STRLEN = 0xFF;                                    //длинна типчиного стринга

