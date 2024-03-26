<?php

namespace GemFramework\Core;

use GemFramework\Traits\Table\ActivateQueryTrait;
use GemFramework\Traits\Table\DeactivateQueryTrait;
use GemFramework\Traits\Table\InsertSingleQueryTrait;
use GemFramework\Traits\Table\RemoveQueryTrait;
use GemFramework\Traits\Table\SafeDeleteQueryTrait;
use GemFramework\Traits\Table\SelectQueryTrait;
use GemFramework\Traits\Table\UpdateQueryTrait;

class Table extends TableBase
{
    use SelectQueryTrait;
    use InsertSingleQueryTrait;
    use RemoveQueryTrait;
    use UpdateQueryTrait;
    use SafeDeleteQueryTrait;
    use ActivateQueryTrait;
    use DeactivateQueryTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function setTable(): string
    {
        return '';
    }

    /**
     * @param int $id
     * @return null|self
     * if found, set item values to this object and return this object
     */
    public function single(int $id):null|self
    {
        $res = $this->selectQuery("SELECT * FROM {$this->setTable()} WHERE id = :id LIMIT 1",[':id'=>$id]);
        if($res === false)
        {
            $this->setError('Failed to select row from table:'.$this->getError());
            return null;
        }
        if(count($res) == 0)
        {
            $this->setError('No row found with id:'.$id);
            return null;
        }
        $res = $res[0];
        /*@phpstan-ignore-next-line*/
        foreach ($res as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }
}
