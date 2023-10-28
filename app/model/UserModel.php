<?php
namespace App\Model;
use App\Table\UserTable;
use GemLibrary\Helper\CryptoHelper;

class UserModel {
    public $error;
    public function __construct()
    {
    }

    public function create($username , $password)
    {
        $table = new UserTable();
        $table->email = $username;
        $table->password = CryptoHelper::hashPassword($password);
        if($table->insert())
        {
            return $table->lastInsertId();
        }
        $this->error = $table->getError();
        return false;
    }
}
