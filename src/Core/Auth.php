<?php
namespace App\Core;

use App\Enum\Role;

class Auth{

    public static function isGuest():bool{
        return (!isset($_SESSION['user']));
    }
    public static function isUser():bool{
        return (isset($_SESSION['user']));
    }
    public static function isAdmin():bool{
     return (self::isUser() && ($_SESSION['user']->getRole() == Role::ADMIN));
    }
    public static function getRole() : Role
    {
        if(self::isGuest())
        {
            return Role::GUEST;
        }
        return $_SESSION['user']->getRole();
    }

    public static function getCurrentUserId() :?int
    {
        return self::isUser() ? $_SESSION['user']->getId() : null;
    }
}