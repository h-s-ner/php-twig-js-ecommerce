<?php
namespace App\Router;

use App\Controller\Frontend\HomeController;
use App\Controller\Frontend\AuthController;
use App\Controller\Frontend\ProductController;
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
        $this->addRoute('/products',HttpMethod::GET,[ProductController::class, 'getPageProducts']);
        $this->addRoute('/show',HttpMethod::GET,[ProductController::class, 'show']);
    }
    public function getRoutes() : array
    {
        return $this->routes;
    }
}
?>