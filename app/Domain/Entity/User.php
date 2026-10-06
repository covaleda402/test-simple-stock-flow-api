<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\BusinessRuleValidationException;

final class User
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SELLER = 'seller';

    private string $id;
    private string $username;
    private string $passwordHash;
    private string $role;

    public function __construct(string $id, string $username, string $passwordHash, string $role)
    {
        $normalizedUsername = self::normalizeUsername($username);
        if ($normalizedUsername === '') {
            throw new BusinessRuleValidationException("El nombre de usuario es obligatorio.");
        }

        if (trim($passwordHash) === '') {
            throw new BusinessRuleValidationException("El hash de contraseña es obligatorio.");
        }

        if (!in_array($role, [self::ROLE_ADMIN, self::ROLE_SELLER], true)) {
            throw new BusinessRuleValidationException("Rol no válido: '{$role}'.");
        }

        $this->id = $id;
        $this->username = $normalizedUsername;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
    }

    public static function normalizeUsername(string $username): string
    {
        return mb_strtolower(trim($username), 'UTF-8');
    }

    public function id(): string
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }
}
