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
}
