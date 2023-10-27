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

    public function getError():string|null
    {
        return $this->connection->getError();
    }

    public function affectedRows():int|null
    {
        return $this->connection->affectedRows();
    }

    public function lastInsertId():int|null
    {
        return $this->connection->lastInsertId();
    }

    public function getQuery():string|null
    {
        return $this->connection->getQuery();
    }

    public function isConnected():bool
    {
        return $this->connection->isConnected();
    }

    public function query(string $query)
    {
        return $this->connection->query($query);
    }

    public function bind(string $param , mixed $bindValue):void
    {
        return $this->connection->bind($param,$bindValue);
    }

    public function execute():bool
    {
        return $this->connection->execute();
    }

    public function getExecutionTime():int|null
    {
       return $this->connection->getExecutionTime();
    }

    private function pdoQuery():PdoQuery|null
    {
        if($this->connection->isConnected())
        {
            return new PdoQuery($this->connection);
        }
        $this->error = $this->connection->getError();
        return null;
    }

    public function insertQuery(string $insertQuery , array $arrayBindKeyValue):int|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            return $pdoQuery->insertQuery($insertQuery,$arrayBindKeyValue);
        }
        return null;

    }

    public function DeleteQuery(string $deleteQuery , array $arrayBindKeyValue):int|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            return $pdoQuery->deleteQuery($deleteQuery,$arrayBindKeyValue);
        }
        return null;

    }

    public function updateQuery(string $updatetQuery , array $arrayBindKeyValue):int|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            return $pdoQuery->updateQuery($updatetQuery,$arrayBindKeyValue);
        }
        return null;

    }

    public function selectQuery(string $selectQuery , array $arrayBindKeyValue):array|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            return $pdoQuery->selectQuery($selectQuery,$arrayBindKeyValue);
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