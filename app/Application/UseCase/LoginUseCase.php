<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\LoginPort;
use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\TokenGeneratorInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Domain\Entity\User;
use App\Domain\Exception\BusinessRuleValidationException;

final class LoginUseCase implements LoginPort
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private TokenGeneratorInterface $tokenGenerator
    ) {}

    public function execute(string $username, string $password): array
    {
        $normalizedUsername = User::normalizeUsername($username);
        $user = $this->userRepository->findByUsername($normalizedUsername);

        if ($user === null || !$this->passwordHasher->verify($password, $user->passwordHash())) {
            throw new BusinessRuleValidationException("Usuario o contraseña incorrectos.");
        }

        return $this->tokenGenerator->generate($user, 60);
    }
}
