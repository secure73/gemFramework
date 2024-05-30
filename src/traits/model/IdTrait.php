<?php
namespace Gemvc\Traits\Model;

/**
 * @method id(int $id)
 * @return self|null
 */
trait IdTrait
{
    public function id(int $id):self|null
    {
        $found = $this->selectByIdQuery($id);
        if(!$found)
        {
            $this->setError($this->getError());
            return null;
        }
        return $this;
    }
}
