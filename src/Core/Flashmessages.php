<?php
namespace App\Core;

class Flashmessages
{
    public static function has_messages(string $key): bool
    {
        if (isset($_SESSION[$key]['messages'])
            && count($_SESSION[$key]['messages']) > 0) {
            return true;
        }
        return false;
    }

    public static function setMessage(string $key, string $message): void
    {
        $_SESSION[$key]['messages'][] = $message;
    }

    public static function getMessages(string $key): ?array
    {
        if (self::has_messages($key)) {
            $messages = $_SESSION[$key]['messages'];
            unset($_SESSION[$key]['messages']);
            return $messages;
        } else {
            return null;
        }
    }
}