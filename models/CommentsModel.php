<?php


namespace models;



class CommentsModel implements \core\iModel
{
    protected $arrCommnets; //все комменты для показа

    public function __construct($params=null)
    {
        if ($params['isAdmin']) {
            // admin видит все записи
            $this->arrCommnets = sql()->select("select * from Comments where 1");
        }
        else {
            //обычный юзер видит только приянтые комментарии
            $this->arrCommnets = sql()->select("select * from Comments where moderStatus=2");
        }
    }


    public function getAll():array
    {
        return $this->arrCommnets;
    }
}
