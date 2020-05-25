<?php
    //массив роутов
    return [

        ['type' => 'get',   'routeName' => 'index',   'Controller' => 'CommentsController',    'func' => 'routeGET'],

        ['type' => 'get',   'routeName' => 'login',   'Controller' => 'LoginController',        'func' => 'routeGET'],
        ['type' => 'post',  'routeName' => 'login',   'Controller' => 'LoginController',        'func' => 'routePOST'],


        ['type' => 'get',   'routeName' => 'error404',     'Controller' => 'Controller',         'func' => 'routeView',
            'param' => ['templateFile' => 'template.html','title'=>'ошибка 404','body_class'=>'p03','body_html'=>'404.html']
        ]
    ];
