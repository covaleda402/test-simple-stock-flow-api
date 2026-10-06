<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface LoginPort
{
    /**
     * @param string $username
     * @param string $password
     * @return array{accessToken: string, expiresAt: string, username: string, role: string}
     */
    public function execute(string $username, string $password): array;
}
