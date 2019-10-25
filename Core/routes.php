<?php
    //массив роутов
    $arrRoutes = [
//        ['type' => 'get',   'routeName' => 'index',   'Controller' => 'IndexController',    'func' => 'runHtml', 'param' => 'template.php'],
        ['type' => 'get',   'routeName' => 'index',   'Controller' => 'IndexController',    'func' => 'run'],
        ['type' => 'get',   'routeName' => '404',     'Controller' => 'Controller',         'func' => 'run']
    ];
