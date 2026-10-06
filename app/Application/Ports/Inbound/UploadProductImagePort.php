<?php

declare(strict_types=1);

namespace App\Application\Ports\Inbound;

interface UploadProductImagePort
{
    public function execute(string $productId, string $binaryData, string $mimeType): string;
}
