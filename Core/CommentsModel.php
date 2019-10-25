<?php


namespace Core;


class CommentsModel implements iModel
{
    protected $arrCommnets; //все комменты для показа

    public function __construct($params=null)
    {
        switch ($params['accessLevel']) {
            case 'guest':
                //обычный юзер видит только приянтые комментарии
                $this->arrCommnets = sql()->select("select * from Comments where moderStatus=2");
               // echo count($this->arrCommnets);
                break;

            case 'admin':
                // admin видит все записи
                $this->arrCommnets = sql()->select("select * from Comments where 1");
                break;

            default:
                throw new \Error();
                break;
        }
    }


    public function getAll():array
    {
        return $this->arrCommnets;
    }
}
