<?php
namespace App\Core;

class Middleware{

    public static function requireAdmin(string $redirectUrl = '/' ): void
    {
        if (!Auth::isAdmin()) {
            header("Location: $redirectUrl");
            exit();
        }

    }
    public static function requireGuest(string $redirectUrl = '/' ): void
    {
        if (!Auth::isGuest()) {
            header("Location: $redirectUrl");
            exit();
        }
    }

    public static function requireUser(
        ?string $messageKey = null,
        ?string $message = null,
        string  $redirectUrl = '/login',
        string  $redirectUrlAfterlogin = '/'): void
    {
        if (!Auth::isUser()) {
            if ($messageKey && $message) {
                Flashmessages::setMessage($messageKey, $message);
            }
            $_SESSION['redirect_after_login'] = $redirectUrlAfterlogin;
            header("Location: $redirectUrl");
            exit();
        }
    }

    // Methode zur Validierung des CSRF-Tokens
    public static function validateCsrfToken(string $key='std', ?string $json_csrf_token = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$json_csrf_token) {
            if (!isset($_POST['csrf_token']) || !hash_equals(Csrf::getCsrfToken($key), $_POST['csrf_token'])) {
                Csrf::reGenerateCsrfToken($key);
                die('Ungültiger CSRF-Token. Anfrage verweigert.');
            }
        }
        elseif(!$json_csrf_token || !hash_equals(Csrf::getCsrfToken($key), $json_csrf_token)) {
            Csrf::reGenerateCsrfToken($key);
            die('Ungültiger CSRF-Token. Anfrage verweigert.');
        }
    }
}
?>
