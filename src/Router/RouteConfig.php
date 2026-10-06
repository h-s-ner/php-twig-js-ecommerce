<?php
namespace App\Router;

use App\Controller\HomeController;
use App\Controller\AuthController;
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
        $this->addRoute('/login',HttpMethod::GET,[AuthController::class, 'loginIndex']);
        $this->addRoute('/login',HttpMethod::POST,[AuthController::class, 'login']);
        $this->addRoute('/logout',HttpMethod::POST,[AuthController::class,'logout']);
        $this->addRoute('/register',HttpMethod::GET,[AuthController::class, 'registerIndex']);
        $this->addRoute('/register',HttpMethod::POST,[AuthController::class, 'register']);
    }
    public function getRoutes() : array
    {
        return $this->routes;
    }
}
?>