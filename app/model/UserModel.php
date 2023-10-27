<?php
namespace App\Model;
use App\Table\UserTable;
class UserModel {
    protected UserTable $userTable;

    public function __construct()
    {
        $this->userTable = new UserTable();
    }

    public function sdfsdf()
    {
        $user = new UserTable();
        if($user->id(9))
        {
            if($user->deactivate())
            {
                echo $user->getError();
            }
            if()
        }
    }





}
