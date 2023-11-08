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

    /**
     * @param string|null $connectionName	
     * @param array<mixed>|null $pdo_db_options
     */
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

    /**
     * @param array<mixed>|null $options
     * @return array<mixed>
     */
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
        /**@phpstan-ignore-next-line */
        if($dsn['type'] == 'mysql')
        {
            return self::createMysqlDsn($dsn);
        }
        /**@phpstan-ignore-next-line */
        return "";
    }

    /**
     * @param array<mixed> $arrayConnection
     */
    private function createMysqlDsn(array $arrayConnection):string
    {
        $port = '3306';
        if($arrayConnection['port'] !== "")
        {
            $port = $arrayConnection['port'];
        }
        /**@phpstan-ignore-next-line */
        return $arrayConnection['type'].':host='.$arrayConnection['host'].";port=$port;".'dbname='.$arrayConnection['database_name'].';charset=UTF8';
    }
}