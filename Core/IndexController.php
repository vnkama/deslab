<?php

namespace Core;

//use Core\Controller;

class IndexController extends Controller
{
    function run()
    {
        $this->data2View = [];
        $this->data2View['title']       = 'Комментарии';
        $this->data2View['body_html']   = 'index.html';
        $this->data2View['body_class']  = 'p01';


        $this->runModelView('template.html',__NAMESPACE__.'\\CommentsModel',['accessLevel' => getAccessLevel()]);
    }

/*    function __construct()
    {
        parent::__construct('indexPage.php');
    }

    function prepareModel()
    {

        //выборка за текущий месяц
        $q="select s1.idWorker as id,name,family, fotoFile, profession,fixSalary,bonusSalary,totalSalary from (select w.id as idWorker,name,family, fotoFile, profession from workers w inner join professions pr on w.idProfession=pr.id where 1) as s1
inner join payments py on py.idWorker = s1.idWorker where date_format(py.tsDate, '%Y%m') = date_format(now(), '%Y%m')";
        $arrWorkers =  sql()->select($q);

        $this->arrPostAnswer['workers'] = sql()->select($q);



        // проверка всех изображений
        foreach ($arrWorkers as $arrWorker) {
            $fullFotoFilename = './img/' . $arrWorker['fotoFile'] . '.jpg';


            if (is_file($fullFotoFilename)) {
                $miniFotoFilename = './img/' . $arrWorker['fotoFile'] . 's.jpg';
                if (!is_file($miniFotoFilename)) {
                    //миниатюры нет - создадим
                    $jpg = new ImageJpg();
                    $jpg->load($fullFotoFilename);

                    $jpg->resizeMini(50);

                    $jpg->save($miniFotoFilename);
                }
            }
        }

        return $arrWorkers;
    }

    function prepareView()
    {
        $toView['title'] = 'Главная';
        $toView['body_class'] = 'p01';
        $toView['script_file'] = 'index.js';
        $toView['body_file'] = 'body_index.html';
        //$toView['template_file'] = 'StandartTemplate.php';

        return $toView;
    }

    function show()
    {
        $this->model = $this->prepareModel();

        $toView = $this->prepareView();

        require_once ('StandartTemplate.php');
    }


    public function OnControl()
    {
        logout(__FUNCTION__);

        try {
            $oper = $this->getPostOperation();

            switch ($oper) {
                case 'getSalaries':
                    $this->onControl_getSalaries();
                    break;

                default:
                    logout(__FUNCTION__.'default');
                    throw new Exception('post_error');
            }
        } catch (Exception $ex) {
            logout('Exception' . $ex->getFile() . $ex->getLine());
            $this->arrPostAnswer['answerResult'] = $ex->getMessage();
        }

        echo json_encode($this->arrPostAnswer);		//вывод массива результата
    }


    protected function onControl_getSalaries()
    {


        $firstDate   = $this->getPostDatetime('firstDate');
        $lastDate    = $this->getPostDatetime('lastDate');

        //делаем запрос


        $q="select s1.idWorker as id,name,family, fotoFile, profession,fixSalary,bonusSalary,totalSalary from (select w.id as idWorker,name,family, fotoFile, profession from workers w inner join professions pr on w.idProfession=pr.id where 1) as s1
    inner join payments py on py.idWorker = s1.idWorker where py.tsDate >= '$firstDate' and py.tsDate <= '$lastDate'";
        $this->arrPostAnswer['workers'] = sql()->select($q);

        $this->arrPostAnswer['answerResult'] = 'ok';
    }*/
}
