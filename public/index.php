<?php
// Autoloading und Composer laden
require_once __DIR__ . '/../vendor/autoload.php';

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

use App\Core\Container;
use App\Router\Router;
use App\Router\RouteConfig;
$rotueConfig = new RouteConfig();
$container = new Container;
$router = Router::getInstance($rotueConfig->getRoutes(),$container);

// URL der Anfrage
$request = $_SERVER['REQUEST_URI'];

// Routing aufrufen
$router->resolve($request);