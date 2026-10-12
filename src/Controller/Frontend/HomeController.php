<?php
namespace App\Controller\Frontend;

use App\Core\View;
use App\Service\ProductService;

class HomeController{

    public function __construct(
        private readonly View $view,
        private readonly ProductService $productService
    )
    {}

    public function index()
    {
        $products = $this->productService->getStartseiteProducts();
        return $this->view->render('home/index.html.twig',[
            'products' => $products
        ]);
    }
}