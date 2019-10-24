<?php

namespace Core;

class Controller
{
    protected $model;
    protected $view;
    protected $arrPostAnswer;   //ответ на post запрос браузера

/*    public function routeGET(){return abort(404);}
    public function routePOST(){return abort(404);}*/






    function runHtml($htmlFile)
    {
        require_once ($htmlFile);
    }


    function runModelView($viewFile,$classModel,$modelParams=null)
    {
        $this->model = new $classModel($modelParams);

        $this->model->getAll();

        require_once ($viewFile);
    }


    function runPostRequest($classModel,$modelParams)
    {
        $this->model = new $classModel($modelParams);

        $this->arrPostAnswer = $this->model->getAll();

        echo json_encode($this->arrPostAnswer);
    }


    protected function getOperationFmPost()
    {
        //проверка на корректность
        if (!isset($_POST['operation']) ) throw new Error('post_error');

        $len = strlen($_POST['operation']);
        if ($len <3 || $len > MAX_STRLEN)  throw new Error('post_error');

        //проверка на белый список символов
        if (!preg_match('#^[A-z\d\-_]{3,100}$#',$_POST['operation'])) throw new Error('post_error');

        //все ОК вернем операцию
        return $_POST['operation'];
    }

    protected function getPostInt($paramName)
    {
        //проверка на корректность
        if (!isset($_POST[$paramName]) ) throw new Exception('post_error');

        $len = strlen($_POST[$paramName]);
        if ($len <1 || $len > 11)  throw new Exception('post_error');

        //проверка регуляркой
        if (!preg_match('#^-?[\d]{1,10}$#',$_POST[$paramName])) throw new Exception('post_error');

        return (int)$_POST[$paramName];
    }

    protected function getPostDatetime($paramName)
    {
        //проверка на корректность
        if (!isset($_POST[$paramName]) ) throw new Exception('post_error');

        if (strlen($_POST[$paramName]) != 19) throw new Exception('post_error');

        //проверка регуляркой
        if (!preg_match('#^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$#',$_POST[$paramName])) throw new Exception('post_error');

        return (string)$_POST[$paramName];
    }

    public function runM($bundleName)
    {
        $bundleName = 'App\Bundles\\'.$bundleName;
        $this->Bundle = new $bundleName();
        $arrBundleData = $this->Bundle->getAll(); //дянные из модели

        return $arrBundleData;
    }

    public function runMV($bundleName,$viewName,array $paramsBundle=null)
    {
        $bundleName = 'App\Bundles\\'.$bundleName;

        $this->Bundle = new $bundleName($paramsBundle);

        $arrBlade = $this->Bundle->getAll();    //данные из модели

        return view($viewName,$arrBlade);
    }
}
