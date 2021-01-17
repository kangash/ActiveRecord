<?php
namespace Engine\Core\DataBase;


use \ReflectionClass;
use \ReflectionProperty;
use Engine\DI\DI;

trait ActiveRecord
{

    protected $db;
    protected $di;
    protected $queryBuilder;

    public function __construct(DI $di, $id = 0)
    {
        $this->di           = $di;
        $this->db           = $this->di->get('connection');
        $this->queryBuilder = new QueryBuilder();

        if ($id) {
            $this->setId($id);  
        }
    }

    // CRUD
    public function createRecord()
    {
        $properties = $this->getIssetProperties();
        $sql = $this->queryBuilder
                ->insert($this->table)
                ->set($properties)
                ->sql();

        $result = $this->db->execute($sql);
        return $result;
    }


    public function readRecord(...$arguments)
    {
        $where = $this->getConditionWhere($arguments);

        $query = $this->queryBuilder->select()
                      ->from($this->getTable())
                      ->where($where)
                      ->sql();

        $find = $this->db->query($query, $this->queryBuilder->value);

        return isset($find) ? $find : null;
    }
    // $arguments[0] = array set
    // $arguments[0->1/0] => array where  
    public function updateRecord(...$arguments)
    {
        $set   = $this->getConditionSet($arguments);
        $where = $this->getConditionWhere($arguments);

        $query = $this->queryBuilder->update($this->table)
                           ->set($set)
                           ->where($where)
                           ->sql();

        $this->db->execute($query, $this->queryBuilder->value);
                                
    }   

    //auxiliary function - вспомогательные функции 
    public function getConditionSet($condition)
    {
        return $condition[0];
    }

    public function getConditionWhere($condition)
    {

        if (is_array($condition[0])) {
            unset($condition[0]);
        }

        $conditionWhere = [];
        foreach ($condition as $keyWhere => $valueWhere) {
            $conditionWhere += [$valueWhere => ''];
        }
        $properties = $this->getIssetProperties();

        $intersect = array_intersect_key($properties, $conditionWhere);
        return $intersect;

    }

    public function getIssetProperties()
    {
        $properties = [];
        $reflection = new ReflectionClass($this); //:
        $property = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);//:

        foreach ($property as $key => $property) {
            if(isset($this->{$property->getName()})) {
                $properties[$property->getName()] = $this->{$property->getName()};
            }
        }
        return $properties;
    }





}