<?php
namespace GemFramework\Core;

use GemFramework\Database\PdoConnManager;
/**
 * @method int|null insert@uery()
 * @method array|null selectQuery()
 * @method int|false countQuery()
 * @method int|null updateQuery()
 * @method int|null deleteQuery()
 */
 class Table extends PdoConnManager
{
    /**
     * @param string|null $connectionName
     * in case of null use DEFAULT_CONNECTION_NAME
     */
    public function __construct(?string $connectionName = null)
    {
        parent::__construct($connectionName);
    }
    /**
     * @param string $insertQuery Sql insert query
     * @param array<string,mixed> $arrayBindKeyValue
     *
     * @return null|int
     * $query example: 'INSERT INTO users (name,email,password) VALUES (:name,:email,:password)'
     * arrayBindKeyValue Example [':name' => 'some new name' , ':email' => 'some@me.com , :password =>'si§d8x']
     * success : return last insertd id
     * you can call affectedRows() to get how many rows inserted
     * error: $this->getError();
     */
    public function insertQuery(string $insertQuery, array $arrayBindKeyValue = []): int|null
    {
        if($this->isConnected()) {
            if ($this->executeQuery($insertQuery, $arrayBindKeyValue)) {
                return (int) $this->lastInsertId();
            }
        }
        return null;
    }

    /**
     * @param array<mixed> $arrayBindKeyValue
     *
     * @return null|array<mixed>
     *
     * @$query example: 'SELECT * FROM users WHERE email = :email'
     * @arrayBindKeyValue Example [':email' => 'some@me.com']
     */
    public function selectQuery(string $selectQuery, array $arrayBindKeyValue = []): array|null
    {
        if(!$this->isConnected())
        {
            return null;
        }
         if ($this->executeQuery($selectQuery, $arrayBindKeyValue)) {
                return $this->fetchAllObjects($this->fetchAll());
        }
        return null;
    }

    /**
     * @param array<mixed> $arrayBindKeyValue
     *
     * @$query example: 'SELECT count(*) FROM users WHERE name LIKE :name'
     *
     * @arrayBindKeyValue Example [':name' => 'someone']
     */
    public function countQuery(string $selectCountQuery, array $arrayBindKeyValue = []): int|false
    {
        $result = false;
        if($this->isConnected()) {
            if ($this->executeQuery($selectCountQuery, $arrayBindKeyValue)) {
                $result = $this->fetchColumn();
            }
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $arrayBindKeyValue
     *
     * @return null|int
     * $query example: 'UPDATE users SET name = :name , isActive = :isActive WHERE id = :id'
     * arrayBindKeyValue Example [':name' => 'some new name' , ':isActive' => true , :id => 32 ]
     * in success return positive number affected rows and in error null
     */
    public function updateQuery(string $updateQuery, array $arrayBindKeyValue = []): int|null
    {
        $result = null;
        if($this->isConnected()) {
            if ($this->executeQuery($updateQuery, $arrayBindKeyValue)) {
                $result = $this->affectedRows();
            }
        }

        return $result;
    }

    /**
     * @param array<string,mixed> $arrayBindKeyValue
     *
     * @query example: 'DELETE users SET name = :name , isActive = :isActive WHERE id = :id'
     *
     * @arrayBindKeyValue example [':id' => 32 ]
     *
     * @success return positive number affected rows and in error null
     */
    public function deleteQuery(string $deleteQuery, array $arrayBindKeyValue = []): int|null
    {
        $result = null;
        if($this->isConnected()) {
            if ($this->executeQuery($deleteQuery, $arrayBindKeyValue)) {
                $result = $this->affectedRows();
            }
        }

        return $result;
    }

    /**
     * @param array<mixed> $arrayBind
     *
     * @success set this->affectedRows
     *
     * @error set this->error and return false
     */
    private function executeQuery(string $query, array $arrayBind): bool
    {
        if ($this->isConnected()) {
            $this->query($query);
            foreach ($arrayBind as $key => $value) {
                $this->bind($key, $value);
            }
            if (!$this->execute()) {
                return false;
            } else {
                return true;
            }
        }
        return false;
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

    private function fetchAllObjects(?array $rows = null):null|array
    {
        if(!$rows)
        {
            return null;
        }
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