<?php
namespace GemFramework\Core;

use GemFramework\Database\PdoConnManager;
use GemLibrary\Database\PdoConnection;
use GemLibrary\Database\PdoQuery;


/**
 * property: connection PdoConnection
 * method: pdoQuery(): PdoQuery
 * in case of null use DEFAULT_CONNECTION_NAME
 * @property PdoConnection|null $connection
 */
 class BaseTable 
{
    public ?string $error = 'no connection initialized';
    private string $connection_name;
    private PdoConnection $connection;

    /**
     * @param string|null $connectionName
     * in case of null use DEFAULT_CONNECTION_NAME
     */
    public function __construct(?string $connectionName = null)
    {
        if(!$connectionName)
        {
            $this->connection_name = DEFAULT_CONNECTION_NAME;
        }
        else
        {
            $this->connection_name = $connectionName;
        }
        $this->connection = PdoConnManager::connect($this->connection_name);
        $this->setError();   
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
        $res = $this->connection->getQuery();
        $this->setError();
        return $res;
    }

    public function isConnected():bool
    {
        $res = $this->connection->isConnected();
        $this->setError();
        return $res;
    }

    public function query(string $query)
    {
        $res = $this->connection->query($query);
        $this->setError();
        return $res;
    }

    public function bind(string $param , mixed $bindValue):void
    {
        $this->connection->bind($param,$bindValue);

    }

    public function execute():bool
    {
        $res = $this->connection->execute();
        $this->setError();
        $this->connection->secure();
        return $res;
    }

    public function getExecutionTime():int|null
    {
       $res = $this->connection->getExecutionTime();
       $this->setError();
       return $res;
    }

    public function insertQuery(string $insertQuery , array $arrayBindKeyValue):int|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            $res = $pdoQuery->insertQuery($insertQuery,$arrayBindKeyValue);
            $this->setError();
            return $res;
        }
        return null;

    }

    public function DeleteQuery(string $deleteQuery , array $arrayBindKeyValue):int|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            $res = $pdoQuery->deleteQuery($deleteQuery,$arrayBindKeyValue);
            $this->setError();
            return $res;
        }
        return null;

    }

    public function updateQuery(string $updatetQuery , array $arrayBindKeyValue):int|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            $res = $pdoQuery->updateQuery($updatetQuery,$arrayBindKeyValue);
            $this->setError();
            return $res;
        }
        return null;

    }

    public function selectQuery(string $selectQuery , array $arrayBindKeyValue):array|null
    {
        $pdoQuery = $this->pdoQuery();
        if($pdoQuery)
        {
            $res = $pdoQuery->selectQuery($selectQuery,$arrayBindKeyValue);
            $this->setError();
            return $res;
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

    public function pdoQuery():PdoQuery|null
    {
        if($this->isConnected())
        {
            return new PdoQuery($this->connection);
        }
        $this->error = $this->setError();
        return null;
    }

    private function setError()
    {
        $this->error = $this->connection->getError();
    }

}