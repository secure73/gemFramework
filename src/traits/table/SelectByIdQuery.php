<?php
namespace Gemvc\Traits\Table;
use Gemvc\Http\Response;

trait SelectByIdQuery
{
    
     /**
     * @return null|<$this> null in case not found and self otherwise
     * insert single row to table
     */
    public function selectById(int $id): null|object
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable');
            Response::internalError("Table is not set in class ".get_class($this))->show();
            die();
        }
        $result = $this->select()->where('id',$id)->limit(1)->run();
        if(!is_array($result))
        {
            $this->setError($this->getError());
            Response::internalError(get_class($this).": Failed to select:". $this->getError())->show();
            die();
        }
        if(count($result) == 0)
        {
            $this->setError('nothing found');
            return null;
        }
        return $result[0];
    }
}