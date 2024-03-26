<?php
namespace GemFramework\Traits\Model;

use GemFramework\Traits\Table\ActivateQueryTrait;

trait ActivateTrait
{
    use ActivateQueryTrait;
    public function activate(int $id = null):bool
    {
        if($id)
        {
            $this->id = $id;
        }
        
        if(!$this->activateQuery())
        {
            return false;
        }
        
        return true;
    }
}