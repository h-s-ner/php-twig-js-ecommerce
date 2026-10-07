<?php
namespace App\Core;

class Csrf{
    private static function generateCsrfToken($key='std'): void
    {
        if (empty($_SESSION[$key]['csrf_token'])){
            $_SESSION[$key]['csrf_token'] = bin2hex(random_bytes(32)); // Token generieren
        }
    }

    public static function reGenerateCsrfToken($key='std'): void
    {
        $_SESSION[$key]['csrf_token'] = bin2hex(random_bytes(32)); // Token regenerieren
    }

    public static function getCsrfToken($key='std'): string
    {
        self::generateCsrfToken($key);
        return $_SESSION[$key]['csrf_token'];
    }

    public static function getNewCsrfToken($key='std'): string
    {
        self::reGenerateCsrfToken($key);
        return $_SESSION[$key]['csrf_token'];
    }
}