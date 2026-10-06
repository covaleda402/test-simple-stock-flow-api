<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\RegisterSellerPort;
use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entity\User;
use App\Domain\Exception\BusinessRuleValidationException;

final class RegisterSellerUseCase implements RegisterSellerPort
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {}

    public function execute(string $username, string $password, string $role): string
    {
        // 1. role es "admin" (DP-04)
        if ($role === 'admin') {
            throw new BusinessRuleValidationException(
                "Solo se pueden dar de alta vendedores. El administrador lo crea el despliegue."
            );
        }

        $normalizedUsername = User::normalizeUsername($username);

        // 2. El usuario ya existe
        if ($this->userRepository->existsByUsername($normalizedUsername)) {
            throw new BusinessRuleValidationException("El usuario '{$normalizedUsername}' ya existe.");
        }

        // 3. role fuera del conjunto
        if ($role !== User::ROLE_SELLER) {
            throw new BusinessRuleValidationException("Rol no válido: '{$role}'.");
        }

        $userId = self::generateUuidV4();
        $passwordHash = $this->passwordHasher->hash($password);

        $user = new User($userId, $normalizedUsername, $passwordHash, $role);
        $this->userRepository->save($user);

        return $user->id();
    }

    private static function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
