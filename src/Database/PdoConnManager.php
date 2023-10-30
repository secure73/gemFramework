<?php
namespace GemFramework\Database;
use GemLibrary\Database\PdoConnection;

/**
 * @param string|null $connectionName
 * @return \Gemlibrary\Database\PdoConnection
 * in case of null , it will use default connection
 * Manage Pdo connections based on config.php file
 * you shall just type connection name and it will care for the rest of the connection
 */
class PdoConnManager extends PdoConnection
{

    public function __construct(?string $connectionName = null , ?array $pdo_db_options = null)
    {
        $pdo_db_options = $pdo_db_options ? $pdo_db_options :  [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        $connectionName = $connectionName ? $connectionName : DEFAULT_CONNECTION_NAME;
        parent::__construct($this->dsn($connectionName),$this->getUserName($connectionName),$this->getPassword($connectionName),$this->getOptions());
    }

    private function getUserName(string $connecionName):string
    {
        return DB_CONNECTIONS[$connecionName]['username'];
    }
    private function getPassword(string $connecionName):string
    {
        return DB_CONNECTIONS[$connecionName]['password'];
    }

    private function getOptions(?array $options = null):array
    {
        if(!$options)
        {
            return [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ];
        }
        return $options;
    }

    private  function dsn(string $connecionName):string
    {
        $dsn = DB_CONNECTIONS[$connecionName];
        if($dsn['type'] == 'mysql')
        {
            return self::createMysqlDsn($dsn);
        }
        return "";
    }

    private function createMysqlDsn(array $arrayConnection):string
    {
        $string = $arrayConnection['type'].'host='.$arrayConnection['host'].';dbname='.$arrayConnection['database_name'].';charset=UTF8';
        if($arrayConnection['port'] !== "")
        {
            $string .= ';'.$arrayConnection['port'];
        }
        return $string;
    }
}