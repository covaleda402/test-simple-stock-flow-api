<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entity\User;

interface TokenGeneratorInterface
{
    /**
     * @param User $user
     * @param int $lifetimeMinutes
     * @return array{accessToken: string, expiresAt: string, username: string, role: string}
     */
    public function generate(User $user, int $lifetimeMinutes = 60): array;

    /**
     * @param string $token
     * @return array{sub: string, unique_name: string, role: string}|null
     */
    public function validate(string $token): ?array;
}
