<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Ports\Outbound\PasswordHasherInterface;

final class Argon2PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $plainPassword): string
    {
        // En PHP estándar PASSWORD_ARGON2ID está disponible si el binario lo soporta, o fallback a PASSWORD_BCRYPT/DEFAULT
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

        return password_hash($plainPassword, $algo);
    }

    public function verify(string $plainPassword, string $hash): bool
    {
        return password_verify($plainPassword, $hash);
    }
}
