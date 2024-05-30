<?php
namespace Gemvc\Traits\Model;

use GemFramework\Traits\Table\UpdateQueryTrait;

trait DeleteTrait
{
    use UpdateQueryTrait;
    public function delete(int $id=null):bool
    {
        if($id)
        {
            $this->id = $id;
        }
        if(!$this->safeDeleteQuery($this->id))
        {
            return false;
        }
        return true;
    }
}
