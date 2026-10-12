<?php
namespace App\Controller\Frontend;

use App\Service\ProductService;
use App\Core\View;

class ProductController
{

    private int $limit;
    public function __construct(
        private readonly View $view,
        private readonly ProductService $productService
    )
    {}

    public function search(string $search)
    {
        return $this->productService->getProductsBySearch($search);
    }

    public function show()
    {
        if (!isset($_GET['id']) || preg_match('#^d+$#', $_GET['id']))
        {
            header('Location: /');
            exit;
        }
        $id = (int)$_GET['id'];
        $product = $this->productService->getProductById($id);
        if (!$product) {
            header('Location: /');
            exit;
        }
        $this->view->render('product/show.html.twig',
            [
                'product' => $product
            ]
        );
    }

    public function  getPageProducts() {
        $page = max(1, intval($_GET['page'] ?? 0) ?: 1);
        $limit = max(16,intval($_GET['limit'] ?? 0) ?: 16);
        $products = $this->productService->getPageProducts($page,$limit);

        $productCount = $this->productService->getProductCount();
        $pageCount = (int) ceil($productCount/$limit);
        if ($page > $pageCount)
        {
            return;
        }
        return $this->view->render('product/index.html.twig',[
            'products' => $products,
            'pageCount' => $pageCount,
            'page'=>$page,
            'query' => $_GET,
        ]);
    }
}
?>