<?php

namespace App\Router;

use App\Core\Container;
use App\Controller\HttpErrorController;
use App\Enum\HttpMethod;

class Router
{
    private static ?Router $instance = null;
    private Container $container;
    private $routes = [];

    // Der Konstruktor ist privat, damit keine neue Instanz direkt erstellt werden kann
    private function __construct(
        array $routes,
        Container $container
    )
    {
        $this->routes = $routes;
        $this->container = $container;
    }

    // Statische Methode, um die Instanz des Routers zu erhalten
    public static function getInstance(
        ?array $routes = null,
        ?Container $container = null
    )
    {
        if (self::$instance === null) {
            // Falls noch keine Instanz existiert, erstellen wir eine
            if ($routes === null) {
                throw new \Exception("Routes müssen übergeben werden.");
            }
            self::$instance = new self($routes, $container);
        }
        return self::$instance;
    }

    private function routeNotFound()
    {
        $this->container
            ->get(HttpErrorController::class)
            ->notFound();
            exit ;
    }

    public function resolve(string $request): void
    {
        $request = parse_url($request, PHP_URL_PATH);
        $request = rawurldecode($request);
        $request = rtrim($request, '/') ?: '/';
        // Statische Routen

        $route = $this->routes[$_SERVER['REQUEST_METHOD']][$request] ?? null;
        if (!$route ){
            // 404
          $this->routeNotFound();
        }
        [$containerClass, $action] = $route;
        $controller = $this->container->get($containerClass);
        $controller->$action();
        return;
    }
}
?>