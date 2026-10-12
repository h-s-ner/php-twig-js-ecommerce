<?php

declare(strict_types=1);

namespace App\Entity;

class Product
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly float $price,
        private readonly int $stock,
        private readonly bool $active,
        private readonly ?string $description = null,
        private readonly ?int $categoryId = null
    )
    {}

    public static function fromDatabase(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            price: (float) $row['price'],
            stock: (int) $row['stock'],
            active: (bool) $row['active'],
            description: $row['description'] ?? null,
            categoryId: isset($row['category_id'])
                ? (int) $row['category_id']
                : null,
        );
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }
}
