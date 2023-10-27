<?php
namespace GemFramework\Core;

use GemFramework\Database\PdoConnManager;
use GemLibrary\Database\PdoConnection;
use GemLibrary\Database\PdoQuery;


/**
 * property: connection PdoConnection
 * method: pdoQuery(): PdoQuery
 * @property PdoConnection|null $connection
 */
 class BaseTable 
{
    public ?PdoConnection $connection =null;
    public function __construct(?string $connectionName = null)
    {
        $this->connectionName = PdoConnManager::connect($connectionName);
    }

    public function pdoQuery():PdoQuery|null
    {
        if($this->connection->isConnected())
        {
            return new PdoQuery($this->connection);
        }
        return null;
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
                $obj->fetchObject($row);
                $objects[] = $obj;
            }
        return $objects;
    }

}