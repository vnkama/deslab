<?php
namespace custom;

use \core\error;

    if (defined("DB_CFG_PHP")) throw new Error();
    define("DB_CFG_PHP",1);


function getDbCfg()
{
    $config = [];

    switch  (CFG_SERVER_TYPE)
    {
        case "SERVER_HOMESTEAD":
            $config['hostname'] = 'localhost';
            $config['dbname']   = 'db_jobtest_deslab';
            $config['username'] = 'jhon';
            $config['password'] = '12345';
             break;

        case "SERVER_WELLWEB":
            $config['hostname'] = 'localhost';
            $config['dbname']   = 'db_jobtest_deslab';
            $config['username'] = 'jhon';
            $config['password'] = '12345';
            break;

        default:
            throw new Error();
    }

    return $config;
}




