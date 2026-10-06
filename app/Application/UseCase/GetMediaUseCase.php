<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\GetMediaPort;
use App\Application\Ports\Outbound\FileStorageInterface;

final class GetMediaUseCase implements GetMediaPort
{
    public function __construct(
        private FileStorageInterface $fileStorage
    ) {}

    public function execute(string $key): ?string
    {
        return $this->fileStorage->getPath($key);
    }
}
