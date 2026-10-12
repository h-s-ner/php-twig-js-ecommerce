<?php
namespace App\Service;
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductService{

    public function __construct(protected readonly ProductRepository $productRepository)
    {}

    public function getProductById(int $id): ?Product
    {
       return $this->productRepository->findAvailableById($id);
    }
    public function getProductsByCategory(string $category)
    {
        return $this->productRepository->getProductsByCategory($category);
    }
    public function getProductCount()
    {
        return $this->productRepository->getProductCount();
    }
    public function getPageProducts(int $page, int $limit)
    {
        return $this->productRepository->getPageAvailableProducts($page,$limit);
    }
    public function getProductsBySearch(string $search)
    {
        return $this->productRepository->getProductsBySearch($search);
    }
    public function getStartseiteProducts()
    {
        return $this->productRepository->getStartseiteProducts();
    }
}
?>