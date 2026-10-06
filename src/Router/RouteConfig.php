<?php
namespace App\Router;

use App\Controller\HomeController;
use App\Enum\HttpMethod;

class RouteConfig
{
    private array $routes=[];
    public function __construct()
    {
        $this->register();

    }
    private function addRoute(string $path,  HttpMethod $method, array $handler){
        $this->routes[$method->value][$path] = $handler;
    }
    private function register(): void
    {
        $this->addRoute('/', HttpMethod::GET, [HomeController::class, 'index']);
    }
    public function getRoutes() : array
    {
        return $this->routes;
    }
}
?>