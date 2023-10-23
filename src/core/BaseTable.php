<?php
namespace GemFramework\Core;

use GemFramework\Database\PdoConnManager;
use Gemvc\Database\PdoQuery;

class BaseTable extends PdoQuery
{
    public function __construct(?string $connectionName = null)
    {
        parent::__construct(PdoConnManager::connect($connectionName));
    }

    private function fetchObject(array $row)
    {
        foreach($row as $key => $value)
        {
            if(property_exists($this, $key)){
                $this->$key = $value;
            }
        }
    }

    private function fetchAllObjects(array $rows):array
    {
        $objects = [];
            foreach($rows as $row)
            {
                $obj = new $this();
                $objects[] = $obj->fetchObject($row);
            }
        return $objects;
    }

}