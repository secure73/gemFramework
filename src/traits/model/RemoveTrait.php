<?php
namespace Gemvc\Traits\Model;

use Gemvc\Traits\Table\RemoveQuery;

trait RemoveTrait
{
    use RemoveQuery;
    public function remove(int $id = null):bool
    {
        if($id)
        {
            $this->id = $id;
        }
        if(!$this->removeQuery($this->id))
        {
           return false;
        }
        return true;
    }
}
