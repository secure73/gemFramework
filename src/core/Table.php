<?php
namespace GemFramework\Core;

use GemLibrary\Database\QueryBuilder;

class Table extends QueryBuilder
{
    /**
     * @param string|null $connectionName
     * in case of null use DEFAULT_CONNECTION_NAME
     */
    public function __construct()
    {
        parent::__construct();
    }
}
