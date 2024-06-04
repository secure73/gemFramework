<?php

namespace Gemvc\Core;
use Gemvc\Database\QueryExecuter;

class TableGenerator extends QueryExecuter{

    public function __construct() {
        parent::__construct();
    }

    public function createTableFromObject(Table $object) :bool{
        if(!$this->isConnected()){
            return false;
        }
        $tableName = $object->getTable();
        if(!$tableName){
            $this->setError("function tableName return null string. please define it and git table a name");
            return false;
        }
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties();
        
        $columns = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyType = $this->getPropertyType($object, $propertyName);

            switch ($propertyType) {
                case 'int':
                case 'integer':
                    $type = 'INT(11)';
                    break;
                case 'string':
                    $type = 'VARCHAR(255)';
                    break;
                case 'DateTime':
                    $type = 'DATE';
                    break;
                default:
                    $type = 'TEXT';
            }

            if ($propertyName == 'id') {
                $type .= ' AUTO_INCREMENT PRIMARY KEY';
            }

            $columns[] = "$propertyName $type";
        }
        $columnsSql = implode(", ", $columns);
        $query = "CREATE TABLE IF NOT EXISTS $tableName ($columnsSql);";

        try {
            $this->query($query);
            if(!$this->execute()){
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            $this->setError($exception->getMessage());
        }
        return false;
    }

    private function getPropertyType($object, $property) {
        $type = gettype($object->$property);
        if ($type === 'object' && get_class($object->$property) === 'DateTime') {
            return 'DateTime';
        }
        return $type;
    }

}
