<?php
namespace Gemvc\Traits\Table;

/**
 * @method single(int $id)
 * insert single row to table
 */
trait SingleQueryTrait
{
    /**
     * @return null|self null in case not found and self otherwise
     * @throws \Exception If failure also throws an exception
     * insert single row to table
     */
    public final function single(int $id): null|self
    {
        $table = $this->getTable();
        if (!$table) {
            $this->setError('Table is not set in function getTable');
            return null;
        }
        $result = $this->select()->where('id',$id)->limit(1)->run();
        if(!is_array($result))
        {
            $this->setError($this->getError());
            return null;
        }
        if(count($result) == 0)
        {
            $this->setError('nothing found');
        }
        $result = $result[0];
        foreach($result as $key=>$value)
        {
            $this->$key = $value;
        }
        return $result;
    }

}
