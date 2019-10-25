<?php


namespace Core;


interface iModel
{
    public function __construct($params=null);

    /**
     * возвращает только базовые параметры Bundle
     * @return array
     */
    //public function getBase():array;


    /**
     * полностью загружаме все данные которые есть
     *
     * @return array
     */
    public function getAll():array;
}