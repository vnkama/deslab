<?php

namespace Core;

class Controller
{
    protected $model;
    protected $view;

    protected $data2View;   //массив данных для передачи во view

    function __construct()
    {
        $this->data2View['accessLevel'] = getAccessLevel();   //массив данных для передачи во view
    }


    protected $arrPostAnswer;   //ответ на post запрос браузера


    public function runHtml($htmlFile)
    {
        require_once ($htmlFile);
    }


    public function runModelView($viewFile,$classModel,$modelParams=[])
    {
        //$this->data2View['accessLevel']  = getAccessLevel();

        $modelParams['accessLevel'] = $this->data2View['accessLevel'];

        $classModelName = __NAMESPACE__.'\\'.$classModel;
        $this->model = new $classModelName($modelParams);

        //$aa = $this->model->getAll();
        //echo count($aa);
        //echo count($this->data2View);


        echo $this->data2View['accessLevel'];

        $toView = $this->data2View;
        $toView['arrComments'] = $this->model->getAll();
        //echo       count($toView);
        //print_r($toView);

        //print_r($toView['arrComments']);
        echo $toView['accessLevel'];


        require_once $viewFile;
    }


    public function runPostRequest($classModel,$modelParams)
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
