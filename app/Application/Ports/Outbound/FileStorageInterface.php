<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

interface FileStorageInterface
{
    /**
     * @param string $binaryData
     * @param string $mimeType
     * @return string opaque key generated (uuid4 hex + extension)
     */
    public function store(string $binaryData, string $mimeType): string;

    public function exists(string $key): bool;

    public function getPath(string $key): ?string;

    public function delete(string $key): void;
}
