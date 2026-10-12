<?php
namespace App\Repository;

use App\DB\Database;
use App\Entity\Product;
use \PDO;
class ProductRepository extends DataBase {

        /**
         * @return Product[]
         */
        public function findAllAvailable(): array
        {
                $sql = "SELECT * FROM products WHERE active = 1 and stock > 0";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }

        public function findAvailableById(int $id): ?Product
        {
                $sql = "SELECT * FROM products
                        WHERE id = :id AND active = 1 AND stock > 0";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch();
                return $result ? Product::fromDatabase($result) : null;
        }

        /**
         * @return Product[]
         */
        public function findAll(): array
        {
                $sql = "SELECT * FROM products";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                //fetchAll gibt immer ein Array zurück
                //Wenn keine Daten → leeres Array []
                $rows = $stmt->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }

        public function findById(int $id): ?Product
        {
                $sql = "SELECT * FROM products WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                // Fetch gibt einzelnen Datensatz zurück
                // wenn nicht gefunden → false
                $result = $stmt->fetch();
                return $result ? Product::fromDatabase($result) : null;
        }

        public function getProductStockById(int $product_id): int
        {

                $sql = "SELECT stock FROM products WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
                $stmt->execute();
                return (int) $stmt->fetchColumn();
        }

        public function destroy(int $id): bool
        {
                $sql = "DELETE FROM products WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                return $stmt->execute();
        }

        public function store(
                string $name,
                float $price,
                string $description,
                int $stock,
                int $active,
                string $image
        ): int
        {
                $sql = "INSERT INTO products (name, price, stock, description, active, image,created_at)
                        VALUES (:name, :price, :stock, :description, :active, :image,NOW())";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                        ':name' => $name,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':description' => $description,
                        ':active' => $active,
                        ':image' => $image
                ]);
                return (int) $this->conn->lastInsertId();
        }

        public function update($id, $name, $price, $description, $stock, $active, $category_id): bool
        {

                $sql = "UPDATE products SET
                name = :name,
                price = :price,
                stock = :stock,
                description = :description,
                active = :active.
                category_id = :category_id
                WHERE id = :id";

                $stmt = $this->conn->prepare($sql);

                return $stmt->execute([
                        ':id' => $id,
                        ':name' => $name,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':description' => $description,
                        ':active' => $active,
                        ':category_id'=>$category_id
                ]);

        }

        public function updateImage($id, $image): bool
        {
                $sql = "UPDATE products SET image = :image WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([
                        ':id' => $id,
                        ':image' => $image
                ]);
        }

        public function updateStock($id, $stock): bool
        {
                $sql = "UPDATE products SET stock = :stock WHERE id = :id";
                $stmt = $this->conn->prepare($sql);

                return $stmt->execute([
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
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                $rows = $stmt->fetchAll();
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
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int)(($page-1)*$limit), PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();
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
                        ORDER BY p.name
                ";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                        ':search'=>'%'.$search.'%'
                ]);
                $rows = $stmt->fetchAll();
                return array_map(
                        fn($row): Product => Product::fromDatabase($row),
                        $rows
                );
        }

        public function getProductCount(): int
        {

                $sql = "SELECT count(*) FROM products WHERE active=1 and stock > 0";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                return (int) $stmt->fetchColumn();
        }

        public function getProductsByCategory(string $category)
        {
                $sql =  "SELECT p.* FROM products p
                        JOIN categories c ON p.category_id = c.id
                        WHERE c.name = :category AND p.stock > 0";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                        ':category' => $category,
                ]);
                $result = $stmt->fetchAll();
                return $result ?: null;
        }

        public function getProductsOfOfferByCategory() {
                $sql = "SELECT  p.*, o.discount, ROUND(p.price * (100 - o.discount) / 100, 2) AS offer_price
                FROM products p
                JOIN orffers_by_category o
                ON p.category_id = o.category_id
                WHERE NOW() BETWEEN o.begin AND o.end";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                //fetchAll gibt immer ein Array zurück
                //Wenn keine Daten → leeres Array []
                return $stmt->fetchAll();

        }
}