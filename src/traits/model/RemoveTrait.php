<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\RemoveQueryTrait;

trait RemoveTrait
{
    use RemoveQueryTrait;
    public function remove(int $id = null):bool
    {
        if($id)
        {
            $this->id = $id;
        }
        if(!$this->removeQuery())
        {
           return false;
        }
        return true;
    }
}
