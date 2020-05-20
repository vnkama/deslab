<?php 
namespace core;

//if (!defined('ROUTER_PHP'))	{die("Directly run of this file: {".__FILE__."} is rejected");}

use \Error;

const MYSQL_MAX_STRLEN                    = 8000;   //макс длинна стрнга в cSql




/**
 * Class cSql
 *
 * класс - проклдка для доступа к mysql
 *
 */
class Mysql
{ 
	private $dblink;

    /**
     * простейшая проверка строки запроса перед вызовом функций mysqli
     * @param $str
     */
    function testString($str)
    {
        if (!is_string($str)) throw new Error();

        $len=strlen($str);
        if ($len < 2 || $len > MYSQL_MAX_STRLEN) throw new Error();
        return 1;
    }

    public function __construct($config)
    {

        $this->dblink = mysqli_connect(
            'localhost',
            $config['username'],
            $config['password'],
            $config['database']
        );

        if (!$this->dblink) throw new Error();


        if (!mysqli_select_db($this->dblink, $config['database']))  throw new Error();

        // указывать кодировку нужно втч для mysqli_real_escape_string
        if (!mysqli_set_charset($this->dblink, 'utf8mb4')) throw new Error();
    }


    /**
     * фунцкия используется для INSERT
     *
     * @param $q        полный текст запросатипа: insert into ...
     *
     *
     */
    public function insert($q)
    {
        $this->testString($q);

        if (!mysqli_query($this->dblink, $q)) throw new Error();
    }


    /**
     * @param $q        полный текст запроса типа: select into ...
     * @return mixed
     *
     *функция вызывается когда запрос должен вернуть СТРОГО только одно значение или одно . например запрос типа: select count(*) from ...
     *
     * если рядов  в ответе несколько вызовет ошибку
     */
    public function selectValue($q)
    {
        $this->testString($q);

        $mysql_result=mysqli_query($this->dblink, $q);

        if (!$mysql_result)  throw new Error();

        $arrRow=mysqli_fetch_array($mysql_result,MYSQLI_NUM);

        if (!$arrRow || count($arrRow) != 1)  throw new Error();

        mysqli_free_result($mysql_result);

        return $arrRow[0];
    }

    /**
     * @param $q
     * @return array|null
     *
     * возвращает только одну страку результата !!!
     * если несколько то это ошибка
     * если ответа нет - это ошибка
     * !! осовбождается результат
     *
     */
    public function selectRecord($q)
    {
        $this->testString($q);

        $mysql_result=mysqli_query($this->dblink, $q);
        if (!$mysql_result)  throw new Error();
        $arrRes=null;

        if (mysqli_num_rows($mysql_result) == 1) {
            $arrRow = mysqli_fetch_assoc($mysql_result);	//$arrRow 1-мерный массив
            if (!$arrRow || !count($arrRow))  throw new Error();
        }
        else {
            //в функции может быть в ответе только
            throw new Error();
        }

        mysqli_free_result($mysql_result);

        return $arrRow;
    }


    /**
     * отрабатыват запрос select
     *
     * @param $q
     *
     * @return array
     * возвращает null если запрос пустой
     * возвращает двумерный ассоциативный масиив если ответ есть
     *
     *
     */
    public function select($q)
    {
        //если ответ пустой,	то вернет  arrResult = null

        $this->testString($q);

        $mysql_result=mysqli_query($this->dblink, $q);
        if  (!$mysql_result) throw new Error();

        $arrRows = [];
        while ($row = mysqli_fetch_assoc($mysql_result)) {
            $arrRows[]=$row;
        }

        mysqli_free_result($mysql_result);

        return $arrRows;	//если ответ пустой то вернет null
    }

    /**
     * просто вызов  mysqli_real_escape_string
     */
    public function escape($str)
    {
        return mysqli_real_escape_string($this->dblink,$str);
    }



}
