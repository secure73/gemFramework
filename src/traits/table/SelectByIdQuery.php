<?php
namespace Gemvc\Traits\Table;
use Gemvc\Http\Response;

trait SelectByIdQuery
{
    
    /**
     * Inserts a single row into the database table
     * 
     * @return null|static The current instance with updated ID
     */
    public final function selectById(int $id): null|static
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