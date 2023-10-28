<?php

namespace App\Table;
use GemFramework\Core\BaseTable;
use GemFramework\Traits\Table\ActivateTrait;
use GemFramework\Traits\Table\InsertTrait;
use GemFramework\Traits\Table\RemoveTrait;
use GemFramework\Traits\Table\SafeDeleteTrait;
use GemFramework\Traits\Table\SelectTrait;
use GemFramework\Traits\Table\UpdateTrait;


class UserTable extends BaseTable
{
    public int $id;
    public string $email;
    public string $password;
    
    use InsertTrait;
    use UpdateTrait;
    use SafeDeleteTrait;
    use RemoveTrait;
    use ActivateTrait;
    use SelectTrait;
    public function __construct()
    {
        parent::__construct();
    }
    public function setTable():string
    {
        return "users";
    }

    public function getT()
    {
        $query = 'Select * from users where email = :email';
        $arry['email'] = "ali";
        return $this->selectQuery($query,$arry);
    }

}