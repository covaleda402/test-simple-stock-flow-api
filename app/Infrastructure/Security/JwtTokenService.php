<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Application\Ports\Outbound\TokenGeneratorInterface;
use App\Domain\Entity\User;
use DateTimeImmutable;
use DateTimeZone;

final class JwtTokenService implements TokenGeneratorInterface
{
    private string $signingKey;
    private int $leewaySeconds;

    public function __construct(?string $signingKey = null, int $leewaySeconds = 30)
    {
        $resolvedKey = $signingKey ?? (string) env('JWT_SIGNING_KEY', '');
        if (trim($resolvedKey) === '') {
            throw new \RuntimeException('JWT_SIGNING_KEY environment variable is not configured.');
        }

        $this->signingKey = $resolvedKey;
        $this->leewaySeconds = $leewaySeconds;
    }

    public function generate(User $user, int $lifetimeMinutes = 60): array
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $expiresAt = $now->modify("+{$lifetimeMinutes} minutes");

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $payload = [
            'sub' => $user->id(),
            'unique_name' => $user->username(),
            'role' => $user->role(),
            'jti' => bin2hex(random_bytes(16)),
            'iat' => $now->getTimestamp(),
            'exp' => $expiresAt->getTimestamp(),
        ];

        $base64Header = self::base64UrlEncode((string) json_encode($header));
        $base64Payload = self::base64UrlEncode((string) json_encode($payload));

        $signature = hash_hmac('sha256', "{$base64Header}.{$base64Payload}", $this->signingKey, true);
        $base64Signature = self::base64UrlEncode($signature);

        $token = "{$base64Header}.{$base64Payload}.{$base64Signature}";

        return [
            'accessToken' => $token,
            'expiresAt' => $expiresAt->format('Y-m-d\TH:i:s.u\+00:00'),
            'username' => $user->username(),
            'role' => $user->role(),
        ];
    }

    public function validate(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $expectedSig = hash_hmac('sha256', "{$base64Header}.{$base64Payload}", $this->signingKey, true);
        $providedSig = self::base64UrlDecode($base64Signature);

        if (!hash_equals($expectedSig, $providedSig)) {
            return null;
        }

        $payloadJson = self::base64UrlDecode($base64Payload);
        $payload = json_decode($payloadJson, true);
        if (!is_array($payload)) {
            return null;
        }

        $now = time();
        $exp = $payload['exp'] ?? 0;
        // Leeway de 30 segundos según contrato (api-contract.md §1)
        if (($exp + $this->leewaySeconds) < $now) {
            return null;
        }

        return [
            'sub' => (string) ($payload['sub'] ?? ''),
            'unique_name' => (string) ($payload['unique_name'] ?? ''),
            'role' => (string) ($payload['role'] ?? ''),
        ];
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
