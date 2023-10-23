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
    public static function safeAssociArrayToObject(array $array, object $TargetClass): object
    {
        $instance = new $TargetClass();
        foreach ($instance as $key => $value) {
            if (isset($array[$key])) {
                $instance->$key = $array[$key];
            }
        }
        return $instance;
    }

}