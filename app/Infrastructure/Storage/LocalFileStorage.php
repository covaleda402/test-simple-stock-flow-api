<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Ports\Outbound\FileStorageInterface;

final class LocalFileStorage implements FileStorageInterface
{
    private string $mediaPath;

    public function __construct(?string $mediaPath = null)
    {
        $this->mediaPath = $mediaPath ?? storage_path('app/public/media');
        if (!is_dir($this->mediaPath)) {
            mkdir($this->mediaPath, 0755, true);
        }
    }

    public function store(string $binaryData, string $mimeType): string
    {
        $ext = match (strtolower($mimeType)) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'bin',
        };

        $hex = bin2hex(random_bytes(16));
        $key = "{$hex}.{$ext}";

        $targetPath = $this->mediaPath . DIRECTORY_SEPARATOR . $key;
        file_put_contents($targetPath, $binaryData);

        return $key;
    }

    public function exists(string $key): bool
    {
        return file_exists($this->mediaPath . DIRECTORY_SEPARATOR . $key);
    }

    public function getPath(string $key): ?string
    {
        $path = $this->mediaPath . DIRECTORY_SEPARATOR . $key;

        return file_exists($path) ? $path : null;
    }

    public function delete(string $key): void
    {
        $path = $this->mediaPath . DIRECTORY_SEPARATOR . $key;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
