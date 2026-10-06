<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Role;

class User
{
    public function __construct(
        private ?int $id,
        private string $fname,
        private string $lname,
        private string $email,
        private ?string $phone,
        private bool $active,
        private Role $role,
        private string $password,
    )
    {}

    public static function fromDatabase(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            fname: $row['fname'],
            lname: $row['lname'],
            email: $row['email'],
            phone: $row['phone'] ?? null,
            active: (bool) $row['active'],
            role: Role::from($row['role']),
            password: $row['password'],
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFname(): string
    {
        return $this->fname;
    }

    public function getLname(): string
    {
        return $this->lname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function changePassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }
}
