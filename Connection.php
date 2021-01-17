<?php 

namespace Engine\Core\Database;


use \PDO;
use Engine\Core\Config\Config;

class Connection 
{
    private $link;

    private $config;

    public function __construct()
    {
        $this->connect();

    }

    private function connect()
    {
        // подключение
        $config = Config::group('database');

        $dsn = 'mysql:host='.$config['host'].';dbname='.$config['db_name'].';charset='.$config['charset'];
       // $dsn = 'mysql:host=localhost;dbname=developer;charset=utf8';
        $this->link = new PDO($dsn, $config['username'], $config['password']); 
        return $this;
    }

    public function execute($sql, $values = []) // выполняет какойто запрос
    {
        $sth = $this->link->prepare($sql); //ptepare() подготавливает запрос к его выполнению и записываем индентификатор выражения в sth
        
        return $sth->execute($values); 
    }

    public function query($sql, $values = [], $statement = PDO::FETCH_OBJ) // С помощью данной функции мы можем что-то получить
    {
        $sth = $this->link->prepare($sql);
        $sth->execute($values);
        // $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        $result = $sth->fetchAll($statement); //вызываем метод фетчь олл который возвращает функция екзекют и говорим этому методу, что хотим получить ассоциативный масив
        
        if($result === false) {
            return [];
        }
        
        return $result;
    }

    public function lastInsertId()
    {
        return $this->link->lastInsertId();
    }


}










?>