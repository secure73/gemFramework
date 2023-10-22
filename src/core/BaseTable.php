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
}