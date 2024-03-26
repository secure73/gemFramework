<?php
namespace GemFramework\Traits\Model;

trait UpdateTrait
{

    public function update():self|null
    {       
        if($this->updateSingleQuery())
        {
            return $this;
        }
        return null;
        
    }
}
