<?php

namespace App\Repository;

use App\DB\Database;
use App\Entity\Product;
use \PDO;

class ProductRepository extends Database
{

        /**
         * @return Product[]
         */
        public function findAllAvailable(): array
        {
                $sql = "SELECT * FROM products WHERE active = 1 and stock > 0";
                $ps = $this->conn->prepare($sql);
                $ps->execute();
                $rows = $ps->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }

        public function findAvailableById(int $id): ?Product
        {
                $sql = "SELECT * FROM products
                        WHERE id = :id AND active = 1 AND stock > 0";
                $ps = $this->conn->prepare($sql);
                $ps->bindValue(':id', $id, PDO::PARAM_INT);
                $ps->execute();
                $result = $ps->fetch();
                return $result ? Product::fromDatabase($result) : null;
        }

        /**
         * @return Product[]
         */
        public function findAll(): array
        {
                $sql = "SELECT * FROM products";
                $ps = $this->conn->prepare($sql);
                $ps->execute();
                //fetchAll gibt immer ein Array zurück
                //Wenn keine Daten → leeres Array []
                $rows = $ps->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }

        public function findById(int $id): ?Product
        {
                $sql = "SELECT * FROM products WHERE id = :id";
                $ps = $this->conn->prepare($sql);
                $ps->bindValue(':id', $id, PDO::PARAM_INT);
                $ps->execute();
                // Fetch gibt einzelnen Datensatz zurück
                // wenn nicht gefunden → false
                $result = $ps->fetch();
                return $result ? Product::fromDatabase($result) : null;
        }

        public function getProductStockById(int $product_id): int
        {

                $sql = "SELECT stock FROM products WHERE id = :id";
                $ps = $this->conn->prepare($sql);
                $ps->bindValue(':id', $product_id, PDO::PARAM_INT);
                $ps->execute();
                return (int) $ps->fetchColumn();
        }

        public function destroy(int $id): bool
        {
                $sql = "DELETE FROM products WHERE id = :id";
                $ps = $this->conn->prepare($sql);
                $ps->bindValue(':id', $id, PDO::PARAM_INT);
                return $ps->execute();
        }

        public function store(
                string $name,
                float $price,
                string $description,
                int $stock,
                int $active,
                string $image
        ): int {
                $sql = "INSERT INTO products (name, price, stock, description, active, image,created_at)
                        VALUES (:name, :price, :stock, :description, :active, :image,NOW())";
                $ps = $this->conn->prepare($sql);
                $ps->execute([
                        ':name' => $name,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':description' => $description,
                        ':active' => $active,
                        ':image' => $image
                ]);
                return (int) $this->conn->lastInsertId();
        }

        public function update(
                int $id,
                string $name,
                float $price,
                string $description,
                int $stock,
                int $active,
                int $category_id
        ): bool {

                $sql = "UPDATE products SET
                name = :name,
                price = :price,
                stock = :stock,
                description = :description,
                active = :active.
                category_id = :category_id
                WHERE id = :id";

                $ps = $this->conn->prepare($sql);

                return $ps->execute([
                        ':id' => $id,
                        ':name' => $name,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':description' => $description,
                        ':active' => $active,
                        ':category_id' => $category_id
                ]);
        }

        public function updateImage($id, $image): bool
        {
                $sql = "UPDATE products SET image = :image WHERE id = :id";
                $ps = $this->conn->prepare($sql);
                return $ps->execute([
                        ':id' => $id,
                        ':image' => $image
                ]);
        }

        public function updateStock($id, $stock): bool
        {
                $sql = "UPDATE products SET stock = :stock WHERE id = :id";
                $ps = $this->conn->prepare($sql);

                return $ps->execute([
                        ':id' => $id,
                        ':stock' => $stock
                ]);
        }

        public function getStartseiteProducts()
        {
                $sql = "SELECT *
                        FROM products
                        where active=1
                           and stock > 0
                           and created_at >= DATE_SUB(CURDATE(), INTERVAL 2 Month)
                           ORDER BY created_at DESC";
                $ps = $this->conn->prepare($sql);
                $ps->execute();
                $rows = $ps->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }
        public function getPageAvailableProducts(int $page, int $limit)
        {

                $sql = "SELECT *
                        FROM products
                        where active=1
                           and stock > 0
                        ORDER BY id ASC
                        LIMIT :limit OFFSET :offset";
                $ps = $this->conn->prepare($sql);
                $ps->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $ps->bindValue(':offset', (int)(($page - 1) * $limit), PDO::PARAM_INT);
                $ps->execute();
                $rows = $ps->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }
        public function getProductsBySearch(string $search)
        {
                $sql = "SELECT * FROM products p
                        WHERE p.name LIKE :search
                        OR p.brand_name LIKE :search
                        OR p.description LIKE :search
                        ORDER BY p.name, p.description
                ";
                $ps = $this->conn->prepare($sql);
                $ps->execute([
                        ':search' => '%' . $search . '%'
                ]);
                $rows = $ps->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }

        public function getProductCount(): int
        {

                $sql = "SELECT count(*) FROM products WHERE active=1 and stock > 0";
                $ps = $this->conn->prepare($sql);
                $ps->execute();
                return (int) $ps->fetchColumn();
        }

        public function getProductsByCategory(string $category)
        {
                $sql =  "SELECT p.* FROM products p
                        JOIN categories c ON p.category_id = c.id
                        WHERE c.name = :category AND p.stock > 0";
                $ps = $this->conn->prepare($sql);
                $ps->execute([
                        ':category' => $category,
                ]);
                $result = $ps->fetchAll();
                return $result ?: null;
        }

        public function getProductsOfOfferByCategory()
        {
                $sql = "SELECT  p.*, o.discount, ROUND(p.price * (100 - o.discount) / 100, 2) AS offer_price
                FROM products p
                JOIN orffers_by_category o
                ON p.category_id = o.category_id
                WHERE NOW() BETWEEN o.begin AND o.end";
                $ps = $this->conn->prepare($sql);
                $ps->execute();
                //fetchAll gibt immer ein Array zurück
                //Wenn keine Daten → leeres Array []
                return $ps->fetchAll();
        }
}
