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
class PdoConnManager
{
    public static function connect(string $connectionName = null):PdoConnection
    {
       if(!$connectionName)
       {
        $connectionName = 'default';
       }
       $options__db = [
            \PDO::ATTR_PERSISTENT => true,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        return new  PdoConnection(self::dsn($connectionName),"root","",$options__db);
    }

    private static function dsn(string $connecionName):string
    {
        $dsn = DB_CONNECTIONS[$connecionName];
        if($dsn['type'] == 'mysql')
        {
            return self::createMysqlDsn($dsn);
        }
        return "";
    }

    private static function createMysqlDsn(array $arrayConnection)
    {
        $string = $arrayConnection['type'].'host='.$arrayConnection['host'].';dbname='.$arrayConnection['database_name'].';charset=UTF8';
        if($arrayConnection['port'] !== "")
        {
            $string .= ';'.$arrayConnection['port'];
        }
        return $string;
    }
}