<?php
namespace GemFramework\Core;

use GemFramework\Database\PdoConnManager;
use GemLibrary\Database\PdoQuery;

class Table extends PdoQuery
{
    /**
     * @param string|null $connectionName
     * in case of null use DEFAULT_CONNECTION_NAME
     */
    public function __construct(?string $connectionName = null)
    {
        $connectionName ? $connectionName : DEFAULT_CONNECTION_NAME;
        $conn = new PdoConnManager($connectionName);
        parent::__construct($conn);
    }

    protected function fetchAllObjects(array $selectQueryResult)
    {
        $result = array();

        foreach ($selectQueryResult as $item) {

            $result[] = $this->fetchObject($item);
            
        }
        return $result;
        
    }

    protected function fetchObject(array $selectQueryResult)
    {
        $instance = new $this();
        foreach ($selectQueryResult as $key => $value) {

            $instance->$key = $value;
            
        }
        return $instance;
    }
}
