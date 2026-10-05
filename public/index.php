<?php
$cookieParams = [
    'lifetime' => 0,                  // Cookie bis Browser geschlossen
    'path' => '/',                     // Gültig für die ganze Website
    'domain' => '',                    // leer = aktueller Host, gut für localhost
    'secure' => isset($_SERVER['HTTPS']), // nur HTTPS, passt automatisch
    'httponly' => true,                // kein Zugriff per JavaScript
    'samesite' => 'Strict'             // schützt vor CSRF
];
session_set_cookie_params($cookieParams);
session_start();

// Autoloading und Composer laden
require_once __DIR__ . '/../vendor/autoload.php';

echo 'Start Seite';