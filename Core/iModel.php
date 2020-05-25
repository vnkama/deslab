<?php
namespace Core;


//
//
//
interface iModel
{
    public function __construct($params=null);

    /**
     * полностью возвращает все данные которые есть
     *
     * @return array
     */
    public function getAll():array;
}