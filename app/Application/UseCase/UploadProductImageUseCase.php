<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Ports\Inbound\UploadProductImagePort;
use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Exception\BusinessRuleValidationException;

final class UploadProductImageUseCase implements UploadProductImagePort
{
    private const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5 MB
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private FileStorageInterface $fileStorage
    ) {}

    public function execute(string $productId, string $binaryData, string $mimeType): string
    {
        $product = $this->productRepository->findById($productId);
        if ($product === null) {
            throw new BusinessRuleValidationException("El producto {$productId} no existe.");
        }

        if (strlen($binaryData) > self::MAX_SIZE_BYTES) {
            throw new BusinessRuleValidationException("La imagen supera el máximo de 5 MB.");
        }

        if (!in_array(strtolower($mimeType), self::ALLOWED_MIME_TYPES, true)) {
            throw new BusinessRuleValidationException("Tipo de archivo no permitido: {$mimeType}.");
        }

        $oldKey = $product->imageKey();

        $key = $this->fileStorage->store($binaryData, $mimeType);
        $product->attachImage($key);
        $this->productRepository->save($product);

        if ($oldKey !== null) {
            $this->fileStorage->delete($oldKey);
        }

        return '/media/' . $key;
    }
}
